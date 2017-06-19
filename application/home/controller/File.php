<?php

namespace app\home\controller;

use app\home\model\File as FileModel;
use app\home\model\Picture;
use think\Config;
use think\Image;

class File extends Base
{
    /* 文件上传 */
    public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = new FileModel();
		$file_driver = Config::get('upload_drive'); 
		$info = $File->upload(
			$_FILES,
            Config::get('download_upload'),
            Config::get('upload_drive'),
            Config::get("upload_{$file_driver}_config")
		);
        /* 记录附件信息 */
        if($info){
            $return['data'] = $info['download'];
            $return['info'] = "上传成功";
            $return['path'] = '/uploads/download/'.$info['download']['savepath'].$info['download']['savename'];
        } else {
            $return['status'] = 0;
            $return['info'] = $File->getError();
        }

        /* 返回JSON数据 */
        return json_encode($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            return $this->error('参数错误！');
        }

//        $logic = D('Download', 'Logic');
//        if(!$logic->download($id)){
//            return $this->error($logic->getError());
//        }

    }

    /**
     * 上传图片
     */
    public function uploadPicture(){
        /* 调用文件上传组件上传文件 */
        $Picture = new Picture();
        $pic_driver = Config::get('upload_drive');
        $info = $Picture->upload(
            $_FILES,
            Config::get('download_upload'),
            Config::get('upload_drive'),
            Config::get("upload_{$pic_driver}_config")
        ); 

        /* 记录图片信息 */
        /* 记录附件信息 */
        if($info){
            $return['data'] = $info['picture'];
            $return['code'] = 1;
        } else {
            $return['code'] = 0;
            $return['info'] = $Picture->getError();
        }

        return json_encode($return);
    }
    /**
     * 上传头像
     */
    public function uploadHead(){
        //打开图片
        $image = Image::open($_FILES ["picture"]["tmp_name"]);
        $types = explode('.',$_FILES ["picture"]['name']);
        $type = array_pop($types);
        //获取 宽高
        $w = $image ->width();
        $h = $image ->height();
        //设置保存路径
        $path =  substr(Config::get('download_upload')['rootPath'].''.date('Y-m-d',NOW_TIME).'/'.uniqid().'.'.$type,1);
        //居中裁剪
        if($w >= $h){
            $result = $image ->crop($h,$h,($w-$h)/2 ,0) ->save('.'.$path);
        }else{
            $result = $image ->crop($w,$w,($h-$w)/2 ,0) ->save('.'.$path);
        }
        $return['data']['path'] = $path;
        $return['code'] = 1;
        return json_encode($return);
    }

}
