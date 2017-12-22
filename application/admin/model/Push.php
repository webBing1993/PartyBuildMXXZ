<?php
/**
 * Created by PhpStorm.
 * User: ali
 * Date: 2017/12/9
 * Time: 9:49
 */
namespace app\admin\model;

class Push extends Base{
    public $insert = array(
        'create_time' => NOW_TIME,
        'status' => 0
    );
    //分类
    public function classify($info) {
        if($info == 1) {
            return "活动发起";
        } elseif($info == 2) {
            return "梦想快讯";
        } elseif($info == 3) {
            return "通知公告";
        } elseif($info == 4) {
            return "重要文件";
        } elseif($info == 5) {
            return "两学一做";
        } elseif($info == 6) {
            return "十九大专题";
        } else {
            return "不明确";
        }
    }
    //找到对应的标题
    public  function title($class,$info) {
        if($class == 1) {
            $wish = \app\admin\model\Wish::where('id',$info) ->field('title,type') ->find();

            return "【活动发起】".$wish->title;
        } elseif($class == 2) {
            $dreamflash = \app\admin\model\DreamFlash::where('id',$info) ->field('title') ->find();

            return "【梦想快讯】".$dreamflash->title;
        } elseif($class == 3) {
            $notice = \app\admin\model\Notice::where('id',$info) ->field('title,type') ->find();
            return "【通知公告】".$notice->title;

        } elseif($class == 4) {
            $matter = \app\admin\model\Matter::where('id',$info) ->field('title,type') ->find();

            return "【重要文件】".$matter->title;
        } elseif($class == 5) {
            $learn = \app\admin\model\Learn::where('id',$info) ->field('title,type') ->find();

            return "【两学一做】".$learn->title;
        } elseif($class == 6) {
            $special = \app\admin\model\Special::where('id',$info) ->field('title,type') ->find();

            return "【十九大专题】".$special->title;
        } else {
            return "不明确";
        }
    }
}