<?php
/**
 * Created by PhpStorm.
 * User: ali
 * Date: 2017/12/8
 * Time: 16:55
 */
namespace  app\admin\controller;
use app\admin\model\Dreamflash;
use app\admin\model\Learn;
use app\admin\model\Notice;
use app\admin\model\Wish;
use app\admin\model\Matter;
use app\admin\model\Special;
use app\admin\model\Picture;
use app\admin\model\Push as PushModel;
use com\wechat\TPWechat;//微信接口
use think\Config;
/*
 * 推送
 * */
class Push extends Admin
{
    /*
     * 推送列表
     * */
    public function index()
    {
        if (IS_POST) {
            $id    = input('id');
            //整合成数组
            $idArr = explode('-', $id);
            $t     = $this->week_time();//判断是否为这周的信息
            $info  = array(
                'create_time' => array('egt', $t),
                'status'      => 0,
                'push'        => 1
            );
            //获取推送数据
            $infoArr   = [];

            $wish    = new Wish();//活动发起
            $wishArr = $wish->push($info, $idArr);
            foreach ($wishArr as $v) {
                $infoArr[] = $v;
            }
            $dreamflash = new Dreamflash();//梦想快讯
            $dreamflashArr   = $dreamflash->push($info, $idArr);//在model层封装
            foreach ($dreamflashArr as $v) {//合成一个数组,方便前端取值
                $infoArr[] = $v;
            }
            $notice    = new Notice();//通知公告
            $noticeArr = $notice->push($info, $idArr);
            foreach ($noticeArr as $v) {
                $infoArr[] = $v;
            }
            $matter    = new Matter();//重要文件
            $matterArr = $matter->push($info, $idArr);
            foreach ($matterArr as $v) {
                $infoArr[] = $v;
            }
            $learn     = new Learn();//两学一做
            $learnArr  = $learn->push($info, $idArr);
            foreach ($learnArr as $v) {
                $infoArr[] = $v;
            }
            $special     = new Special();//十九大专题
            $specialArr  = $special->push($info, $idArr);
            foreach ($specialArr as $v) {
                $infoArr[] = $v;
            }
            return $this->success($infoArr);
        } else {
            $map = array(
                'status' => array('egt', -1)
            );
            $list = $this->lists('Push', $map);//带分页的
            int_to_string($list, array(
                'status' => array(-1 => '不通过', 0 => '已发送')
            ));
            $this->assign('list', $list);
            //主图文本周内的新闻消息
            $t    = $this->week_time();
            $info = array(
                'create_time' => array('egt', $t),
                'status'      => 0,
                'push'        => 1
            );
            //获取推送数据
            $wish    = new Wish();//活动发起
            $wishArr = $wish->push($info);

            $dreamflash = new Dreamflash();//梦想快讯
            $dreamflashArr   = $dreamflash->push($info);//在model层封装

            $notice    = new Notice();//通知公告
            $noticeArr = $notice->push($info);

            $matter    = new Matter();//重要文件
            $matterArr = $matter->push($info);

            $learn     = new Learn();//两学一做
            $learnArr  = $learn->push($info);

            $special     = new Special();//十九大专题
            $specialArr  = $special->push($info);

            $this->assign('wishArr', $wishArr);
            $this->assign('dreamflashArr', $dreamflashArr);
            $this->assign('noticeArr', $noticeArr);
            $this->assign('matterArr', $matterArr);
            $this->assign('learnArr', $learnArr);
            $this->assign('specialArr', $specialArr);
            return $this->fetch();
        }
    }

    /*
     * 接受推送信息
     * */
    public function pushList()
    {
        $data   = input('post.');
        $main   = explode('-', $data['focus_main']);//主图文
        $vice   = [];//副图文
        $all    = array();
        $wish   = new Wish();
        $dreamflash = new Dreamflash();
        $notice    = new Notice();
        $matter    = new Matter();
        $learn  = new Learn();
        $special     = new Special();
        $modelArr = array(
            1 => $wish,
            2 => $dreamflash,
            3 => $notice,
            4 => $matter,
            5 => $learn,
            6 => $special,
        );
        /*switch ($main[0]) {
            case 1:
                $mainArray = $wish->where('id', $main[1])->find();
                break;
            case 2:
                $mainArray = $dreamflash->where('id', $main[1])->find();
                break;
            case 3:
                $mainArray = $notice->where('id', $main[1])->find();
                break;
            case 4:
                $mainArray = $matter->where('id', $main[1])->find();
                break;
            case 5:
                $mainArray = $learn->where('id', $main[1])->find();
                break;
            case 6:
                $mainArray = $special->where('id', $main[1])->find();
                break;
        }*/
        $mainArray = $modelArr[$main[0]]->where('id', $main[1])->find();
        $all = $this->changeTpye($all, $mainArray, $main[0]);
        //副图文
        // var_dump($data);exit;
        if ($data['focus_vice'] != '') {//如果没有选择副图文的情况
            foreach ($data['focus_vice'] as $k => $v) {
                $vice[] = explode('-', $v);
            }
            foreach ($vice as $k => $v) {
                $viceArray = $modelArr[$v[0]]->where('id', $v[1])->find();
                $all = $this->changeTpye($all, $viceArray, $v[0]);
            }
        }
        $this -> push($all);//推送
    }

