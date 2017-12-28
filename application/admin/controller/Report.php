<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/16
 * Time: 15:07
 */
namespace app\admin\controller;

use app\admin\model\Report as ReportModel;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class
 * @package 网上述职
 */
class Report extends Admin {
    /**
     * 主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Report',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
            'push' => array(0=>"否",1=>"是"),
            'type' => array(1=>"党委",2=>"工会",3=>"团委",4=>"妇联",5=>"科协")
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
            $specialModel = new ReportModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $specialModel->validate('Report')->save($data);
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
            $specialModel = new ReportModel();
            $model = $specialModel->validate('Report')->save($data,['id'=>input('id')]);
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
                $msg = ReportModel::get($id);
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
        $info = ReportModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}