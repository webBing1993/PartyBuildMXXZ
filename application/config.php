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
        'login' => 'http://zthm.0571ztnet.com/home/verify/index',
        'token' => 'N3mIjNX',
        'encodingaeskey' => 'RxanruTaFxW7X5r5Cx2xRrI91dhRgNUx77KM3paUfS7',
        'appid' => 'wxd4e86a2f4f1e299b',
        'appsecret' => 'c1a2250667aae7d6898517343b0270f1'
    ),
    

    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je'
    
];
