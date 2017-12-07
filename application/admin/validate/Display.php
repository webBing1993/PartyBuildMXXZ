<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/11
 * Time: 16:36
 */

namespace app\admin\validate;


use think\Validate;

class Display extends Validate {
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require',

    ];

    protected $message = [
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布者不能为空',
    ];
    
}