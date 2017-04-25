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
        $Wechat = new TPQYWechat(config('party'));
        $result = $Wechat->getUserId(input('code'), config('party.agentid'));
        if(isset($result['UserId'])) {
            $user = $Wechat->getUserInfo($result['UserId']);

            // 添加本地数据
            $UserAPI = new APIIndex();
            $localUser = $UserAPI->checkWechatUser($result['UserId']);
            if($localUser) {
                $UserAPI->updateWechatUser($user);
            } else {
                $UserAPI->addWechatUser($user);
            }
            session("userId", $result['UserId']);
            //存在url则跳转，不存在则回主页
            if(session('url')){
                $this->redirect(session('url'));
                session('url','');
            }else{
                $this->redirect("Activity/index");
            }
        } else {
            // 用户不存在通讯录默认为游客，跳转到url;
            session('userId','visitor');
            $this->redirect(session('url'));
        }
    }
}