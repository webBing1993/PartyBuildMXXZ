<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/16
 * Time: 15:07
 */
namespace app\admin\controller;

use app\admin\model\Style as StyleModel;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class
 * @package 网上述职个人风采
 */
class Style extends Admin {
    /**
     * 主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Style',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
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
            $specialModel = new StyleModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $specialModel->validate('Style')->save($data);
            if($model){
                return $this->success('新增成功!',Url("index"));
            }else{
                return $this->error($specialModel->getError());
            }
        }else{
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
            $specialModel = new StyleModel();
            $model = $specialModel->validate('Style')->save($data,['id'=>input('id')]);
            if($model){
                return $this->success('修改成功!',Url("index"));
            }else{
                return $this->get_update_error_msg($specialModel->getError());
            }
        }else{

            //根据id获取课程
            $id = input('id');
            if(empty($id)){
                return $this->error("系统错误,不存在该条数据!");
            }else{
                $msg = StyleModel::get($id);
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
        $info = StyleModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}