<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/21
 * Time: 14:41
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPWechat;
use app\admin\model\News as NewsModel;
use think\Config;
use app\admin\model\Opinion as OpinionModel;
/**
 * Class News
 * @package 新闻动态 控制器
 */
class News extends Admin {

    /**
     * 主页列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Opinion',$map);
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 新闻添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            if(empty($data['id']))
            {
                unset($data['id']);
            }
//            $data['images'] = json_encode($data['images']);
            $data['create_time'] = time();
            $data['comments'] = 0;
            $data['likes'] = 0;
            $newModel = new OpinionModel();
            $info = $newModel->validate('opinion')->save($data);
            if($info) {
                return $this->success("新增成功",Url('News/index'));
            }else{
                return $this->error($newModel->getError());
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
        if(IS_POST) {
            $data = input('post.');
            $newModel = new OpinionModel();
            $info = $newModel->validate('opinion')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("News/index"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
//            $this->default_pic();
            $id = input('id');
            $msg = OpinionModel::get($id);

            if(!empty($msg['images']))
            {
                $msg['images'] = json_decode($msg['images']);
            }
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除功能
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = OpinionModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }

    }
}