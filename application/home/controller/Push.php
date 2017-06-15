<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/8
 * Time: 15:28
 */
namespace app\home\controller;
use think\Controller;
use com\wechat\TPWechat;
use think\Config;
use app\home\model\News;
use app\home\model\Learn;
use app\home\model\Picture;

class Push extends Controller{
    /**
     * 订阅号每日定时推送
     */
    public function cron(){
        $Wechat = new TPWechat(Config::get('party'));
        $author = '之图红盟';//推送作者
        $request = 'http://zthm.0571ztnet.com';//域名
        $answer = '/home/constitution/course';//每日一课路径
        //每日一课的图片ID
        $image_id = "65s5Dd8jcGNvhC9izswZtFKnYGIP_Wp9OT1-3zUmyuQ";
        $openid = 'ome1gxJkdYt9Ji1LZjvl4d2d-6Fk';//王志超
        //获取需要推送的数据
        $list = $this ->pushlist();
        //没有需要推送的消息,就只推每日一课
        if(empty($list)){
//            $info['media_id'] = '65s5Dd8jcGNvhC9izswZtFEHs-SRAE-JxAqQCoP7g_g';
        }else{
//            //先上传素材 media_id
            foreach($list as $k => $v){
                //class 1新闻 2两学一做
                $class = $v['class'];
                if($class == 1 ||
                   $class == 2 ){
                    $data = array(
                        "media" => '@.'.$v['img']
                    );
                    $img = $Wechat ->uploadForeverMedia($data,'thumb');
                    $v['thumb_media_id'] = $img['media_id'];
                    $id = $v['id'];
                    if($class == 1){$link = 'details/index';}
                    else if($class == 2){$link = 'learn/article';}
                    $v['content_source_url'] = "$request/home/$link/id/$id";
                }
            }
            //图文素材列表
            $article = array();
            foreach ($list as $k =>$v ){
                //class 1新闻
                if($v['class'] == 1 ||
                   $v['class'] == 2) {
                    $article['articles'][$k] = [
                        'thumb_media_id' => $v['thumb_media_id'],
                        'author' => $v['publisher'],
                        'title' => $v['title'],
                        'content_source_url' => $v['content_source_url'],
                        "content" => $v['content'],
                        "digest" => $v['title'],
                        "show_cover_pic" => 0,
                    ];
                }
            }
            //最后一条加入每日一课
//            $article['articles'][count($article['articles'])] = [
//                    'thumb_media_id' => $image_id,
//                    'author' => $author,
//                    'title' =>'每日一课',
//                    'content_source_url' => "$request.$answer",
//                    "content" => "每日一课已经等候你多时了,点阅读全文开始答题!",
//                    "digest" => "休息一下,去答一下题吧",
//                    "show_cover_pic" => 1,
//                ];
            $lists =  $article;
            //上传多条图文素材
            $info = $Wechat ->uploadForeverArticles($lists);
            //消息群发
            $send = [
                "filter" => [
                    "is_to_all" =>true
                ],
                "mpnews" =>[
                    "media_id" => $info['media_id']
                ],
                "msgtype" => "mpnews",
                "send_ignore_reprint" => 0
            ];
            $res = $Wechat ->sendGroupMassMessage($send);
            //发送成功 修改对应数据状态
            if($res['errcode'] == 0){
                $news = new News();
                $learn = new Learn();
                if (!empty($info['media_id'])){
                    //class 1新闻 2两学一做
                    foreach ($list as $k => $v){
                        if($v['class'] == 1){
                            $news ->where('id',$v['id']) ->update(['status' => 1]);//1为已推送
                        }else if($v['class'] == 2){
                            $learn ->where('id',$v['id']) ->update(['status' => 1]);//1为已推送
                        }
                    }
                }
            }
        }
        //预览图文通知
//        $notice = array(
//            "touser" => $openid,
//            "mpnews" =>[
//                "media_id" => $info['media_id']
//            ],
//            "msgtype" => "mpnews"
//        );
//        $info = $Wechat ->previewMassMessage($notice);
//        dump( $info);
//        //上传图片获得缩略图片的media_id
//        $file_path = './home/images/constitution/3.png';
//        $data = array(
//            "media" => '@'.$file_path
//        );
//        $info = $Wechat ->uploadForeverMedia($data,'thumb');
        

//        $info = $Wechat ->getUserInfo("ome1gxJkdYt9Ji1LZjvl4d2d-6Fk");
//        dump($info);
//        //上传图文消息素材
//        $article = array(
//            "articles" => [
//                [
//                    'thumb_media_id' => $image_id,
//                    'author' => $author,
//                    'title' =>'每日一课',
//                    'content_source_url' => "$request.$answer",
//                    "content" => "每日一课已经等候你多时了,点阅读全文开始答题!",
//                    "digest" => "休息一下,去答一下题吧",
//                    "show_cover_pic" => 1,
//                ]
//            ]
//        );
//        $info = $Wechat ->uploadForeverArticles($article);
//
//        $answer_id = '65s5Dd8jcGNvhC9izswZtFEHs-SRAE-JxAqQCoP7g_g';//每日一课素材id
////        //预览图文通知
//        $notice = array(
//            "touser" => $openid,
//            "mpnews" =>[
//                "media_id" => $answer_id
//            ],
//            "msgtype" => "mpnews"
//        );
//        $info = $Wechat ->previewMassMessage($notice);
//        dump($info);
    }

    /**
     * 每月一课推送
     */
    public function everyMonth(){

    }
    /**
     * 待推送的列表
     */
    public function pushlist(){
        //获取未推送的前7条数据
        $news =new News();
        $learn = new Learn();
        //获取未推送的前7条数据
        $map = array('status' => 0,'push' => 1);
        $order = 'create_time desc';
        $list = $news ->where($map) ->order($order) ->limit(8) ->select();
        $all_list = array();
        foreach ($list as $k => $v){
            //图片地址转化
            $img = Picture::get($v['front_cover']);
            //class 1新闻
            $temp = array(
                'id' => $v['id'],
                'publisher' => $v['publisher'],
                'title' => $v['title'],
                'content' => $v['content'],
                'img' => $img['path'],
                'class' => 1
            );
            //压入数据
            array_push($all_list,$temp);
        }
        $count = count($all_list);
        if($count < 8){
            $limit = 8 - $count;
            $map = array('status' => 0,'push' => 1,'type' => 2 );
            $list = $learn ->where($map) ->order($order) ->limit($limit) ->select();
            foreach ($list as $k => $v){
                //图片地址转化
                $img = Picture::get($v['front_cover']);
                //class 1新闻
                $temp = array(
                    'id' => $v['id'],
                    'publisher' => $v['publisher'],
                    'title' => $v['title'],
                    'content' => $v['content'],
                    'img' => $img['path'],
                    'class' => 2
                );
                //压入数据
                array_push($all_list,$temp);
            }
        }
        return $all_list;
    }
}