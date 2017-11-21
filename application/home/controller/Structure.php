<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\WechatTest;
use app\home\model\WechatUser;
use app\home\model\WechatDepartment;

class Structure extends Base{
    /*
     * 组织架构主页
     */
    public function index(){
        $modelAll = WechatDepartment::where(['parentid' => 1,'status' => 1])->order('id')->select();
        $this->assign('modelAll',$modelAll);
        return $this->fetch();
    }
    /*
     * 组织架构详情页
     */
    public function detail(){
        $this ->checkAnonymous();
        $userId = session('userId');
        $tag = WechatUser::getTag($userId);
        $party = input('party');
        $modelAll = WechatUser::where(['department' => $party,'state' => 1])->order('id')->select();
        $bg_color=["#b1e3fc", "#aeefef", "#ffa351", "#9393f5", "#cf88f7", "#65abfa", "#ebcffb", "#76f4f0", "#ffcf6e", "#ff8ff4"];
        foreach ($modelAll as $model){
            $model['surname'] = mb_substr($model['name'], 0, 1,'utf-8');
            $model['color'] = $bg_color[substr($model['mobile'], 7, 1)];
            $model['politics_status'] = WechatUser::POLITICS_ARRAY[$model['politics_status']];
            if($tag!=3){
                $model['mobile'] = hide_mobile($model['mobile']);
            }
        }
        $this->assign('modelAll',$modelAll);
        $this->assign('party',$party);
        return $this->fetch();
    }
    /*
    * 组织架构通讯公司页面
    */
    public function communication(){
        $pid = input('id');
        $modelAll = WechatDepartment::where(['parentid' => $pid,'status' => 1])->order('id')->select();
        $this->assign('modelAll',$modelAll);
        return $this->fetch();
    }
    /*
    *领导通讯简介页面
    */
    public function pilot(){
        return $this->fetch();
    }
    /*
   *领导通讯简介页面
   */
    public function pilotInfo(){
        return $this->fetch();
    }
}
