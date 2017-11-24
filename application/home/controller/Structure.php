<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\LeaderIntro;
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
        $modelAll = WechatUser::where(['department' => $party,'state' => 1])->order('id')->limit(6)->select();
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
     * 组织架构详情页加载更多
     */
    public function detailmore(){
        $userId = session('userId');
        $tag = WechatUser::getTag($userId);
        $party = input('party');
        $len = input('length');
        $map = array(
            'department' => $party,
            'state' => 1,
        );
        $bg_color=["#b1e3fc", "#aeefef", "#ffa351", "#9393f5", "#cf88f7", "#65abfa", "#ebcffb", "#76f4f0", "#ffcf6e", "#ff8ff4"];
        $modelAll = WechatUser::where($map)->order('id')->limit($len,6)->select();
        foreach($modelAll as $model){
            $model['surname'] = mb_substr($model['name'], 0, 1,'utf-8');
            $model['color'] = $bg_color[substr($model['mobile'], 7, 1)];
            $model['politics_status'] = WechatUser::POLITICS_ARRAY[$model['politics_status']];
            if($tag!=3){
                $model['mobile'] = hide_mobile($model['mobile']);
            }
        }
        if($modelAll){
            return $this->success("加载成功",'',$modelAll);
        }else{
            return $this->error("加载失败");
        }
    }

    /*
    * 组织架构通讯公司页面
    */
    public function communication(){
        $pid = input('id');
        $name = WechatDepartment::where(['id' => $pid])->value('name');
        $modelAll = WechatDepartment::where(['parentid' => $pid,'status' => 1])->order('id')->select();
        $this->assign('name',$name);
        $this->assign('pid',$pid);
        $this->assign('modelAll',$modelAll);
        return $this->fetch();
    }
    /*
    *领导通讯简介页面
    */
    public function pilot(){
        $userId = session('userId');
        $tag = WechatUser::getTag($userId);
        $pid = input('id');
        $modelAll = WechatUser::where(['department' => $pid,'tag' => 3])->order('id')->select();
        foreach ($modelAll as $model){
            $model['politics_status'] = WechatUser::POLITICS_ARRAY[$model['politics_status']];
            if($tag!=3){
                $model['mobile'] = hide_mobile($model['mobile']);
            }
        }
        $this->assign('modelAll',$modelAll);
        return $this->fetch();
    }
    /*
   *领导通讯简介页面
   */
    public function pilotInfo(){
        $userId = session('userId');
        $tag = WechatUser::getTag($userId);
        $pid = input('id');
        $model = WechatUser::where(['id' => $pid])->find();
        $model['politics_status'] = WechatUser::POLITICS_ARRAY[$model['politics_status']];
        if($tag!=3){
            $model['mobile'] = hide_mobile($model['mobile']);
        }
        $info = LeaderIntro::where(['uid' => $model['id']])->find();
        $this->assign('info',$info);
        $this->assign('model',$model);
        return $this->fetch();
    }
}
