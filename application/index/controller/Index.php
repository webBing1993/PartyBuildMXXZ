<?php
namespace app\index\controller;

use app\admin\model\Menu;
use app\admin\model\Config;
use app\user\model\UcenterMember;
use app\user\model\WechatUser;
use think\Controller;

class Index extends Controller
{
    public function index() {

//        $list = Menu::where('1=1')->field('title,id,pid');
//        $list = Menu::all(function($query){
//            $query->field('title,id,pid');
//        });
//
//        dump($list);

    }
}
