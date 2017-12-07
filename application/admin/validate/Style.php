<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/11
 * Time: 16:36
 */

namespace app\admin\validate;


use think\Validate;

class Style extends Validate {
    protected $rule = [
        'name' => 'require',
        'mobile' => 'require',
        'position' => 'require',
        'mechanism' => 'require',
        'content' => 'require',
        'publisher' => 'require',

    ];

    protected $message = [
        'name' =>  '姓名不能为空',
        'mobile'  =>  '联系方式不能为空',
        'position'  =>  '职位信息不能为空',
        'mechanism'  =>  '机构不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布者不能为空',
    ];
    
}