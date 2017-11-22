<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 2017/11/22
 * Time: 上午9:51
 */

namespace app\home\controller;

/**
 * 梦想快讯
 */
class Dreamflash extends Base {
    /**
     * 首页
     */
    public function index(){
        return $this->fetch();
    }

    /**
     * 新闻详情页
     */
    public function news(){
        return $this->fetch();
    }

    /**
     * 视频详情页
     */
    public function video(){
        return $this->fetch();
    }

}