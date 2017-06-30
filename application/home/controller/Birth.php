<?php

namespace app\home\controller;
/**
 * Class Birth
 * @package  入党那一天
 */
use app\home\model\Birth as BirthModel;
class Birth extends Base {
    /**
     * 主页
     */
    public function index(){

        return $this->fetch();
    }

    /**
     *  详细页
     */
    public function detail(){
        $name = input('post.name');
        $data = input('post.data');  // 2017-07-07
        if (strtotime($data) > time()){
            return 0;
        }
        $str1 = date('y年m月d日',time());
        $res = BirthModel::where('name',$name)->find();
        $str2 = $this ->datediffage(strtotime('1921-07-01'),strtotime($data));
        $Birth = new BirthModel();
        if ($res){
            // 有数据  修改
            $results = $Birth -> where(['name' => $name])->save(['name' => $name,'content' => $str1,'create_time' => time()]);
            if ($results){
                $num = BirthModel::count();  // 添加 人数
                $this->assign(['name' => $name,'num' => 500+$num,'str1' => $str1,'str2' => $str2]);
            }
        }else{
            // 无数据  添加
            $result = $Birth -> save(['name' => $name,'content' => $str1,'create_time' => time()]);
            if ($result){
                $num = BirthModel::count();  // 添加 人数
                $this->assign(['name' => $name,'num' => 500+$num,'str1' => $str1,'str2' => $str2]);
            }
        }
        return $this->fetch();
    }
    function datediffage($before, $after) {
        if ($before>$after) {
            $b = getdate($after);
            $a = getdate($before);
        }else {
            $b = getdate($before);
            $a = getdate($after);
        }

        $n = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
        $y=$m=$d=0;

        if ($a['mday']>=$b['mday']) { //天相减为正

            if ($a['mon']>=$b['mon']) {//月相减为正
                $y=$a['year']-$b['year'];$m=$a['mon']-$b['mon'];
            }else { //月相减为负，借年
                $y=$a['year']-$b['year']-1;$m=$a['mon']-$b['mon']+12;
            }

            $d=$a['mday']-$b['mday'];

        }else {  //天相减为负，借月
            if ($a['mon']==1) { //1月，借年
                $y=$a['year']-$b['year']-1;$m=$a['mon']-$b['mon']+12;$d=$a['mday']-$b['mday']+$n[12];
            }else {

                if ($a['mon']==3) { //3月，判断闰年取得2月天数
                    $d=$a['mday']-$b['mday']+($a['year']%4==0?29:28);
                } else {
                    $d=$a['mday']-$b['mday']+$n[$a['mon']-1];
                }


                if ($a['mon']>=$b['mon']+1) { //借月后，月相减为正
                    $y=$a['year']-$b['year'];$m=$a['mon']-$b['mon']-1;
                }else { //借月后，月相减为负，借年
                    $y=$a['year']-$b['year']-1;$m=$a['mon']-$b['mon']+12-1;
                }


            }


        }

        return ($y==0?'':$y.'年').($m==0?'':$m.'个月').($d==0?'':$d.'天');

    }
}