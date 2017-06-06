<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/5/19
 * Time: 14:07
 */

namespace app\admin\model;


use think\Model;

class WechatDepartment extends Base
{
    public function Wechat_user() {
        return $this->belongsToMany('WechatUser','WechatDepartmentUser');
    }
    //增加或修改部门
    public function add($data){
        $result = $this ->where(['name' => $data['name'],'status' => 1]) ->find();
        if($result){
            if(!empty($data['id'])){
                $this ->save(['name' => $data['name']],['id' => $data['id']]);
                return ['code' => 1,'msg'=> '修改成功'];
            }else{
                return ['code' => 0,'msg'=> '新增失败,已存在同名称部门!'];
            }
        }else{
            if(!empty($data['id'])){
                $this ->save(['name' => $data['name']],['id' => $data['id']]);
                return ['code' => 1,'msg'=> '修改成功'];
            }else{
                $info = $this ->data(['name' => $data['name']]) ->save();
                return ['code' => 1,'msg'=> '添加成功','id' => $info];
            }
        }
    }
}