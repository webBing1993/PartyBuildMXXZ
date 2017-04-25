<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/5/20
 * Time: 10:11
 */

namespace app\admin\model;

use Endroid\QrCode\QrCode;
use think\Model;

class Base extends Model
{
    protected $autoWriteTimestamp = false;

    //获取新增用户名称
    public function cuser(){
        return $this->hasOne('Member','id','create_user');
    }

    //获取更新用户
    public function uuser(){
        return $this->hasOne('Member','id','update_user');
    }

    /**
     * 利用时间戳及英文字母生成16位随机数
     */
    function foo() {
        $o = $last = '';
        do {
            $last = $o;
            usleep(10);
            $t = explode(' ', microtime());
            $o = substr(base_convert(strtr($t[0].$t[1].$t[1],'.',''),10,36),0,16);
        }while($o == $last);
        return $o;
    }

    /**
     * 生成二维码
     * type :1第一聚焦 2两学一做
     */
    public function qrcode($type,$code) {
        if($type == 1) {
            $url = "http://".$_SERVER['SERVER_NAME']."/home/news/review?code=".$code;
        }else{
            $url = "http://".$_SERVER['SERVER_NAME']."/home/learn/review?code=".$code;
        }
        //生成保存二维码文件路径
        $date = date("Y-m",time());
        if (!is_dir('qrcode')) mkdir('qrcode', 0777);
        if (!is_dir('qrcode/'.$date)) mkdir('qrcode/'.$date, 0777);
        $file = "qrcode/".$date."/".time().".jpg";
        //生成二维码
        $qrCode = new QrCode();
        $qrCode
            ->setText($url)
            ->setSize(300)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->render($file)
        ;
        $data = array(
            'code' => $code,
            'url' => $url,
            'code_path' => $file,
        );
        return json_encode($data);
    }
}