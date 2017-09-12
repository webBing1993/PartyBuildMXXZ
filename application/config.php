<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    'url_route_on' => true,
//    'log'          => [
//        'type' => 'trace', // 支持 socket trace file
//    ],

    /* 默认模块和控制器 */
    'default_module' => 'home',

    /* URL配置 */
    'base_url'=>'',
    'parse_str'=>[
        '__ROOT__' => '/',
        '__STATIC__' => '/static',
        '__ADMIN__' => '/admin',
        '__HOME__' => '/home',
    ],
    
    /* 公众号配置 */
    'party' => array(
        'login' =>'http://zthm.0571ztnet.com/home/verify/index',
        'token' =>'mxxzdj',
        'encodingaeskey' =>'8Bzq5oDfWQJSWAgvOsHbOmmUOP54lcCGokJ2tXsaGrN',
        'appid' =>'wxd4e86a2f4f1e299b',
        'appsecret' =>'f76e392e80496e81f8244115f823143e',
    ),
    

    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je',

     /*直播地址*/
    'live_path' => 'https://pullhls36734237.live.126.net/live/4f2e1cac3a694f9ba9c0ed4397c42a2d/playlist.m3u8'

];
