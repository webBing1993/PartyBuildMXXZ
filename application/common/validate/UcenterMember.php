<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/4/25
 * Time: 10:56
 */

namespace app\common\validate;

use think\Validate;

class UcenterMember extends Validate
{
    protected $rule = [
        'username'  =>  'require|max:30|unique:UcenterMember',
        'password' => 'min:6|max:50',
        'email' =>  'unique:UcenterMember',
        'mobile' => 'regex:/^1\d{10}$/|unique:UcenterMember'
    ];

    protected $message = [
        'username.require'  =>  '用户名必须',
        'username.max'  =>'用户名过长' ,
        'usename.unique' => '用户名存在',
        'email' =>  '邮箱格式错误',
        'email.unique'=>'邮箱已经被注册了'
    ];

    protected $scene = [
        'add'   =>  ['name','email','mobile'],
        'edit'  =>  ['name'],
    ];

}