<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/16
 * Time: 15:07
 */
namespace app\admin\controller;

use app\admin\model\Brand as BrandModel;
use app\admin\model\BrandDetail as BrandDetailModel;
use app\admin\model\Picture;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * 品牌项目
 */
class Branddetail extends Admin {
    /**
     * 主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('BrandDetail',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
            'recommend' => array(0=>"否",1=>"是"),
            'push' => array(0=>"否",1=>"是"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data = input('post.');
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $models = new BrandDetailModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $models->validate('BrandDetail')->save($data);
            if($model){
                return $this->success('新增成功!',Url("index"));
            }else{
                return $this->error($models->getError());
            }
        }else{
            $map = array(
                'status' => array('egt',0),
                'type' => 1,
            );
            $list = BrandModel::where($map)->order('id')->column('id,title');
            $this->assign('list',$list);
            $this->assign('msg','');
            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST){
            $data = input('post.');
            $models = new BrandDetailModel();
            $model = $models->validate('BrandDetail')->save($data,['id'=>input('id')]);
            if($model){
                return $this->success('修改成功!',Url("index"));
            }else{
                return $this->get_update_error_msg($models->getError());
            }
        }else{
            //根据id获取课程
            $id = input('id');
            if(empty($id)){
                return $this->error("系统错误,不存在该条数据!");
            }else{
                $msg = BrandDetailModel::get($id);
                $map = array(
                    'type' => 1,
                    'id' => $msg['pid'],
                );
                $model = BrandModel::where($map)->field('id,title')->find();
                if(!empty($msg['images']))
                {
                    $msg['images'] = json_decode($msg['images']);
                }
                $this->assign('model',$model);
                $this->assign('msg',$msg);
            }
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = BrandDetailModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}