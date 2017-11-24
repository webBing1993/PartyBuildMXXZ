<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/22
 * Time: 15:58
 */

namespace app\home\controller;


class Brand extends Base{
    /*
     * 主页
     */
    public function index(){
        return $this->fetch();
    }
    /*
     * 详情页
     */
    public function detail(){
        return $this->fetch();
    }

}