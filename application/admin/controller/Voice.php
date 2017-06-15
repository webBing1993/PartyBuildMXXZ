<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/10
 * Time: 17:01
 */
namespace app\admin\controller;
/*
  * 党员之声  控制器
   */
use app\admin\model\Opinion;

class Voice extends Admin{
    /*
     * 党员之声 主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Opinion',$map);
        foreach($list as $value){
            $value['images'] = json_decode($value['images']);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 党员之声 删除
     */
    public function del(){
        $id = input('id/d');
        $opinion = new Opinion();
        $res = $opinion->where('id',$id)->update(['status' => '-1']);
        if ($res){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
}
