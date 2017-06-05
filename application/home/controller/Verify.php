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
    /**
     *账号密码登陆
     */
    public function login(){
        //没有session值 跳转微信认证
//        $newUser = session('newuser');//暂时储存用户微信数据
//        if(empty($newUser)){
//            return $this ->fetch('index');
//        }
        $newUser = array(
            'openid' => '15700004138',
            'nickname' => 'stiff',
            'sex' => '1',
            'city' => '杭州',
            'province' => '浙江',
            'country' => '中国',
            'language' => 'en',
            'headimgurl' => 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEKlsVNxbstk0iaBJaqibfombfH0Q4rWUIGnO2VP5IjJ0nj1fveoPG4JCM9mvR6jibpDBBqoKEia5MIjKw/0',
            'subscribe_time' => '1495784632',
            'subscribe' => '1',
            'tagid_list' => '',
            'status' => 1
        );
        $pass = '123456';//默认密码123456
        $vali = input('post.');
        if($vali){
            $user = new WechatUser();
            $result = $user ->where('mobile',$vali['user']) ->find();
            if($result && $vali['password'] == $pass){
                //userid 为 openid
                $newUser['userid'] =  $newUser['openid'];
                $user ->save( $newUser , ['mobile' => $vali['user']]);
                return $this ->success('登录成功!');
            }else{
                return $this ->error('账号或密码错误!');
            }
        }else{
            return $this ->fetch();
        }
    }
   
}