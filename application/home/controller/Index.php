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
class Index extends Controller {
    public function index(){
        return $this->fetch();
    }

    /**
     * 用户登入获取信息
     */
    public function login(){
        // 获取用户信息
        $Wechat = new TPWechat(Config::get('party'));
        $result = $Wechat->getOauthAccessToken();
        $newUser = $Wechat ->getUserInfo($result["openid"]);
        //查询本地用户是否存在
        $map = array(["openid"=>$result["openid"],'state' => 1]);
        $user = model("WechatUser") ->where($map) ->find();
        //不存在
        if(empty($user)) {
            $this->redirect('');//跳转登录页
        }else{
            //更新用户信息
            $user ->save($newUser, ['openid' => $result["openid"]]);
        }
        session('userId', $result["openid"]);
        if(empty(session('url'))){
            $this->redirect('');//跳转首页
        }else{
            $this->redirect(session('url'));
        }
    }
}