<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/19
 * Time: 17:57
 */
namespace app\admin\validate;
use think\Validate;

class Pioneer extends Validate{
    protected $rule = [
        'front_cover' => 'require',
        'name' => 'require',
        'position' => 'require'
    ];

    protected $message = [
        'front_cover.require'  =>  '请上传导师头像！',
        'name.require' =>  '请输入导师姓名！',
        'position.require'  =>  '请填写导师职位！',
    ];
}