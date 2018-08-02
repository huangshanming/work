<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;

class Image extends Controller
{ 
	/**
	* 保存64位编码图片
	*/

	public static function saveBase64Image($base64_image_content){

	    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){

	              //图片后缀
	              $type = $result[2];

	              //保存位置--图片名
	              $image_name=rand(1,1000).date('His').str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT).rand(1,1000).".".$type;
	              $img_url = date('Ymd') . '/'.$image_name;  
	              $image_url =   config('photo_upload') . $img_url;  
	              $dir_url = config('photo_upload') . date('Ymd');         
	              if(!is_dir(dirname($image_url))){
	                    mkdir(dirname($image_url));
	                    chmod(dirname($image_url), 0777);

	              }
	             
	              //解码
	              $decode=base64_decode(str_replace($result[1], '', $base64_image_content));
	              if (file_put_contents($image_url, $decode)){
	                    $data['code']=0;
	                    $data['imageName']=$image_name;
	                    $data['url']=$img_url;
	                    $data['msg']='保存成功！';
	              }else{
	                $data['code']=1;
	                $data['imgageName']='';
	                $data['url']='';
	                $data['msg']='图片保存失败！';
	              }
	    }else{
	        $data['code']=1;
	        $data['imgageName']='';
	        $data['url']='';
	        $data['msg']='base64图片格式有误！';


	    }       
	    return $data['url'];
	}
}
