<?php
/**
 * Created by PhpStorm.
 * User: sitff
 * Date: 2017/8/26
 * Time: 上午9:52
 */
namespace app\home\controller;
use app\home\model\Like;
use app\home\model\Comment;
use app\user\model\WechatUser;
use app\home\model\Browse;

class Live extends Base
{
    /**
     *
     * @return mixed
     */
    public function index()
    {

        $this->anonymous();

        $userId = session('userId');

        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'live_id' => 1,
                'create_time' => ['egt',strtotime(date('Y-m-d'))]
            );
            $history = Browse::get($con);
            if(!$history){
                $s['score'] = array('exp','`score`+1');
                if ($this->score_up()){
                    // 未超过 15分
                    WechatUser::where('userid',$userId)->update($s);
                    unset($con['create_time']);
                    Browse::create($con);
                }
            }

        }

        //获取 评论
//        $commentModel = new Comment();
//        $comment = $commentModel->getComment(7,1,$userId);
//        $map = array(
//            'type' => 7,
//            'aid' => 1,
//            'status' => 0,
//            'create_time' => ['egt',strtotime(date('Y-m-d'))]
//        );
//
//        $comments = $commentModel->where($map)->count();

        $this->assign('live_path',config('live_path'));
//        $this->assign('comments',$comments);
//        $this->assign('comment',$comment);

        return $this->fetch();
    }

    /**
     * 获取在线人数
     */
    public function getOnlineNumber()
    {
        //浏览不存在则存入pb_browse表
        $con = array(
            'live_id' => 1,
            'create_time' => ['egt',strtotime(date('Y-m-d'))]
        );
        $sum = Browse::where($con)->count();

        return $this->success('获取成功!',null,$sum);
    }

    /**
     * 每个支部人数统计
     */
    public function getDepartmentNumer()
    {
        $userId = session('userId');
        //浏览不存在则存入pb_browse表
        $con = array(
            'live_id' => 1,
            'create_time' => ['egt',strtotime(date('Y-m-d'))]
        );
        $sum = Browse::where($con)->select();
        $wechatDe = WechatDepartment::where('parentid',1)->select();
        $num = [];
        foreach ($sum as $K => $v) {
            $user = WechatUser::where('userid',$v['user_id'])->find();
            $userDid =json_decode($user['department'])[0]; // 部门id

            while (true) {
                $res = WechatDepartment::where('id',$userDid)->find();
                if ($res['parentid'] == 1) {
                    break;
                }
                $userDid = $res['parentid'];
            }

            if (empty($num[$res['id']])){

                $num[$res['id']] = 1;
            } else {

                $num[$res['id']]++;
            }

         }

         foreach ($wechatDe as $k => $v) {
             $id = $wechatDe[$k]['id'];
             if (empty($num[$id])) {

                 $wechatDe[$k]['numer'] = 0;
             } else {

                 $wechatDe[$k]['numer'] = $num[$id];
             }
         }

        return $this->success('获取成功!',null,$wechatDe);
    }


    /**
     * 获取实时评论
     * @return array|void
     */
    public function getComment()
    {
        $id = input('id')?input('id'):0;
        $uid = session('userId');
        //获取 评论
        $commentModel = new Comment();
        $map = array(
            'type' => 7,
            'aid' => 1,
            'status' => 0,
            'create_time' => ['egt',strtotime(date('Y-m-d'))],
            'id' => ['gt',$id]
        );

        $comment = $commentModel->where($map)->order('create_time desc')->limit(7)->select();
        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));

        if($comment) {
            foreach ($comment as $value) {
                $user = WechatUser::where('userid',$value['uid'])->find();
                $value['nickname'] = $user['name'];
                $value['header'] = $user['headimgurl'];
                $value['time'] = date('H:i:s',$value['create_time']);
                $value['content'] = strtr($value['content'], $badword1);
//                $map1 = array(
//                    'type' => 0,
//                    'aid' => $value['id'],
//                    'uid' => $uid,
//                    'status' => 0,
//                );
//                $like = Like::where($map1)->find();
//                ($like) ? $value['is_like'] = 1 : $value['is_like'] = 0;
            }
            return $this->success("加载成功","",$comment);
        }else{
            return $this->error("没有更多");
        }

    }

}