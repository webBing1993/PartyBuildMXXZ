<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/4/25
 * Time: 10:45
 */

namespace app\user\model;

use think\Model;

class Member extends Base
{
    protected $insert = [
        'reg_time','reg_ip','status'=>1
    ];

    protected function setRegTimeAttr(){
        return NOW_TIME;
    }

    protected function setRegIpAttr(){
        return get_client_ip(1);
    }
}