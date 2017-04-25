<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use think\Input;
use app\admin\model\Menu as MenuModel;

/**
 * 后台配置控制器
 */
class Menu extends Admin
{
    /**
     * 后台菜单首页
     */
    public function index(){
        $pid  = input('pid', 0);
        if($pid) {
            $data = MenuModel::get($pid);
            $this->assign('data',$data);
        }
        $title = trim(input('title'));

        $map['pid'] =   $pid;
        if($title) $map['title'] = array('like',"%{$title}%");
        $list = MenuModel::all(function($query) use($map) {
            $query->where($map)->order('sort asc,id asc');
        });
        int_to_string($list, array(
            'hide' => array(1=>'是', 0=>'否'),
            'is_dev' => array(1=>'是', 0=>'否')
        ));
        $this->assign('list',$list);

        // 组合上级菜单
        $all_menu = MenuModel::column('id,title');
        foreach ($list as &$key) {
            if($key['pid']){
                $key['up_title'] = $all_menu[$key['pid']];
            }
        }

        // 记录当前列表页的cookie
        cookie('__forward__',$_SERVER['REQUEST_URI']);

        // 所以菜单的下拉树
        $MenuModel = new MenuModel();
        $menus = $MenuModel->all();
        foreach ($menus as $key=>$value) {
            $menus[$key] = $value->toArray();
        }
        $menus = $MenuModel->toFormatTree($menus);
        $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
        $this->assign('Menus', $menus);
        $this->assign("pid", $pid);

        return $this->fetch();
    }

    /**
     * 新增菜单
     */
    public function add(){
        $MenuModel = new MenuModel();
        unset($_POST['id']);    //过滤ID空值
        $MenuModel->data($_POST);
        $result = $MenuModel->validate(true)->save();

        if($result){
            session('ADMIN_MENU_LIST',null);
            //记录行为
            //action_log('update_menu', 'Menu', $id, UID);
            return $this->success('新增成功', cookie('__forward__'));
        } else {
            return $this->error($MenuModel->getError());
        }
    }

    /**
     * 编辑配置
     */
    public function edit($id = 0){
        $MenuModel = new MenuModel();
        $map['id'] = $id;
        $result = $MenuModel->validate(true)->save($_POST, $map);

        if($result){
            return $this->success("更新成功",cookie('__forward__'));
        }else{
            return $this->error($MenuModel->getError());
        }
    }

    /**
     * 删除后台菜单
     */
    public function del(){
        $id = array_unique((array)input('id/a',0));

        if ( empty($id) ) {
            return $this->error('请选择要操作的数据!');
        }
        if(MenuModel::destroy($id)){
            session('ADMIN_MENU_LIST',null);
            //记录行为
            //action_log('update_menu', 'Menu', $id, UID);
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败！');
        }
    }

    /**
     * @param $id
     * @param int $value
     * @return mixed
     */
    public function toogleHide($id, $value = 1){
        session('ADMIN_MENU_LIST',null);
        $result = MenuModel::where('id', $id)->update(['hide' => $value]);
        if($result) {
            return $this->success("操作成功");
        } else {
            return $this->error("操作成功");
        }
    }

    /**
     * @param $id
     * @param int $value
     * @return mixed
     */
    public function toogleDev($id, $value = 1){
        session('ADMIN_MENU_LIST',null);
        $result = MenuModel::where('id', $id)->update(['is_dev' => $value]);
        if($result) {
            return $this->success("操作成功");
        } else {
            return $this->error("操作成功");
        }
    }

    /**
     * 菜单排序
     */
    public function sort(){
        if (IS_POST){
            $ids = input('ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $result = MenuModel::where('id', $value)->update(['sort' => $key+1]);
            }
            if($result !== false){
                session('ADMIN_MENU_LIST',null);
                return $this->success('排序成功！');
            }else{
                return $this->error('排序失败！');
            }
        } else {
            $ids = input('ids');
            $pid = input('pid');
            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)) {
                $map['id'] = array('in',$ids);
            } else {
                if($pid !== '') $map['pid'] = $pid;
            }

            $list = MenuModel::all(function($query) use($map){
                $query->where($map)->field('id,title')->order('sort asc,id asc');
            });
            $this->assign('list', $list);
            
            return $this->fetch();
        }
    }

    /**
     * 获取菜单详情
     * @param int $id
     */
    public function getInfo($id = 0) {
        $menu = MenuModel::get($id);
        return json_encode($menu);
    }
}