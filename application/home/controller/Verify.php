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
use app\home\model\WechatUser;
use think\Cookie;

class Verify extends Controller{
    /**
     * 用户登入获取信息
     */
    public function index(){
        // 获取用户信息
//        $Wechat = new TPWechat(Config::get('party'));
//        $result = $Wechat->getOauthAccessToken();
//        $newUser = $Wechat ->getUserInfo($result["openid"]);
//        //查询本地用户是否存在
//        $map = array(["openid"=>$result["openid"],'state' => 1]);
//        $user = model("WechatUser") ->where($map) ->find();
//        //不存在
//        if(empty($user)) {
//            session('newuser',$newUser);//暂时储存用户微信数据
//            $this->redirect('Verify/login');//跳转登录页
//        }else{
//            //更新用户信息
//            $user ->save($newUser, ['openid' => $result["openid"]]);
//        }
//        session('userId', $result["openid"]);
//        if(empty(session('url'))){
//            $this->redirect('Index/index');//跳转首页
//        }else{
//            $this->redirect(session('url'));
//        }
    }
    /**
     *账号密码登陆
     */
    public function login(){
        //判断是否味微信打开
//        $this ->oauth();
        $pass = '123456';//默认密码123456
        $vali = input('post.');
        if($vali){
            $user = new WechatUser();
            $result = $user ->where(['mobile' => $vali['user'],'state' => 1]) ->find();
            //账户密码正确
            if($result && $vali['password'] == $pass){
                //cookie初始化
                Cookie::init(['prefix'=>'think_','expire'=>31533600,'path'=>'/']);
                Cookie::set('dypb',['user' =>$vali['user']]);
                if(empty($result['userid'])){
                    $user ->save( ['userid' => $vali['user']] , ['mobile' => $vali['user']]);
                }
                    return $this ->success('登录成功!', session('url'));
            }else{
                return $this ->error('账号或密码错误!');
            }
        }else{
            return $this ->fetch();
        }
    }
    /**
     * 判断是否微信打开
     */
    public function oauth(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            // 非微信浏览器禁止浏览
            return $this ->error('请在微信打开!');
        } else {
            // 微信浏览器，允许访问
            // 获取版本号
            preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
        }
    }
}