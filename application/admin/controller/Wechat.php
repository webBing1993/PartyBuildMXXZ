<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/5/20
 * Time: 09:14
 */

namespace app\admin\controller;


use app\admin\model\WechatDepartment;
use app\admin\model\WechatDepartmentUser;
use app\admin\model\WechatTag;
use app\admin\model\WechatUser;
use app\admin\model\WechatUserTag;
use com\wechat\QYWechat;
use com\wechat\TPWechat;
use think\Config;
use think\Input;

class Wechat extends Admin{
    //用户页面
    public function user() {
        $name = input('name');
        $map = ['name' => ['like', "%$name%"],'state' => 1];
        $list = $this->lists("WechatUser",$map);

        //部门进行转换
        foreach ($list as $key=>$value) {
            $departmentId = $value['department'];
            if($departmentId){
                $record = WechatDepartment::where('id',$departmentId) ->find();
                $list[$key]['department'] = $record['name'];
            }else{
                $list[$key]['department'] = "暂无";
            }
        }
       // 状态转化
        wechat_status_to_string($list);
        $this->assign('list',$list );
        return $this->fetch();
    }
    /**
     * 部门添加跟修改
     */
    public function add_department(){
        $data = input('post.');
        if(input('post.')){
            $department = new WechatDepartment();
            $result = $department ->add($data);
            if($result['code']){
                return $this ->success($result['msg']);
            }else{
                return $this ->error($result['msg']);
            }
        }else{
            $id = input('id');
            //$type 1为修改 0为新增
            if($id){
                $type = 1;
                $record = WechatDepartment::where('id',$id) ->field('name') ->find();
                $this ->assign('name',$record['name']);
            }else{
                $type = 0;
            }
            $this ->assign('id',$id);
            $this ->assign('type',$type);
            return $this->fetch();
        }
    }

    /**
     * 删除部门
     */
    public function del_department(){
        $id = input('get.id');
        if($id){
            //该部门已经由成员就禁止删除
            $record = WechatUser::where(['department' => $id,'state' => 1]) ->find();
            if($record){
                return $this ->error('该部门已经存在用户,静止删除!');
            }else{
                $wd = new WechatDepartment();
                $wd ->save(['status' => 0,], ['id' => $id]);
                return $this ->success('删除成功!');
            }
        }else{
            return $this ->error('参数错误!');
        }
    }
    /**
     * 用户的增加与修改
     */
    public function add_user(){
        $data = input('post.');
        $user = new WechatUser();
        if(input('post.')){
            $info = $user ->add($data);
            if($info['code'] == 1){
                return $this ->success($info['msg']);
            }else{
                return $this ->error($info['msg']);
            }
        }else{
            $id = input('id');
            //$type 1为修改 0为新增
            if($id){
                $type = 1;
                $info = $user ->where('id',$id) ->find();
                $this ->assign('info',$info);
            }else{
                $type = 0;
            }
            $department = WechatDepartment::where('status',1) ->select();
            $this ->assign('department',$department);
            $this ->assign('type',$type);
            return $this->fetch();
        }
    }
    /**
     * 删除用户
     */
    public function del_user(){
        $id = input('get.id');
        if($id){
            $user = new WechatUser();
            $record = $user ->save(['state' => 0],['id' => $id]);
            if($record){
                return $this ->success('删除成功');
            }else{
                return $this ->error('删除失败');
            }
        }else{
            return $this ->error('参数错误');
        }
    }
    public function tag(){
        $list = $this->lists("WechatTag");
        $this->assign('list', $list);
        return $this->fetch();
    }


}