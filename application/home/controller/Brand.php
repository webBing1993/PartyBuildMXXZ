<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/22
 * Time: 15:58
 */

namespace app\home\controller;
use app\home\model\WechatUser;
use think\Controller;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\Brand as BrandModel;
use app\home\model\BrandDetail as BrandDetailModel;
use app\home\model\Like;

/**
 * Class Brand
 * @package 品牌项目
 */
class Brand extends Base{
    /*
     * 主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = BrandModel::where($map)->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 详情页
     */
    public function branddetail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $map = array(
            'status' => ['egt',0],
            'pid' => $id,
        );
        $list = BrandDetailModel::where($map)->order('id desc')->select();
        foreach ($list as $model){
            $model['images'] = json_decode($model['images'], true);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function detail(){
        return $this->fetch();
    }


}