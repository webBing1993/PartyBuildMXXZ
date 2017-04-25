<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/5/27
 * Time: 11:08
 */

namespace app\admin\model;


class Log extends Base
{
    protected $insert = [ 'create_time'=> NOW_TIME ];

    public function user(){
        return $this->hasOne('WechatUser','userid','userid');
    }
}