<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/19
 * Time: 16:38
 */
namespace app\admin\controller;
use app\admin\model\Pioneer as PionnerModel;

//先锋引领
class Pioneer extends Admin{
    /**
     * 创业导师团
     * @return mixed
     */
    public function partybuild(){
        $map = array(
            'status' => ['egt',0],
            'type' => 1,
        );
        $list = $this->lists('Pioneer',$map);
        $this->assign('list',$list);
        return $this ->fetch();
    }
    /**
     * 创客先锋队
     */
    public function business(){
        $map = array(
            'status' => ['egt',0],
            'type' => 2,
        );
        $list = $this->lists('Pioneer',$map);
        $this->assign('list',$list);
        return $this ->fetch();
    }
    /**
     * 先锋事迹展
     */
    public function story(){
        $map = array(
            'status' => array('egt',0),
            'type' => 3
        );
        $list = $this->lists('Pioneer',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布",1=>"已发布"),
            'recommend' => array( 1=>"是" , 0=>"否"),
            'push' => array( 1=>"是" , 0=>"否"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }
    /**
     * 创业导师团 新增 修改
     */
    public function addpb(){
        $data = input('post.');
        $info = input('get.');
        $pioneer = new PionnerModel();
        if(empty($data))
        {
            //新增页面
            if(empty($info['id']) || 
               !isset($info['id']) ) 
            {
                return $this ->fetch();
            }else{
                //修改页面
                $msg = $pioneer ->where('id',$info['id']) ->find();
                $this ->assign('msg',$msg);
                return $this ->fetch();
            }
            
        }else if(empty($data['id'])){
            //新增方法
            unset($data['id']);
            $res = $pioneer ->validate('Pioneer') ->save($data);
            if(!empty($res))
            {
                return $this ->success("新增成功",Url('Pioneer/partybuild'));
            }else{
                return $this ->error($pioneer ->getError());
            }
        }else{
            //修改方法
            $id = $data['id'];
            unset($data['id']);
            $res = $pioneer ->validate('Pioneer') ->save($data,['id' => $id]);
            if(!empty($res))
            {
                return $this ->success("修改成功",Url('Pioneer/partybuild'));
            }else{
                return $this->get_update_error_msg($pioneer ->getError());;
            }
        }

    }
    /**
     * 创客先锋队 新增 修改
     */
    public function addbs(){
        $data = input('post.');
        $info = input('get.');
        $pioneer = new PionnerModel();
        if(empty($data))
        {
            //新增页面
            if(empty($info['id']) ||
                !isset($info['id']) )
            {
                return $this ->fetch();
            }else{
                //修改页面
                $msg = $pioneer ->where('id',$info['id']) ->find();
                $this ->assign('msg',$msg);
                return $this ->fetch();
            }

        }else if(empty($data['id'])){
            //新增方法
            unset($data['id']);
            $res = $pioneer ->validate('Pioneer') ->save($data);
            if(!empty($res))
            {
                return $this ->success("新增成功",Url('Pioneer/business'));
            }else{
                return $this ->error($pioneer ->getError());
            }
        }else{
            //修改方法
            $id = $data['id'];
            unset($data['id']);
            $res = $pioneer ->validate('Pioneer') ->save($data,['id' => $id]);
            if(!empty($res))
            {
                return $this ->success("修改成功",Url('Pioneer/business'));
            }else{
                return $this->get_update_error_msg($pioneer ->getError());;
            }
        }

    }
    /**
     * 先锋事迹 新增 修改
     */
    public function addsy(){
        $data = input('post.');
        $id = input('get.id');
        $pioneer = new PionnerModel();
        if(empty($data))
        {
            //新增页面
            if(empty($id) ||
               !isset($id))
            {
                $this->assign('msg','');
                return $this->fetch();
            }else{
                //修改页面
                $msg = PionnerModel::get($id);
                $this->assign('msg',$msg);
                return $this->fetch();
            }

        }else if (empty($data['id']) ||
                  !isset($data['id'])){
            //新增方法
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $res = $pioneer ->validate('Story') ->save($data);
            if(!empty($res))
            {
                return $this->success("新增成功",Url('Pioneer/story'));
            }else{
                return $this->error($pioneer->getError());
            }
            
        }else{
            //修改方法
            $id = $data['id'];
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $res = $pioneer ->validate('Story') ->save($data,['id' => $id]);
            if(!empty($res))
            {
                return $this ->success("修改成功",Url('Pioneer/story'));
            }else{
                return $this->get_update_error_msg($pioneer ->getError());;
            }
        }
        
    }
    /**
     * 先锋引领 通用删除方法
     */
    public function del(){
        $id = input('get.id');
        if(empty($id))
        {
            return $this ->error('参数错误!');
        }else{
            $res = PionnerModel::where('id',$id) ->update(['status' => -1]);
            if(empty($res))
            {
                return $this ->error('删除失败!');
            }else{
                return $this ->success('删除成功!');
            }
        }
    }

}
