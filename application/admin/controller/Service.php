<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/3/25
 * Time: 09:12
 * 微信事件接口
 */
namespace app\admin\controller;

use app\admin\model\WechatLog;
use com\wechat\QYWechat;
use com\wechat\TPQYWechat;
use com\wechat\TPWechat;
use think\Config;
use think\Controller;
use think\Log;

class Service extends Controller
{

    // 服务号接收的应用
    public function event() {
        $Wechat = new TPQYWechat(Config::get('party'));
        $Wechat->valid();

        $type = $Wechat->getRev()->getRevType();
        switch ($type) {
            case QYWechat::MSGTYPE_TEXT:
                $Wechat->text("您好！感谢关注！")->reply();
                break;
            case QYWechat::MSGTYPE_EVENT:
                $event = $Wechat->getRev()->getRevEvent();
                switch ($event['event']) {
                    case 'subscribe':
//                        $replyText = "您好！欢迎关注香市党建！";
//                        $Wechat->text($replyText)->reply();
                        $newsData = array(
                            '0'=> array(
                                'Title' => "欢迎您关注“香市机关党建”",
                                'Description' => "内含企业二维码，可转发给同事关注",
                                'PicUrl' => "http://xspb.0571ztnet.com/home/images/special/music_2.jpg",
                                'Url' => "http://u3665579.viewer.maka.im/pcviewer/WRZ0GJFA",
                            ),
                        );
                        $Wechat->news($newsData)->reply();
                        break;
                    case 'enter_agent':
                        $data = array(
                            'event' => $event['event'],
                            'msgtype' => $type,
                            'agentid' => $Wechat->getRev()->getRevAgentID(),
                            'create_time' => $Wechat->getRev()->getRevCtime(),
                            'event_key' => isset($event['key']) ? $event['key'] : '',
                            'userid' => $Wechat->getRev()->getRevFrom()
                        );
//                        Log::record("进入事件：".json_encode($data));
                        $id  = WechatLog::create($data);
//                        Log::record("创建记录：".$id);
                        //$Wechat->text(json_encode($data))->reply();
                        save_log($Wechat->getRev()->getRevFrom(), 'WechatLog');
                        break;
                }
                break;
            case QYWechat::MSGTYPE_IMAGE:
                break;
            default:
                $Wechat->text("您好！感谢关注！")->reply();
        }

    }

    // 企业号验证
    public function oauth() {
        $weObj = new TPQYWechat(Config::get('party'));
        $weObj->valid();
    }

    //订阅号验证
    public function oauth2(){
        $Wechat = new TPWechat(Config::get('news'));
        $Wechat->valid();
    }

    // 创建订阅号菜单
    public function menu() {
        $menu["button"] = array(
            array(
                "type"=>"view",
                "name"=>"第一聚焦",
                "url"=>"http://party.0571ztnet.com/home/focus/index"
            ),
            array(
                "type"=>"view",
                "name"=>"活动通知",
                "url"=>"http://party.0571ztnet.com/home/activity/index"
            ),
            array(
                "type"=>"view",
                "name"=>"品牌特色",
                "url"=>"http://party.0571ztnet.com/home/special/index"
            ),
        );

        $Wechat = new TPWechat(Config::get('news'));
        $result = $Wechat->createMenu($menu);

        if($result) {
            return $this->success('提交成功');
        } else {
            return $this->error('错误代码：'.$result['errcode'].'，消息：'.$result['errmsg']);
        }
    }

    public function media() {
        $Wechat = new TPWechat(Config::get('news'));
        $list = $Wechat->getForeverList("news", 0, 20);
        dump($list);
    }
}