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
        $list = [];
        foreach ($modelAll as $model){
            $list[$model['name']] = WechatDepartment::where(['parentid' => $model['id'],'status' => 1])->field('id,name,mark')->order('id')->select();
        }
//        var_dump(json_decode(json_encode($list)));die;
        $this->assign('list',$list);
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
        $modelAll = WechatUser::where(['department' => $party,'state' => 1])->where("tag", "<>", 3)->order('tag desc,id')->limit(10)->select();
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
        $modelAll = WechatUser::where($map)->where("tag", "<>", 3)->order('tag desc,id')->limit($len,6)->select();
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
    /**
     * 搜索
     */
    public function search(){
        $userId = session('userId');
        $tag = WechatUser::getTag($userId);
        $name = input('val');
        if($name){
            $bg_color=["#b1e3fc", "#aeefef", "#ffa351", "#9393f5", "#cf88f7", "#65abfa", "#ebcffb", "#76f4f0", "#ffcf6e", "#ff8ff4"];
            $list = WechatUser::where('name',['like',"%$name%"],['neq',''])->where("tag", "<>", 3)->order('tag desc,id')->limit(10)->select();  // 模糊查询
            foreach($list as $model){
                $model['surname'] = mb_substr($model['name'], 0, 1,'utf-8');
                $model['color'] = $bg_color[substr($model['mobile'], 7, 1)];
                $model['politics_status'] = WechatUser::POLITICS_ARRAY[$model['politics_status']];
                if($tag!=3){
                    $model['mobile'] = hide_mobile($model['mobile']);
                }
                $model['department'] = WechatDepartment::where(['id' => $model['department']])->value('name'); //  获取用户所在部门
            }
            if($list){
                return $this->success("加载成功",'',$list);
            }else{
                return $this->error("加载失败");
            }
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 搜索-加载更多
     */
    public function searchmore(){
        $userId = session('userId');
        $tag = WechatUser::getTag($userId);
        $name = input('val');
        $len = input('length');
        if($name){
            $bg_color=["#b1e3fc", "#aeefef", "#ffa351", "#9393f5", "#cf88f7", "#65abfa", "#ebcffb", "#76f4f0", "#ffcf6e", "#ff8ff4"];
            $list = WechatUser::where('name',['like',"%$name%"],['neq',''])->where("tag", "<>", 3)->order('tag desc,id')->limit($len,10)->select();  // 模糊查询
            foreach($list as $model){
                $model['surname'] = mb_substr($model['name'], 0, 1,'utf-8');
                $model['color'] = $bg_color[substr($model['mobile'], 7, 1)];
                $model['politics_status'] = WechatUser::POLITICS_ARRAY[$model['politics_status']];
                if($tag!=3){
                    $model['mobile'] = hide_mobile($model['mobile']);
                }
                $model['department'] = WechatDepartment::where(['id' => $model['department']])->value('name'); //  获取用户所在部门
            }
            if($list){
                return $this->success("加载成功",'',$list);
            }else{
                return $this->error("加载失败");
            }
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




    /**
     * 新版本首页
     */
    public function index1(){
        return $this->fetch();
    }
    public function newIndex(){
        return $this->fetch();
    }
}
