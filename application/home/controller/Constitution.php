<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/8/16
 * Time: 9:12
 */

namespace app\home\controller;
use app\home\model\WechatUser;
use think\Controller;
use app\admin\model\Question;
use app\home\model\Answers;

/**
 * class Constitution
 * 每日一课
 */
class Constitution extends Base {
    /*
     * 每日一课 页面
     */
    public function course(){
        $userid = session('userId');
        $day = date("j",time());  // 获取当天日期  没有前导0
        $mon = date("n",time());  // 获取当天月份  没有前导0
        $year = date("Y",time());  // 获取当天年份
        $map = array(
            'userid' => $userid,
        );
        $Answers = Answers::where($map)->order('id desc')->limit(1)->find();
        if(empty($Answers)){   // 没有数据
            //取单选
            $arr=Question::all(['type'=>0]);
            foreach($arr as $value){
                $ids[]=$value->id;
            }
            //随机获取单选的题目
            $num=3;//题目数目
            $data=array();
            while(true){
                if(count($data) == $num){  // 满足题目数量   退出
                    break;
                }
                $index = mt_rand(0,count($ids)-1);  // 获取一个随机数  即随机获取题库中题目数组的索引
                $res = $ids[$index];  // 该索引下对应的题目
                if(!in_array($res,$data)){
                    $data[] = $res;  //  不重复
                }
            }
            foreach($data as $value){
                $question[] = Question::get($value);
            }
            $this->assign('question',$question);
            return $this->fetch();
        }else{  //  有数据
            $user_day = date("j", $Answers['create_time']);  // 获取用户答题的日期
            $user_mon = date("n", $Answers['create_time']);  // 获取用户答题的月份  无前导0
            $user_year = date("Y", $Answers['create_time']);  // 获取用户答题的年
            if($day != $user_day ){  // 当天 还未答题
                //取单选
                $arr=Question::all(['type'=>0]);
                foreach($arr as $value){
                    $ids[]=$value->id;
                }
                //随机获取单选的题目
                $num=3;//题目数目
                $data=array();
                while(true){
                    if(count($data) == $num){
                        break;
                    }
                    $index=mt_rand(0,count($ids)-1);
                    $res=$ids[$index];
                    if(!in_array($res,$data)){
                        $data[]=$res;
                    }
                }
                foreach($data as $value){
                    $question[]=Question::get($value);
                }
                $this->assign('question',$question);
                return $this->fetch();
            }else{
                if ($mon != $user_mon){
                    //取单选
                    $arr=Question::all(['type'=>0]);
                    foreach($arr as $value){
                        $ids[]=$value->id;
                    }
                    //随机获取单选的题目
                    $num=3;//题目数目
                    $data=array();
                    while(true){
                        if(count($data) == $num){
                            break;
                        }
                        $index=mt_rand(0,count($ids)-1);
                        $res=$ids[$index];
                        if(!in_array($res,$data)){
                            $data[]=$res;
                        }
                    }
                    foreach($data as $value){
                        $question[]=Question::get($value);
                    }
                    $this->assign('question',$question);
                    return $this->fetch();
                }else{
                    if($year != $user_year){
                        //取单选
                        $arr=Question::all(['type'=>0]);
                        foreach($arr as $value){
                            $ids[]=$value->id;
                        }
                        //随机获取单选的题目
                        $num=3;//题目数目
                        $data=array();
                        while(true){
                            if(count($data) == $num){
                                break;
                            }
                            $index=mt_rand(0,count($ids)-1);
                            $res=$ids[$index];
                            if(!in_array($res,$data)){
                                $data[]=$res;
                            }
                        }
                        foreach($data as $value){
                            $question[]=Question::get($value);
                        }
                        $this->assign('question',$question);
                        return $this->fetch();
                    }else{
                        // 当天已经答过题
                        $Qid = json_decode($Answers->question_id);
                        $rights=json_decode($Answers->value);
                        $re = array();
                        foreach($Qid as $key => $value){
                            $re[$key] = Question::get($value);
                            $re[$key]['right'] = $rights[$key];
                        }
                        $this->assign('question',$re);
                        return $this->fetch('scan');
                    }
                }
            }
        }
    }
    /*
     * 每日一课 提交
     */
    public function commit(){
        // 获取用户提交数据
        $data = input('post.');
        $arr = $data['data'];
        $question = array();
        $status = array();
        $options = array();
        $Right = array();
        $score = 0;
        foreach($arr as $key => $value){
            $Question=Question::get($value[0]);
            switch(json_decode($Question->value)){
                case 1:
                    $right = "A";
                    break;
                case 2:
                    $right = "B";
                    break;
                case 3:
                    $right = "C";
                    break;
                case 4:
                    $right = "D";
                    break;

            }
            $Right[$key+1] = $right;
            $question[$key] = $value[0];
            $options[$key] = $value[1];
             // 判断 题目的对错
            if($value[1] == json_decode($Question->value)){
                $status[$key] = 1;
                $score++;
            }else{
                $status[$key] = 0;
            }
        }
        $users = session('userId');
        //将分数添加至用户积分排名
        $wechatModel = new WechatUser();
        $wechatModel->where('userid',$users)->setInc('score',$score);
        //将获取的数据进行json格式转化  存储 表
        $Answers = new Answers();
        $Answers->userid = $users;
        $Answers->question_id = json_encode($question);
        $Answers->value = json_encode($options);
        $Answers->status = json_encode($status);
        $Answers->score = $score;
        $Answers->create_time = time();
        $res = $Answers->save();
        if($res){
            return $this->success('提交成功',array('id' =>$res,'score'=>$score,'right'=>$Right));
        }else{
            return $this->error('提交失败');
        }
    }
    /*
     * 每日一课  查看详情
     */
    public function scan(){
        $id = input('id/d');
        $Answers = Answers::get($id);
        if (empty($Answers)){
            return $this->error('系统错误,不存在该条数据',Url('Constitution/course'));
        }
        $Qid = json_decode($Answers->question_id);
        $rights=json_decode($Answers->value);
        $re = array();
        foreach($Qid as $key => $value){
            $re[$key] = Question::get($value);
            $re[$key]['right'] = $rights[$key];
        }
        $this->assign('question',$re);
        return $this->fetch();
    }
}