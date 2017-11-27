<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/12
 * Time: 10:46
 */
namespace app\admin\controller;
use app\admin\model\WechatDepartment;
use app\admin\model\WechatUser;
use app\admin\model\Wish;
use app\admin\model\WishReceive;
 /**
  * 活动发起  控制器
  */
 class Activity extends Admin{
     /*
      * 活动 列表 主页
      */
     public function index(){
         $map = array(
             'type' => 1, // 活动
             'status' => array('egt',0),
         );
         $list = $this->lists('Wish',$map);
         int_to_string($list,array(
             'status' => array(0=>"已发布",1=>"已发布"),
             'recommend' => array(0=>"否",1=>"是"),
             'push' => array(0=>"否",1=>"是"),
         ));
         /*foreach ($list as $key => $value) {
             $msg = array(
                 'rid' => $value['id'],
                 'status' => 0,
             );
             $info = WishReceive::where($msg)->select();
             if($info) {
                 $value['is_enroll'] = 1;
             }else{
                 $value['is_enroll'] = 0;
             }
         }*/
         $this->assign('list',$list);
         return $this->fetch();
     }
     /**
      * 活动  添加  修改
      */
     public function edit() {
         $id = input('id/d');
         if ($id){
             // 修改
             if(IS_POST) {
                 $data = input('post.');
                 $data['update_time'] = time();
                 $data['update_user'] = $_SESSION['think']['user_auth']['id'];
                 $wishModel = new Wish();
                 $info = $wishModel->validate('wish')->save($data,['id'=>$data['id']]);
                 if($info) {
                     return $this->success("修改成功",Url('Activity/index'));
                 }else{
                     return $this->get_update_error_msg($wishModel->getError());
                 }
             }else {
                 $id = input('id');
                 $msg = Wish::get($id);
                 $this->assign('msg',$msg);
                 return $this->fetch();
             }
         }else{
             // 添加
             if(IS_POST) {
                 $data = input('post.');
                 unset($data['id']);
                 $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                 $wishModel = new Wish();
                 $model = $wishModel->validate('wish')->save($data);
                 if($model) {
                     return $this->success("新增成功",Url('Activity/index'));
                 }else{
                     return $this->get_update_error_msg($wishModel->getError());
                 }
             }else {
                 $this->assign('msg',null);
                 return $this->fetch('edit');
             }
         }
     }
     /**
      * 领取列表
      */
     public function receive() {
         $id = input('id');
         $map = array(
             'rid' => $id,
             'status' => array('egt',0),
         );
         $list = $this->lists('WishReceive',$map);
         $this->assign('list',$list);
         return $this->fetch();
     }
     /**
      * 活动  删除
      */
     public function del() {
         $id = input('id');
         $map = array(
             'status' => -1,
         );
         $wishModel = new Wish();
         $model = $wishModel->where(['id' => $id,'type' => 1])->update($map);
         if($model) {
             $result = WishReceive::where('rid',$id)->count();
             if ($result != 0){
                 $res = WishReceive::where('rid',$id)->update($map);
                 if ($res){
                     return $this->success("删除成功");
                 }else{
                     return $this->error("删除失败");
                 }
             }else{
                 return $this->success("删除成功");
             }
         }else{
             return $this->error("删除失败");
         }
     }
     /*
      * 投票  主页
      */
     public function vote(){
         $map = array(
             'type' => 2 ,  // 投票
             'status' => array('egt',0),
         );
         $list = $this->lists('Wish',$map);
         foreach($list as $value){
             $User = WechatUser::where('userid',$value['publisher'])->field('name,department')->find();
             $value['name'] = $User['name'];  // 获取发布人 姓名
             $Department = WechatDepartment::where('id',$User['department'])->field('name')->find();
             $value['department'] = $Department['name'];  // 获取发布人 组别
             $value['images'] = json_decode($value['images']);
         }
         $this->assign('list',$list);
         return $this->fetch();
     }
     /*
      * 投票 删除
      */
     public function votedel(){
         $id = input('id/d');
         $opinion = new Wish();
         $res = $opinion->where(['id' => $id,'type' => 2])->update(['status' => '-1']);
         if ($res){
             return $this->success('删除成功');
         }else{
             return $this->error('删除失败');
         }
     }
 }