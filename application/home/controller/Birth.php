<?php

namespace app\home\controller;
/**
 * Class Birth
 * @package  入党那一天
 */
class Birth extends Base {
    /**
     * 主页
     */
    public function index(){

        return $this->fetch();
    }

    /**
     *  详细页
     */
    public function detail(){

        return $this->fetch();
    }
}