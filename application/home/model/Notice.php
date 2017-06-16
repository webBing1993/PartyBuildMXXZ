<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;


use think\Model;

class Notice extends Model {
    //首页获取已推送的数据
    public function get_list($length,$len){
        $details = $this ->where(['status' => 1]) ->order('create_time desc') ->limit($length,$len) ->select();
        return $details;
    }
}