    /**
     * 进行数据区分
     * @param $list
     * @param $type
     */
    private function changeTpye($all, $list, $type)
    {
        //图片进行转化
        $img = Picture::get(['id' => $list['front_cover']]);
        $list['img']   = $img['path'];
        //增加类别
        $list['class'] = $type;
        array_push($all, $list);
        return $all;
    }

    /*
     * 推送订阅号
     * */
    public function push($push)
    {
        $Wechat = new TPWechat(Config::get('party'));

        $host   = "http://mxxz.0571ztnet.com";//域名
        //拼接到图片的路径
        if(!empty($push)) {
           
            foreach($push as $k=>$v) {
                $data = array(//固定格式
                    "media" => '@.'.$v['img']
                );
                $img = $Wechat ->uploadForeverMedia($data,'thumb');
                $push[$k]['thumb_media_id'] = $img['media_id'];//图片地址
                $id  = $v['id'];
                //拼路径
                if($v['class'] == 1) {
                    $link = "activity/activitydetails";
                } elseif ($v['class'] == 2) {
                    $link = "dreamflash/article";
                } elseif ($v['class'] == 3) {
                    $link = "college/forumnotice";
                } elseif ($v['class'] == 4) {
                    $link = "college/forummatter";
                } elseif ($v['class'] == 5) {
                    $link = "learn/article";
                } elseif ($v['class'] == 6) {
                    $link = "special/article";
                }
                $push[$k]['content_source_url'] = "$host/home/$link/id/$id";
                //如果内容有图片
                preg_match_all('^<img.*? (src=\".*?(\.(jpg|gif|bmp|bnp|png|mp4|jpeg))*\")^',$v['content'],$match);
                $result_media=$match[1];//url的地址
                if($result_media != '') {//判断内容有没有图片
                    foreach($result_media as $key_media=>$val_media){
                        $url_media=explode('"',$val_media);
                        $urldata=array("media"=>"@./".$url_media[1]); //windows环境下使用绝对路径，linux环境下使用相对路径
                        $Pics = $Wechat->uploadImg($urldata);
                        if ($Pics){
                            $push[$k]['content'] = str_replace($val_media,'src="'.$Pics['url'].'"',$v['content']);//将URL替换为微信平台返回的URL
                        }else{
                            echo $Wechat->errMsg ;
                        }
                    }
                }

            }
            //上传图文消息素材
            $article = array();
            foreach ($push as $k =>$v ){
                $article['articles'][$k] = [
                    'thumb_media_id' => $v['thumb_media_id'],
                    'author' => $v['publisher'],
                    'title' => $v['title'],
                    'content_source_url' => $v['content_source_url'],
                    "content" => $v['content'],
                    "digest" => msubstr(strip_tags($v['content']),0,30),
                    "show_cover_pic" => 0,
                ];
            }
            //上传多条永久图文素材
            $info = $Wechat ->uploadForeverArticles($article);
//            var_dump($info);die;
            //消息群发
            $send = [
                /*"filter"=>array(
                    "is_to_all"=>true,     //是否群发给所有用户.True不用分组id，False需填写分组id
                ),*/
                'touser'=>'4a08b03f568be7ea7642591a357483db',
                "msgtype"=>"mpnews",        //群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video，卡券为wxcard
                "mpnews" => ['media_id' => $info['media_id']]//用于设定即将发送的图文消息
            ];
            $res = $Wechat ->sendGroupMassMessage($send);
            //发送成功 修改对应数据状态
            //var_dump($Wechat->errCode);exit;
            if($res['errcode'] == 0) {
                $wish   = new Wish();
                $dreamflash = new Dreamflash();
                $notice    = new Notice();
                $matter    = new Matter();
                $learn  = new Learn();
                $special     = new Special();
                $modelArr = array(
                    1 => $wish,
                    2 => $dreamflash,
                    3 => $notice,
                    4 => $matter,
                    5 => $learn,
                    6 => $special,
                );
                //修改推送状态 || 添加推送表
                foreach($push as $k=>$v) {
                    $modelArr[$v['class']]->where('id', $v['id'])->update(['status'=>1]);
                    $datas = [
                        'focus'       =>$v['id'],
                        'create_user' => $_SESSION['think']['user_auth']['id'],
                        'status'      => 1,
                        'class'       => $v['class']
                    ];
                    PushModel::create($datas);
                }
                return  $this->success('推送成功');
            }else{
                return $this->error('推送失败');
            }
        } else {
            return  $this->success('暂无推送数据');
        }

    }
}















