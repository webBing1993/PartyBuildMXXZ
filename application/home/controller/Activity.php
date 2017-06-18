<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/6/17
 * Time: 17:40
 */
namespace app\home\controller;
use app\home\model\Wish as  wishModel;
/**
 *  活动发起  控制器
 */
class Activity extends Base{
    /*
     * 活动发起 主页 
     */
    public function index(){
        return $this ->fetch();
    }
    /*
     * 投票  发布
     */
    public function publish(){
        $data = input('post.');
        $uid = session('userId');
        if(empty($data))
        {
            return $this ->fetch();
        }else{
            $wishModel = new wishModel();
            $data['type'] = 2;
            $data['images'] = json_encode($data['images']);
            $data['publisher'] = get_name($uid);
            $data['create_time'] = time();
            $data['create_user'] = $uid;
            $data['status'] = 0;
            $wishModel ->data($data) ->save();
            if($wishModel ->id)
            {
                return $this ->success('发布成功!');
            }else{
                return $this ->error('发布失败!');
            }
        }
    }
}