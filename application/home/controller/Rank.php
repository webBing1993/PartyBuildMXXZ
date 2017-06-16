<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/5
 * Time: 13:53
 */
namespace app\home\controller;
use app\home\model\WechatUser;
use app\home\model\Like;
use app\home\model\Comment;
use app\home\model\Answers;
use app\home\model\Browse;
use think\Config;
use think\Db;

class Rank extends Base{
    /**
     * 个人积分排行
     */
    public function index(){
        //判断游客登陆
        $this ->anonymous();
        $wechatModel = new WechatUser();
        $userId = session('userId');
        //个人信息
        $personal = $wechatModel::where('userid',$userId)->find();
        //总榜
        $con['score'] = array('neq',0);
        $all  = $wechatModel->where($con)->order('score desc')->limit(60)->select();
        foreach ($all as $k => $value){
            //当前用户排名
            if($value['userid'] == $userId){
                $personal['rank'] = $k+1;
            }
        }
        $this->assign('all',$all);
        $this->assign('personal',$personal);

        //获取周榜信息
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
        }
        $map = array(
            'create_time' => array('egt',$t),
            'score' =>  array('egt',1)
        );
        //本周浏览
        $browse = Browse::where($map)->select();
        $list1 = array();
        foreach ($browse as $value){
            $k = $value['user_id'];
            $list1[$k][] = $value;
        }
        $new1 = array();
        foreach ($list1 as $u => $val){
            $count = count($list1[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new1[] = $cen;
        }
        //本周评论
        $comment = Comment::where($map)->select();
        $list2 = array();
        foreach ($comment as $value){
            $k = $value['uid'];
            $list2[$k][] = $value;
        }
        $new2 = array();
        foreach ($list2 as $u => $val){
            $count = count($list2[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new2[] = $cen;
        }
        //本周点赞
        $like = Like::where($map)->select();
        $list3 = array();
        foreach ($like as $value){
            $k = $value['uid'];
            $list3[$k][] = $value;
        }
        $new3 = array();
        foreach ($list3 as $u => $val){
            $count = count($list3[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new3[] = $cen;
        }
        //本周答题
        $answers = Answers::where($map)->select();
        $list4 = array();
        foreach ($answers as $value){
            $k = $value['userid'];
            $list4[$k][] = $value;
        }
        $new4 = array();
        foreach ($list4 as $u => $val){
            $cen['userid'] = $u;
            $score = 0;
            foreach ($val as $k){
                $score += $k['score'];
            }
            $cen['score'] = $score;
            $new4[] = $cen;
        }

        //先第一组、第二组比较，相同累加，不同删除，合并到过渡数组center中
        $center = array();
        foreach ($new1 as $k1 => $v1){
            foreach ($new2 as $k2 => $v2){
                if($v1['userid'] == $v2['userid']){
                    $cen['userid'] = $v2['userid'];
                    $cen['score'] = $v1['score'] + $v2['score'];
                    $center[] = $cen;
                    unset($new2[$k2]);
                    unset($new1[$k1]);
                }else{

                }
            }
        }
        $center = array_merge($center,$new2,$new1);     //unset掉重复值，重组成新数组

        //过渡数组与第三组比较同里，结果存入final2
        $final2 = array();
        foreach ($center as $f => $val){
            foreach ($new3 as $k3 => $v3){
                if($val['userid'] == $v3['userid']){
                    $cen['userid'] = $v3['userid'];
                    $cen['score'] = $val['score'] + $v3['score'];
                    $final2[] = $cen;
                    unset($new3[$k3]);
                    unset($center[$f]);
                }
            }
        }

        $final2 = array_merge($final2,$new3,$center); //unset掉重复值，重组成新数组

        //过渡数组与第四组比较同里，结果存入final3
        $final3 = array();
        foreach ($final2 as $f => $val){
            foreach ($new4 as $k4 => $v4){
                if($val['userid'] == $v4['userid']){
                    $cen['userid'] = $v4['userid'];
                    $cen['score'] = $val['score'] + $v4['score'];
                    $final3[] = $cen;
                    unset($new4[$k4]);
                    unset($final2[$f]);
                }
            }
        }

        $final3 = array_merge($final3,$new4,$final2); //unset掉重复值，重组成新数组

        //倒序，字段score排序
        $sort = array(
            'direction' => 'SORT_DESC',
            'field' => 'score',
        );
        $arrSort = array();
        foreach ($final3 as $k => $v){
            foreach ($v as $key => $value){
                $arrSort[$key][$k] = $value;
            }
        }
        if($sort['direction'] && $arrSort){
            array_multisort($arrSort[$sort['field']],constant($sort['direction']),$final3);
        }

        //最终重组，限制输出20名，获取用户个人信息
        $final = array();
        foreach ($final3 as $key => $value){
            if($key<20){
                $user = WechatUser::where('userid',$value['userid'])->find();
                $value['name'] = $user['name'];
                $final[$key] = $value;
            }
        }
        $this->assign('week',$final);

        //获取季榜信息
        date_default_timezone_set("PRC");        //初始化时区
        $season = ceil((date('n'))/3);//当月是第几季度
        $start = mktime(0, 0, 0,$season*3-3+1,1,date('Y'));
        $end = mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y'));
        $map = array(
            'create_time' => array('between',[$start,$end]),    //在时间区间内
            'score' =>  array('egt',1)
        );

        //本月浏览
        $browse_m = Browse::where($map)->select();
        $list1_m = array();
        foreach ($browse_m as $value){
            $k = $value['user_id'];
            $list1_m[$k][] = $value;
        }
        $new1_m = array();
        foreach ($list1_m as $u => $val){
            $count = count($list1_m[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new1_m[] = $cen;
        }

        //本月评论
        $comment_m = Comment::where($map)->select();
        $list2_m = array();
        foreach ($comment_m as $value){
            $k = $value['uid'];
            $list2_m[$k][] = $value;
        }
        $new2_m = array();
        foreach ($list2_m as $u => $val){
            $count = count($list2_m[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new2_m[] = $cen;
        }

        //本月点赞
        $like_m = Like::where($map)->select();
        $list3_m = array();
        foreach ($like_m as $value){
            $k = $value['uid'];
            $list3_m[$k][] = $value;
        }
        $new3_m = array();
        foreach ($list3_m as $u => $val){
            $count = count($list3_m[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new3_m[] = $cen;
        }
        //本月答题
        $answers_m = Answers::where($map)->select();
        $list4_m = array();
        foreach ($answers_m as $value){
            $k = $value['userid'];
            $list4_m[$k][] = $value;
        }
        $new4_m = array();
        foreach ($list4_m as $u => $val){
            $cen['userid'] = $u;
            $score = 0;
            foreach ($val as $k){
                $score += $k['score'];
            }
            $cen['score'] = $score;
            $new4_m[] = $cen;
        }


        $center_m = array();
        foreach ($new1_m as $k1 => $v1){
            foreach ($new2_m as $k2 => $v2){
                if($v1['userid'] == $v2['userid']){
                    $cen['userid'] = $v2['userid'];
                    $cen['score'] = $v1['score'] + $v2['score'];
                    $center_m[] = $cen;
                    unset($new2_m[$k2]);
                    unset($new1_m[$k1]);
                }else{

                }
            }
        }
        $center_m = array_merge($center_m,$new2_m,$new1_m);     //unset掉重复值，重组成新数组

        //过渡数组与第三组比较同里，结果存入final2_m
        $final2_m = array();
        foreach ($center_m as $f => $val){
            foreach ($new3_m as $k3 => $v3){
                if($val['userid'] == $v3['userid']){
                    $cen['userid'] = $v3['userid'];
                    $cen['score'] = $val['score'] + $v3['score'];
                    $final2_m[] = $cen;
                    unset($new3_m[$k3]);
                    unset($center_m[$f]);
                }
            }
        }
        $final2_m = array_merge($final2_m,$new3_m,$center_m);

        //过渡数组与第四组比较同里，结果存入final3
        $final3_m = array();
        foreach ($final2_m as $f => $val){
            foreach ($new4_m as $k4 => $v4){
                if($val['userid'] == $v4['userid']){
                    $cen['userid'] = $v4['userid'];
                    $cen['score'] = $val['score'] + $v4['score'];
                    $final3_m[] = $cen;
                    unset($new4_m[$k4]);
                    unset($final2_m[$f]);
                }
            }
        }

        $final3_m = array_merge($final3_m,$new4_m,$final2_m); //unset掉重复值，重组成新数组

        //倒序，字段score排序
        $sort_m = array(
            'direction' => 'SORT_DESC',
            'field' => 'score',
        );
        $arrSort_m = array();
        foreach ($final3_m as $k => $v){
            foreach ($v as $key => $value){
                $arrSort_m[$key][$k] = $value;
            }
        }
        if($sort_m['direction'] && $arrSort_m){
            array_multisort($arrSort_m[$sort_m['field']],constant($sort_m['direction']),$final3_m);
        }
        //最终重组，限制输出20名，获取用户个人信息
        $final_m = array();
        foreach ($final3_m as $key => $value){
            if($key<20){
                $user = WechatUser::where('userid',$value['userid'])->find();
                $value['name'] = $user['name'];
                $final_m[$key] = $value;
            }
        }
        $this->assign('month',$final_m);

        return $this->fetch();
    }
    /**
     * 部门积分排行
     */
    public function department(){
        //判断游客登陆
        $this ->anonymous();
        $userId = session('userId');
            //所在部门名称
            if($userId !== 'visitor'){
                $personal = Db::table('pb_wechat_user')
                    ->alias('a')
                    ->join('pb_wechat_department b','a.department = b.id')
                    ->field('a.userid,b.name,b.id')
                    ->where('a.userid',$userId)
                    ->find();
                //部门总分
                $personal['sum'] = WechatUser::where('department',$personal['id']) ->sum('score');
            }

            //
            $dpall = Db::table('pb_wechat_department')
                ->alias('a')
                ->join('pb_wechat_user b','b.department = a.id','LEFT')
                ->field('a.id,a.name,b.score')
                ->select();
            //合并相同数组的数据并值累加
            $item = array();
            foreach($dpall as $k=>$v){
                //没有送积分
    //            if ($v['score'] != 0){
    //                $v['score'] += 10;
    //            }
                if(!isset($item[$v['id']])){
                    $item[$v['id']]=$v;
                }else{
                    $item[$v['id']]['score']+=$v['score'];
                }
            }
            //倒序，字段score排序
            $sort = array(
                'direction' => 'SORT_DESC',
                'field' => 'score',
            );
            $arrSort = array();
            foreach ($item as $k => $v){
                foreach ($v as $key => $value){
                    $arrSort[$key][$k] = $value;
                }
            }
            if($sort['direction'] && $arrSort){
                array_multisort($arrSort[$sort['field']],constant($sort['direction']),$item);
            }
            //获取头部信息，并取20名用户
            $new = array();
            foreach ($item as $key=>$value){
                if($userId !== 'visitor') {
                    if ($value['id'] == $personal['id']) {
                        $personal['score'] = $value['score'];
                    }
                }
                if($value['score'] != 0){
                    $new[$key] = $value;
                }
            }

            $last = array();
            foreach ($new as $k => $v){
                if($userId !== 'visitor') {
                    if ($v['id'] == $personal['id']) {
                        $personal['score'] = $v['score'];
                        $personal['rank'] = $k + 1;
                    }
                }
                if($k < 20){ //取小于20名排行
                        $last[$k] = $v;
                }
            }

            $this->assign('all',$last);
            if($userId !== 'visitor'){
                $this ->assign('deparment',$personal);
            }

        //获取周榜信息
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
        }
        $map = array(
            'create_time' => array('egt',$t),
            'score' => array('egt',1)
        );
        //本周浏览
        $browse = Browse::where($map)->select();
        $list1 = array();
        foreach ($browse as $value){
            $k = $value['user_id'];
            $list1[$k][] = $value;
        }
        $new1 = array();
        foreach ($list1 as $u => $val){
            $count = count($list1[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new1[] = $cen;
        }
        //本周评论
        $comment = Comment::where($map)->select();
        $list2 = array();
        foreach ($comment as $value){
            $k = $value['uid'];
            $list2[$k][] = $value;
        }
        $new2 = array();
        foreach ($list2 as $u => $val){
            $count = count($list2[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new2[] = $cen;
        }
        //本周点赞
        $like = Like::where($map)->select();
        $list3 = array();
        foreach ($like as $value){
            $k = $value['uid'];
            $list3[$k][] = $value;
        }
        $new3 = array();
        foreach ($list3 as $u => $val){
            $count = count($list3[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new3[] = $cen;
        }
        // 本周答题
        $answer = Answers::where($map)->select();
        $list4 = array();
        foreach($answer as $value){
            $k = $value['userid'];
            $list4[$k][] = $value;
        }
        $news4 = array();
        foreach($list4 as $u => $val){
            $count = 0;
            foreach($val as $valu){
                $count += $valu->score;
            }
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $news4[] = $cen;
        }

        //先第一组、第二组比较，相同累加，不同删除，合并到过渡数组center中
        $center = array();
        foreach ($new1 as $k1 => $v1){
            foreach ($new2 as $k2 => $v2){
                if($v1['userid'] == $v2['userid']){
                    $cen['userid'] = $v2['userid'];
                    $cen['score'] = $v1['score'] + $v2['score'];
                    $center[] = $cen;
                    unset($new2[$k2]);
                    unset($new1[$k1]);
                }else{

                }
            }
        }
        $center = array_merge($center,$new2,$new1);     //unset掉重复值，重组成新数组

        //过渡数组与第三组比较同里，结果存入final2
        $final2 = array();
        foreach ($center as $f => $val){
            foreach ($new3 as $k3 => $v3){
                if($val['userid'] == $v3['userid']){
                    $cen['userid'] = $v3['userid'];
                    $cen['score'] = $val['score'] + $v3['score'];
                    $final2[] = $cen;
                    unset($new3[$k3]);
                    unset($center[$f]);
                }
            }
        }
        $final2 = array_merge($final2,$new3,$center);

        // final2 与第四组比较同理,结果存入 final3
        $final3 = array();
        foreach($final2 as $y => $l){
            foreach($news4 as $k4 => $v4){
                if($l['userid'] == $v4['userid']){
                    $cen['userid'] = $v4['userid'];
                    $cen['score'] = $l['score'] + $v4['score'];
                    $final3[] = $cen;
                    unset($news4[$k4]);
                    unset($final2[$y]);
                }
            }
        }
        $final3 = array_merge($final3,$news4,$final2);

        $finals = array();
        foreach ($final3 as $key=>$value){
            $department = Db::table('pb_wechat_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.department = b.id','LEFT')
                ->where('a.userid',$value['userid'])
                ->find();
            $value['id'] = $department['id'];
            $value['name'] = $department['name'];
            unset($value['userid']);
            $finals[$key] = $value;
        }
        //合并相同值，获得部门值
        $item = array();
        foreach($finals as $k=>$v){
            if(!isset($item[$v['id']])){
                $item[$v['id']]=$v;
            }else{
                $item[$v['id']]['score']+=$v['score'];
            }
        }

        //倒序，字段score排序
        $arrSort = array();
        foreach ($item as $k => $v){
            foreach ($v as $key => $value){
                $arrSort[$key][$k] = $value;
            }
        }
        if($sort['direction'] && $arrSort){
            array_multisort($arrSort[$sort['field']],constant($sort['direction']),$item);
        }
        //限制20条
        $dpfinals = array();
        foreach ($item as $key =>$value){
            if($key < 20){
                    $dpfinals[] = $value;
            }
        }
        $this->assign('week',$dpfinals);

        //获取季榜信息
        date_default_timezone_set("PRC");        //初始化时区
        $season = ceil((date('n'))/3);//当月是第几季度
        $start = mktime(0, 0, 0,$season*3-3+1,1,date('Y'));
        $end = mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y'));
        $map = array(
            'create_time' => array('between',[$start,$end]),    //在时间区间内
            'score' => array('egt',1)
        );

        //本月浏览
        $browse_m = Browse::where($map)->select();
        $list1_m = array();
        foreach ($browse_m as $value){
            $k = $value['user_id'];
            $list1_m[$k][] = $value;
        }
        $new1_m = array();
        foreach ($list1_m as $u => $val){
            $count = count($list1_m[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new1_m[] = $cen;
        }
        //本月评论
        $comment_m = Comment::where($map)->select();
        $list2_m = array();
        foreach ($comment_m as $value){
            $k = $value['uid'];
            $list2_m[$k][] = $value;
        }
        $new2_m = array();
        foreach ($list2_m as $u => $val){
            $count = count($list2_m[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new2_m[] = $cen;
        }

        //本月点赞
        $like_m = Like::where($map)->select();
        $list3_m = array();
        foreach ($like_m as $value){
            $k = $value['uid'];
            $list3_m[$k][] = $value;
        }
        $new3_m = array();
        foreach ($list3_m as $u => $val){
            $count = count($list3_m[$u])*1;
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $new3_m[] = $cen;
        }
        // 本月答题
        $answer_m = Answers::where($map)->select();
        $list4_m = array();
        foreach($answer_m as $value){
            $k = $value['userid'];
            $list4_m[$k][] = $value;
        }
        $news4_m = array();
        foreach($list4_m as $u => $val){
            $count = 0;
            foreach($val as $valu){
                $count += $valu->score;
            }
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $news4_m[] = $cen;
        }

        $center_m = array();
        foreach ($new1_m as $k1 => $v1){
            foreach ($new2_m as $k2 => $v2){
                if($v1['userid'] == $v2['userid']){
                    $cen['userid'] = $v2['userid'];
                    $cen['score'] = $v1['score'] + $v2['score'];
                    $center_m[] = $cen;
                    unset($new2_m[$k2]);
                    unset($new1_m[$k1]);
                }else{

                }
            }
        }
        $center_m = array_merge($center_m,$new2_m,$new1_m);     //unset掉重复值，重组成新数组

        //过渡数组与第三组比较同里，结果存入final2_m
        $final2_m = array();
        foreach ($center_m as $f => $val){
            foreach ($new3_m as $k3 => $v3){
                if($val['userid'] == $v3['userid']){
                    $cen['userid'] = $v3['userid'];
                    $cen['score'] = $val['score'] + $v3['score'];
                    $final2_m[] = $cen;
                    unset($new3_m[$k3]);
                    unset($center_m[$f]);
                }
            }
        }
        $final2_m = array_merge($final2_m,$new3_m,$center_m);
        // final2 与第四组比较同理,结果存入 final3
        $final3_m = array();
        foreach($final2_m as $y => $l){
            foreach($news4_m as $k4 => $v4){
                if($l['userid'] == $v4['userid']){
                    $cen['userid'] = $v4['userid'];
                    $cen['score'] = $l['score'] + $v4['score'];
                    $final3_m[] = $cen;
                    unset($news4_m[$k4]);
                    unset($final2_m[$y]);
                }
            }
        }
        $final3_m = array_merge($final3_m,$news4_m,$final2_m);
        $finals_m = array();
        foreach ($final3_m as $key=>$value){
            $department_m = Db::table('pb_wechat_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.department = b.id','LEFT')
                ->where('a.userid',$value['userid'])
                ->find();
            $value['id'] = $department_m['id'];
            $value['name'] = $department_m['name'];
            unset($value['userid']);
            $finals_m[$key] = $value;
        }
        //合并相同值，获得部门值
        $item_m = array();
        foreach($finals_m as $k=>$v){
            if(!isset($item_m[$v['id']])){
                $item_m[$v['id']]=$v;
            }else{
                $item_m[$v['id']]['score']+=$v['score'];
            }
        }

        //倒序，字段score排序
        $arrSort_m = array();
        foreach ($item_m as $k => $v){
            foreach ($v as $key => $value){
                $arrSort_m[$key][$k] = $value;
            }
        }
        if($sort['direction'] && $arrSort_m){
            array_multisort($arrSort_m[$sort['field']],constant($sort['direction']),$item_m);
        }

        //限制20条
        $dpfinals_m = array();
        foreach ($item_m as $key =>$value){
            if($key < 20){
                $dpfinals_m[] = $value;
            }
        }
        $this->assign('month',$dpfinals_m);
        
        return $this->fetch();
    }
    /**
     * 修改头像
     */
    public function setHeader(){
        $userId = session('userId');
        $header = input('header');
        $map = array(
            'headimgurl' => $header,
        );
        $user = WechatUser::where('userid',$userId) ->find();
        $info = $user ->where('userid',$userId)->update($map);
        if($info){
            return $this->success("修改成功");
        }else{
            return $this->error("修改失败");
        }
    }
}