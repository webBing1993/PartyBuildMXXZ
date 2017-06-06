<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/5/19
 * Time: 9:43
 */

namespace app\admin\model;


use think\Model;

class WechatUser extends Base
{
    public function WechatMessage() {
        return $this->belongsTo('WechatMessage');
    }

    public function Wechat_department() {
        return $this->belongsToMany('Wechat_department','Wechat_department_user');
    }

    public function Wechat_tag() {
        return $this->belongsToMany('Wechat_tag','Wechat_user_tag');
    }

    /**
     * @param $data
     * @return array
     *用户的新增跟修改
     */
    public function add($data){
        //出生年月 跟入党时间 时间戳修改
        if($data['birthday']){
            $data['birthday'] = strtotime($data['birthday']);
        }
        if($data['partytime']){
            $data['partytime'] = strtotime($data['partytime']);
        }
        //修改用户
        if(!empty($data['check'])){
            $this ->allowField([
                'mobile','name','department','gender','position','birthday','education','nation','partytime','address'
            ]) ->save($data, ['id' => $data['check']]);
            return ['code' => 1,'msg'=> '修改成功'];
        }else{
            //新增用户
            $record = $this ->where(['mobile' => $data['mobile'],'state' => 1]) ->find();
            //唯一id重复
            if($record){
                return ['code' => 0,'msg'=> '手机号码有重复,请核实后重新输入!'];
            }else{
//                    $user ->data($data) ->allowField(true) ->save();
                $this ->data($data) ->allowField([
                    'mobile','name','department','gender','position','birthday','education','nation','partytime','address'
                ]) ->save();
                return['code' => 1,'msg'=> '新增成功'];
            }
        }
    }
}