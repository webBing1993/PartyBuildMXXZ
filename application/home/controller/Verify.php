<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/24
 * Time: 9:22
 */
namespace app\home\controller;
use think\Controller;
use think\Config;
use com\wechat\TPWechat;

class Verify extends Controller{
    /**
     * 用户登入获取信息
     */
    public function index(){
        // 获取用户信息
        $Wechat = new TPWechat(Config::get('party'));
        $result = $Wechat->getOauthAccessToken();
        $newUser = $Wechat ->getUserInfo($result["openid"]);
        //查询本地用户是否存在
        $map = array(["openid"=>$result["openid"],'state' => 1]);
        $user = model("WechatUser") ->where($map) ->find();
        //不存在
        if(empty($user)) {
            session('newuser',$newUser);//暂时储存用户微信数据
            $this->redirect('Verify/login');//跳转登录页
        }else{
            //更新用户信息
            $user ->save($newUser, ['openid' => $result["openid"]]);
        }
        session('userId', $result["openid"]);
        if(empty(session('url'))){
            $this->redirect('Index/index');//跳转首页
        }else{
            $this->redirect(session('url'));
        }
    }
    //账号密码登陆
    public function login(){
        return $this ->fetch();
    }
   
}