<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/11
 * Time: 16:36
 */

namespace app\admin\validate;


use think\Validate;

class ClubActivity extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require',
        'start_time' => 'require',
        'address' => 'require',
        'telephone' => 'require',
        'content' => 'require',
        'publisher' => 'require',

    ];

    protected $message = [
        'front_cover' => '封面图片不能为空',
        'title' =>  '标题不能为空',
        'start_time' => '开始时间不能为空',
        'address' => '地址不能为空',
        'telephone' => '联系方式不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布者不能为空',
    ];
    
}