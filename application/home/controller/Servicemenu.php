<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\WechatUser;
use app\home\model\ServiceItems;

class Servicemenu extends Base{
    /*
     * 主页
     */
    public function index(){
        $modelAll = ServiceItems::where('1=1')->order('department_id, id')->select();
        $this->assign('modelAll',$modelAll);

        /*$modelAll = [];
        $result = ServiceItems::where('1=1')->group('department_id')->order('department_id')->column('department_id');
        foreach ($result as $key => $department_id){
            $modelAll[$key]['name'] = ServiceItems::where(['department_id' => $department_id])->value('department');
            $modelAll[$key]['data'] = ServiceItems::where(['department_id' => $department_id])->order('id')->column('id,name');
        }*/
//        var_dump($modelAll);die;
        $this->assign('modelAll',$modelAll);
        return $this->fetch();
    }
    /*
     * 组织架构详情页
     */
    public function detail(){
        $pid = input('id');
        $model = ServiceItems::where(['id' => $pid])->find();
        if($model['tel2']){
            $model['tel'] = explode('-', $model['tel2'])[1];
        }else{
            $model['tel'] = '';
        }
        $this->assign('model',$model);
        return $this->fetch();
    }
    /**
     * 二次开发主页
     *
     */
    public function newindex(){
        return $this->fetch();
    }
}
