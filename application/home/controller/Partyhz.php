<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/4/27
 * Time: 16:14
 */
namespace app\home\controller;

class Partyhz extends Base{
    //红色汇集首页
    public function index(){
        return $this ->fetch();
    }
}