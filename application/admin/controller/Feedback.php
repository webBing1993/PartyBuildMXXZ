<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/3/28
 * Time: 10:37
 */
namespace app\admin\controller;
use app\admin\model\WechatDepartment;
use app\admin\model\WechatUser;
use app\admin\model\Feedback as FeedbackModel;
use think\Config;
use com\wechat\TPQYWechat;
class Feedback extends Admin{
    /*
     * 意见反馈 列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Feedback',$map);
        foreach($list as $value){
            $User = WechatUser::where('userid',$value['userid'])->find();
            $value['name'] = $User['name'];
            $Depart = WechatDepartment::where('id',$User['department'])->find();
            $value['department'] = $Depart['name'];
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 查看 意见反馈 详情页
     */
    public function detail(){
        $id = input('post.id');
        $detail = FeedbackModel::where(['id' => $id])->find();
        return $detail['content'];
    }
    /*
     * 意见反馈 删除
     */
    public function del(){
        $id = input('param.id');
        $update['status'] = '-1';
        $res = FeedbackModel::where('id',$id)->update($update);
        if ($res){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
}