<?php
namespace app\common\behavior;
use think\Config;
use think\Request;

class AppInit  {

    public function run(&$param){
        //站点初始化
        $this->initialization();
        //注册路由
        if (Config::get('url_route_on')) {
            $this->router();
        }
    }
    private function router(){
        $router_rule['test']='index/Index/test';
        $router_rule['test2']='index/Index/test2';
        \think\Route::rule($router_rule);
    }

    //初始化
    private function initialization() {
        //定义废除的一些常量
        define('NOW_TIME',$_SERVER['REQUEST_TIME']);

        $request = Request::instance();
        define('REQUEST_METHOD', $request->method());
        define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
        define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
        define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
        define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
        define('IS_AJAX', $request->isAjax());
        define('__EXT__', $request->ext());
    }
    
}