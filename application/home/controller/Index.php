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
use com\wechat\TPWechat;
use think\Config;
use think\Controller;
use app\user\controller\Index as APIIndex;
use think\Log;

/**
 * 党建主页
 */
class Index extends Base {
    
    public function index(){
        return $this->fetch();
    }
    
}