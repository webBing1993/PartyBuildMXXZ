<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/24
 * Time: 9:22
 */
namespace app\home\controller;
use think\Controller;
use app\home\model\WechatUser;
use think\Cookie;
use think\Session;
use think\Config;

class Verify extends Controller{
    public function _initialize(){
        //cookie初始化
        Cookie::init(Config::get('cookie'));
    }
    /**
     * 用户登入获取信息
     */
    public function index(){
//        dump(md5(uniqid()));
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
            $map = ['mobile' => $vali['user'],'state' => 1];
            $result = $user ->where($map) ->find();
            //账户密码正确
            if($result){
                Cookie::clear('dypb');
                session('userId',null);
                //第一次登陆 初始userId
                if(empty($result['userid']))
                {
                    $id = md5(uniqid());//不重复随机id
                    Cookie::set('dypb',['user' =>$id]);
                    $user ->save( ['userid' => $id,'status' => 1] , ['mobile' => $vali['user'],'state' => 1]);
                }else{
                    Cookie::set('dypb',['user' =>$result['userid']]);
                }
                //登陆后跳转判断
                if(Session::has('url'))
                {
                    $url = session('url');
                    Session::delete('url');//跳转后清空
                }else{
                    $url = '';
                }
                return $this ->success('登录成功!',$url);
            }else{
                return $this ->error('账号错误!');
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
        if (strpos($user_agent, 'MicroMessenger') === false) 
        {
            // 非微信浏览器禁止浏览
            return $this ->error('请在微信打开!');
        } else {
            // 微信浏览器，允许访问
            // 获取版本号
            preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
        }
    }
    /**
     * 退出登录
     */
    public function logout(){
        //cookie初始化 session 初始化
        Cookie::delete('dypb');
        session('userId',null);
        return $this ->fetch('memberslogin');
    }
    /**
     * 游客登录
     */
    public function tourist(){
        //cookie初始化 session 初始化
        Cookie::delete('dypb');
        session('userId','visitor');
        return $this ->redirect(session('url'));
    }

    /**
     * 带游客模式的登录页
     * @return mixed
     */
    public function memberslogin(){
        return $this ->fetch();
    }
}