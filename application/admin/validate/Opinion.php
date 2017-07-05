<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 16:28
 */

namespace app\admin\validate;


use think\Validate;

class Opinion extends Validate {
    protected $rule = [
        'content'  =>  'require',
    ];

    protected $message = [
        'content.require'  =>  '请填写内容！',
    ];

}