<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\WechatUser;

class Club extends Base{
    /*
     * 主页
     */
    public function index(){
        return $this->fetch();
    }
    /*
     * 社团详情页
     */
    public function detail(){
        return $this->fetch();
    }
    /*
     * 活动列表
     */
    public function activity(){
        return $this->fetch();
    }
    /*
     * 活动详情页
     */
    public function activitydetail(){
        return $this->fetch();
    }
}
