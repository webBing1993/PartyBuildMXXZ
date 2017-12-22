<?php
/**
 * 后台的基本父类
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/3/17
 * Time: 14:50
 */

namespace app\admin\controller;
use app\admin\model\Menu;
use org\Auth;
use org\Page;
use think\Cache;
use think\Config;
use think\Controller;
use think\Input;
use think\Request;

class Admin extends Controller {
    protected $moduleName;
    protected $controllerName;
    protected $actionName;

    /* 空操作，用于输出404页面 */
    public function _empty(){
        $this->redirect('base/error_404'); //暂时
    }

    /* 后台控制器初始化 */
    protected function _initialize(){
        $request = Request::instance();
        $this->moduleName = $request->module();
        $this->controllerName = $request->controller();
        $this->actionName = $request->action();

        // 获取当前用户ID
        if(defined('UID')) return ;
        define('UID',is_login());
        if(!UID ){// 还没登录 跳转到登录页面
            $this->redirect('Base/login');
        }
        /* 读取数据库中的配置 */
        $config = Cache::get('db_config_data');
        if(!$config) {
            $configModel = new \app\admin\model\Config();
            $config = $configModel->lists();
            Cache::set('db_config_data',$config);
        }
        Config::set($config); //添加配置

        /* 是否是超级管理员 */
        define('IS_ROOT', is_administrator());
        if(!IS_ROOT && Config::get('admin_allow_ip')){
            // 检查IP地址访问
            if(!in_array(get_client_ip(),explode(',',Config::get('admin_allow_ip')))){
                return $this->error('403:禁止访问');
            }
        }
        // 检测系统权限
        if(!IS_ROOT){
            $access = $this->accessControl();
            if ( false === $access ) {
                return $this->error('403:禁止访问');
            } elseif (null === $access ) {
                //检测访问权限
                $rule  = strtolower($this->moduleName.'/'.$this->controllerName.'/'.$this->actionName);
                if (!$this->checkRule($rule,[1,2])) {
                    return $this->error('未授权访问!');
                } else {
                    // 检测分类及内容有关的各项动态权限
                    $dynamic = $this->checkDynamic();
                    if( false === $dynamic ) {
                        return $this->error('未授权访问!');
                    }
                }
            }
        }

        $this->assign('__MENU__', $this->getMenus());
        // 判断二级菜单位置
        $menu = Menu::where('url', 'like', '%'.$this->controllerName.'/'.$this->actionName.'%')->order('id')->find();
        $this->assign('subMenu', $menu);

        //后台用户名头像用户组显示
        $this->assign('user', session('user_auth'));
    }
    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     */
    final protected function checkRule($rule, $type = \app\admin\model\AuthRule::RULE_URL, $mode='url'){
        if (IS_ROOT) {
            return true;//管理员允许访问任何页面
        }
        static $Auth = null;
        if (!$Auth) {
            $Auth = new Auth();
        }
        if (!$Auth->check($rule, UID, $type, $mode)){
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     */
    protected function checkDynamic(){
        if(IS_ROOT) {
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     */
    final protected function accessControl(){
        $allow = Config::get('allow_visit');
        $deny  = Config::get('deny_visit');
        $check = strtolower($this->controllerName.'/'.$this->actionName);
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                      url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    final protected function editRow ( $model ,$data, $where , $msg="" ){
        $id    = array_unique((array)I('id',0));
        $id    = is_array($id) ? implode(',',$id) : $id;
        //如存在id字段，则加入该条件
        $fields = M($model)->getFields();
        if(in_array('id',$fields) && !empty($id)){
            $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
        }
        if(!in_array('update_time',$fields)){
            unset($data['update_time']);//如果表中无则删除update_time字段
        }
        $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
        if( M($model)->where($where)->save($data)!==false ) {
            return $this->success($msg['success'],$msg['url'],$msg['ajax']);
        }else{
            return $this->error($msg['error'],$msg['url'],$msg['ajax']);
        }
    }

    /**
     * 禁用条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的 where()方法的参数
     * @param array  $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function forbid ( $model , $where = array() , $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！')){
        $data    =  array('status' => 0);
        return $this->editRow( $model , $data, $where, $msg);
    }

    /**
     * 恢复条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function resume (  $model , $where = array() , $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！')){
        $data    =  array('status' => 1);
        return $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 还原条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function restore (  $model , $where = array() , $msg = array( 'success'=>'状态还原成功！', 'error'=>'状态还原失败！')){
        $data    = array('status' => 1);
        $where   = array_merge(array('status' => -1),$where);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 条目假删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function delete ( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！')) {
        $data['status']         =   -1;
        $data['update_time']    =   NOW_TIME;
        return $this->editRow($model , $data, $where, $msg);
    }

    /**
     * 设置一条或者多条数据的状态
     */
    public function setStatus($Model=CONTROLLER_NAME){
        $ids    =   I('request.ids');
        $status =   I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }

        $map['id'] = array('in',$ids);
        switch ($status){
            case -1 :
                $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));
                break;
            case 0  :
                $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                break;
            case 1  :
                $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     */
    final public function getMenus(){
        //$menus = session('admin_menu_list');
        if(empty($menus)){
            // 获取主菜单
            $menus = Menu::all(function($query){
                $query->where('pid',0)->where('hide',0)->order('sort','asc');
                if(Config::get('develop_mode')){ // 是否开发者模式
                    $query->where('is_dev',0);
                }
            });

            foreach ($menus as $key=>$item) {
                // 错误配置
                if (empty($item['title']) || empty($item['url']) ) {
                    return $this->error('控制器基类$menus属性元素配置有误');
                }

                // 判断主菜单权限
                if (!IS_ROOT && !$this->checkRule(strtolower($this->moduleName.'/'.$item['url']), [1,2]) ) {
                    unset($menus[$key]);
                    continue;   //继续循环
                }
                // 当前菜单位置
//                if (strtolower(CONTROLLER_NAME.'/'.ACTION_NAME)  == strtolower($item['url'])) {
//                    $menus[$key]['class']='current';
//                }
                // 自动添加module
                if (stripos($item['url'], $this->moduleName) !== 0) {
                    $item['url'] = $this->moduleName.'/'.$item['url'];
                }

                // 查找二级菜单
                $where['pid']    = $item['id'];  // 原先pid字段时无法显示菜单。
                $where['hide']  =   0;
                if (!Config::get('DEVELOP_MODE')){ // 是否开发者模式
                    $where['is_dev'] = 0;
                }
                $secondMenus = Menu::all(function($query) use($where) {
                    $query->where($where)->order('sort','asc');
                });
                foreach ($secondMenus as $k=>$value) {
                    if (!IS_ROOT && !$this->checkRule(strtolower($this->moduleName.'/'.$value['url']), [1,2])) {
                        unset($secondMenus[$k]);
                        continue;   //继续循环
                    }
                }
                $menus[$key]['child'] = list_to_tree($secondMenus, 'id', 'pid', 'operater', $item['id']);
            }
            session('admin_menu_list', $menus);
        }

        return $menus;
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     */
    final protected function returnNodes($tree = true){
        static $tree_nodes = array();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        if((int)$tree){
            $list = Menu::all(function($query) {
                $query->field('id,pid,title,url,tip,hide')->order('sort asc');
            });
            foreach ($list as $key => $value) {
                if( stripos($value['url'],$this->moduleName)!==0 ){
                    $list[$key]['url'] = $this->moduleName.'/'.$value['url'];
                }
            }
            foreach ($list as $key=>$value) {
                $list[$key] = $value->toArray();
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            $nodes = Menu::all(function($query) {
                $query->field('title,url,tip,pid')->order('sort asc');
            });
            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],$this->moduleName)!==0 ){
                    $nodes[$key]['url'] = $this->moduleName.'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;

        return $nodes;
    }

    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序)
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(), $order=''){
        $options  =  array();
        $REQUEST  =  (array)input('request/a');
        if (is_string($model)) {
            $model = 'app\admin\model\\'.$model;
            $model =  new $model;
        }

        $pk = $model->getPk();
        if($order===null) {
            //order置空
        } elseif ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        } elseif( $order==='' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk.' desc';
        } elseif($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        $options['where'] = array_filter($where, function($val){
            if($val==='' || $val===null){
                return false;
            } else {
                return true;
            }
        });
        if(empty($options['where'])) {
            unset($options['where']);
            $total = $model::count();
        } else {
            $total = $model::where($options['where'])->count();
        }

        if(isset($REQUEST['r'])) {
            $listRows = (int)$REQUEST['r'];
        } else {
            $listRows = Config::get('list_rows') > 0 ? Config::get('list_rows') : 10;
        }

        $Page = new Page($total, $listRows, $REQUEST);
        if($total>$listRows) {
            $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p = $Page->show();
        $this->assign('_page', $p ? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $Page->firstRow.','.$Page->listRows;

        $list = $model::all(function($query) use($Page, $where, $options) {
            $query->page($Page->nowPage, $Page->listRows);
            if(!empty($options['where'])){
                $query->where($options['where']);
            }
            if(!empty($options['order'])) {
                $query->order($options['order']);
            }
        });

        return $list;
    }

    /**
     * 默认图片
     * $front_pic 封面图片id：1-10
     * $list_pic 列表图片id：35-44
     * $carousel_pic 轮播图片id: 45-54
     */
    public function default_pic(){
        //随机封面图
        $a = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o');
        $front_pic = array_rand($a,1);
        $this->assign('front_pic',$front_pic);

        //随机列表图
        $b = array('16'=>'a','17'=>'b','18'=>'c','19'=>'d','20'=>'e','21'=>'f','22'=>'g', '23'=>'h','24'=>'i','25'=>'j','26'=>'k');
        $list_pic = array_rand($b,1);
        $this->assign('list_pic',$list_pic);

        //随机轮播图
        $c = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o',
            '16'=>'p','17'=>'q','18'=>'r','19'=>'s','20'=>'t','21'=>'u','22'=>'v','23'=>'w','24'=>'x','25'=>'y','26'=>'z');
        $carousel_pic1 = array_rand($c,1);
        $this->assign('carousel_pic1',$carousel_pic1);
        $carousel_pic2 = array_rand($c,1);
        $this->assign('carousel_pic2',$carousel_pic2);
        $carousel_pic3 = array_rand($c,1);
        $this->assign('carousel_pic3',$carousel_pic3);
    }

    /**
     * 判断无修改时错误返回提示
     */
    public function get_update_error_msg($msg){
        if(empty($msg)) {
            return $this->error("未修改内容,更新失败");
        }else{
            return $this->error($msg);
        }

    }
    /**
     * 获取本周时间
     */
    public function week_time() {
        date_default_timezone_set("PRC");        //初始化时区
        $y = date("Y");        //获取当天的年份
        $m = date("m");        //获取当天的月份
        $d = date("d");        //获取当天的号数
        $todayTime= mktime(0,0,0,$m,$d,$y);        //将今天开始的年月日时分秒，转换成unix时间戳
        $time = date("N",$todayTime);        //获取星期数进行判断，当前时间做对比取本周一和上周一时间。
        //$t为本周周一，$s为上周周一
        switch($time){
            case 1: $t = $todayTime;
                break;
            case 2: $t = $todayTime - 86400*1;
                break;
            case 3: $t = $todayTime - 86400*2;
                break;
            case 4: $t = $todayTime - 86400*3;
                break;
            case 5: $t = $todayTime - 86400*4;
                break;
            case 6: $t = $todayTime - 86400*5;
                break;
            case 7: $t = $todayTime - 86400*6;
                break;
            default:
                break;
        }
        return $t;
    }
}