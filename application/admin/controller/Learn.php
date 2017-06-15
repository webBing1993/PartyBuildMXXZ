<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/16
 * Time: 15:07
 */
namespace app\admin\controller;

use app\admin\model\Learn as LearnModel;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Learn
 * @package 两学一做
 */
class Learn extends Admin {
    /**
     * 主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
            'type'=> array('in',[1,2])
        );
        $list = $this->lists('Learn',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
            'recommend' => array(0=>"否",1=>"是"),
            'push' => array(0=>"否",1=>"是"),
            'type' => array(1=>"视频课程",2=>"文章课程")
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
            if($data['type'] == 1){
                if($data['video_path'] == "" && $data['net_path'] == ""){
                    return $this->error("请上传视频文件或网址，如文件过大，请耐心等待..");
                }
            }
            $learnModel = new LearnModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $learnModel->validate('Learn')->save($data);
            if($model){
                return $this->success('新增成功!',Url("Learn/index"));
            }else{
                return $this->error($learnModel->getError());
            }
        }else{
            $this->default_pic();
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
            if($data['type'] == 1){
                if($data['video_path'] == "" && $data['net_path'] == ""){
                    return $this->error("请上传视频文件或网址，如文件过大，请耐心等待..");
                }
            }
            $learnModel = new LearnModel();
            $model = $learnModel->validate('Learn')->save($data,['id'=>input('id')]);
            if($model){
                return $this->success('修改成功!',Url("Learn/index"));
            }else{
                return $this->get_update_error_msg($learnModel->getError());
            }
        }else{
            $this->default_pic();
            //根据id获取课程
            $id = input('id');
            if(empty($id)){
                return $this->error("系统错误,不存在该条数据!");
            }else{
                $msg = LearnModel::get($id);
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
        $info = LearnModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}