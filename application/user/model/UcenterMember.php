<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/4/25
 * Time: 10:55
 */

namespace app\user\model;

use think\Config;
use think\model\Merge;

class UcenterMember extends Merge
{
    protected $autoWriteTimestamp = false;
    // 定义关联模型列表
    protected static $relationModel = ['Member'];
    // 定义关联外键
    protected $fk = 'id';
    protected $mapFields = [
        // 为混淆字段定义映射
        'id'        =>  'UcenterMember.id',
        'member_id' =>  'Member.id',
        'status'    =>  'UcenterMember.status',
        'member_status' =>  'Member.status',
        'reg_time'    =>  'UcenterMember.reg_time',
        'member_reg_time' =>  'Member.status',
        'reg_ip'    =>  'UcenterMember.reg_ip',
        'member_reg_ip' =>  'Member.reg_ip',
        'member_reg_time' =>  'Member.reg_time',
        'last_login_time'    =>  'UcenterMember.last_login_time',
        'member_last_login_time' =>  'Member.last_login_time',
        'last_login_ip'    =>  'UcenterMember.last_login_ip',
        'member_last_login_ip' =>  'Member.last_login_ip',
    ];

    protected $insert = [
        'reg_time','reg_ip','password','status'=>1
    ];

    protected function setRegTimeAttr(){
        return NOW_TIME;
    }
    
    protected function setRegIpAttr(){
        return get_client_ip(1);
    }
    
    protected function setPasswordAttr($vaule){
        return think_ucenter_md5($vaule, Config::get('uc_auth_key'));
    }
    
    /**
     * 更新登入信息
     * @param $uid
     */
    public function updateLogin($uid){
        $data = array(
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
//        $this->where(['id' => $uid])->update($data);
    }
}