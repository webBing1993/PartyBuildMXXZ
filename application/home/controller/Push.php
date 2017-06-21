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
use app\home\model\News as NewsModel;
use app\home\model\Learn as LearnModel;
use app\home\model\Pioneer as PioneerModel;
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
        $list = $this ->pushList();
        //没有需要推送的消息,就只推每日一课
        if(empty($list)){
//            $info['media_id'] = '65s5Dd8jcGNvhC9izswZtFEHs-SRAE-JxAqQCoP7g_g';
        }else{
//            //先上传素材 media_id
            foreach($list as $k => $v){
                //class 1红色足迹  2两学一做 3先锋事迹
                $class = $v['class'];
                if($class == 1 ||
                   $class == 2 ){
                    $data = array(
                        "media" => '@.'.$v['img']
                    );
                    $img = $Wechat ->uploadForeverMedia($data,'thumb');
                    $v['thumb_media_id'] = $img['media_id'];
                    $id = $v['id'];
                    if($class == 1)
                    {
                        $link = 'details/index';
                    } else if($class == 2){
                        $link = 'learn/article';
                    }else if($class == 3){
                        $link = 'pioneer/detail';
                    }
                    $v['content_source_url'] = "$request/home/$link/id/$id";
                }
            }
            //图文素材列表
            $article = array();
            foreach ($list as $k =>$v ){
                //class 1新闻
                if($v['class'] == 1 ||
                   $v['class'] == 2 ||
                   $v['class'] == 3 ) {
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
                $pioneer = new PioneerModel();
                if (!empty($info['media_id']))
                {
                    //class 1新闻 2两学一做
                    foreach ($list as $k => $v)
                    {
                        if($v['class'] == 1)
                        {
                            $news ->where('id',$v['id']) ->update(['status' => 1]);//1为已推送
                        }else if($v['class'] == 2){
                            $learn ->where('id',$v['id']) ->update(['status' => 1]);//1为已推送
                        }else if($v['class'] == 3){
                            $pioneer ->where('id',$v['id']) ->update(['status' => 1]);//1为已推送
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
     * 获取待推送的8条数据
     * @return array
     */
    public function pushList(){
        $count = 8; //总数据数量
        $count1 = 0; //从第几条开始取数据
        $count2 = 0;
        $count3 = 0;
        $news = new NewsModel();
        $learn = new LearnModel();
        $pioneer = new PioneerModel();
        $news_check = false; //新闻数据状态 true为取空
        $learn_check = false;
        $pioneer_check = false;
        $all_list = array();
        //获取数据  取满8条 或者取不出数据退出循环
        while(true)
        {
            if(!$news_check &&
                count($all_list) < $count)
            {
                $res = $news ->getDataList($count1,1); //获取一条数据
                if(empty($res))
                {
                    $news_check = true;
                }else {
                    $count1 ++;
                    $all_list = $this ->changeTpye($all_list,$res,1); //给每条数据增加类别判断
                }
            }
            if(!$learn_check &&
                count($all_list) < $count)
            {
                $res = $learn ->getDataList($count2,1);
                if(empty($res))
                {
                    $learn_check = true;
                }else {
                    $count2 ++;
                    $all_list = $this ->changeTpye($all_list,$res,2);
                }
            }

            if(!$pioneer_check &&
                count($all_list) < $count)
            {
                $res = $pioneer ->getDataList($count3,1);
                if(empty($res))
                {
                    $pioneer_check = true;
                }else {
                    $count3 ++;
                    $all_list = $this ->changeTpye($all_list,$res,3);
                }
            }

            if(count($all_list) >= $count ||
                ($news_check && $learn_check && $pioneer_check))
            {
                break;
            }
        }
        return $all_list;
    }
    /**
     * 进行数据区分
     * @param $list
     * @param $type 1新闻  2两学一做 3通知
     */
    private function changeTpye($all,$list,$type){
        //图片进行转化
        $img = Picture::get($list['front_cover']);
        $list['img'] = $img['path'];
        //增加类别
        $list['class'] = $type;
        array_push($all,$list);
        return $all;
    }
}