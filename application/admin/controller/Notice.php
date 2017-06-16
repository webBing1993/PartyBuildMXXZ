<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 10:29
 */

namespace app\admin\controller;

use app\admin\model\Notice as NoticeModel;
use app\admin\model\Picture;

/**
 * Class Notice
 * @package 支部活动
 */
class Notice extends Admin {
    /**
     * 相关通知
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布"),
            'recommend' => array(0=>"否",1=>"是")
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 相关通知 添加
     */
    public function indexadd(){
        if(IS_POST) {
            $data = input('post.');
            $result = $this->validate($data,'Notice');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                if ($data['recommend'] == 1){
                    $res = $noticeModel->save($data);
                    if ($res){
                        $map = array(
                            'status' => 0,
                            'id' => array("neq",$res)
                        );
                        $ree = $noticeModel->where($map)->count();
                        if ($ree != 0){
                            $noticeModel->where($map)->update(['recommend' => 0]);
                        }
                        return $this->success("新增通知成功",Url('Notice/index'));
                    }else{
                        return $this->error($noticeModel->getError());
                    }
                }else{
                    $res = $noticeModel->save($data);
                    if ($res){
                        return $this->success("新增通知成功",Url('Notice/index'));
                    }else{
                        return $this->error($noticeModel->getError());
                    }
                }
            }
        }else {
            return $this->fetch();
        }
    }
    /**
     * 相关通知 修改
     */
    public function indexedit(){
        if(IS_POST) {
            $data = input('post.');
            $result = $this->validate($data,'Notice');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                if ($data['recommend'] == 1){
                    $res = $noticeModel->save($data,['id'=>$data['id']]);
                    if ($res){
                        $map = array(
                            'status' => 0,
                            'id' => array('neq',$data['id'])
                        );
                        $ree = $noticeModel->where($map)->count();
                        if ($ree != 0){
                            $noticeModel->where($map)->update(['recommend' => 0]);
                        }
                        return $this->success("修改通知成功",Url('Notice/index'));
                    }else{
                        return $this->error($noticeModel->getError());
                    }
                }else{
                    $res = $noticeModel->save($data,['id'=>$data['id']]);
                    if ($res){
                        return $this->success("修改通知成功",Url('Notice/index'));
                    }else{
                        return $this->error($noticeModel->getError());
                    }
                }
            }
        }else{
            $id = input('id');
            $msg = NoticeModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $map['status'] = "-1";
        $info = NoticeModel::where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}