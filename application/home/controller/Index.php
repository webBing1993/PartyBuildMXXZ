<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 13:47
 */

namespace app\home\controller;
use app\home\model\Message;
use app\home\model\WechatUser;
use com\wechat\TPQYWechat;
use think\Config;
use think\Controller;
use think\Log;
use app\home\model\News as NewsModel;

/**
 * 党建主页
 */
class Index extends Base {
    public function index(){
        $news = new NewsModel();
        $list2 = $news ->where(['status' => ['egt',0]]) ->order('create_time desc') ->limit(6)->select();
        $this ->assign('list2',$list2);
        $this ->assign('user',session('userId'));
        return $this->fetch();
    }
}