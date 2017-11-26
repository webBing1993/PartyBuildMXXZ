<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/26
 * Time: 10:50
 */

namespace app\home\controller;


class Report extends Base{


    /**
     * 主页列表
     */
    public function index(){
        return $this->fetch();
    }

    /**
     * 详情页
     */
    public function detail(){
        return $this->fetch();
    }
}