<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\WechatUser;

class Servicemenu extends Base{
    /*
     * 主页
     */
    public function index(){
        return $this->fetch();
    }
    /*
     * 组织架构详情页
     */
    public function detail(){
        return $this->fetch();
    }
}
