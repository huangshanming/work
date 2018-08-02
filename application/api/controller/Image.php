<?php
namespace app\api\controller;
use think\Db;
use think\Controller;

class Image extends Controller
{
    public function upload()
    {
        $files = array();
		$success = 0;    //用户统计有多少张图片上传成功了

		foreach ($_FILES as $item) {
		  $index = count($files);

		  $files[$index]['srcName'] = $item['name'];    //上传图片的原名字
		  $files[$index]['error'] = $item['error'];    //和该文件上传相关的错误代码
		  $files[$index]['size'] = $item['size'];        //已上传文件的大小，单位为字节
		  $files[$index]['type'] = $item['type'];        //文件的 MIME 类型，需要浏览器提供该信息的支持，例如"image/gif"
		  $files[$index]['success'] = false;            //这个用于标志该图片是否上传成功
		  $files[$index]['path'] = '';                //存图片路径

		  // 接收过程有没有错误
		  if($item['error'] != 0) continue;
		  //判断图片能不能上传
		  if(!is_uploaded_file($item['tmp_name'])) {
		      $files[$index]['error'] = 8000;
		      continue;
		  }
		  //扩展名
		  $extension = '';
		  if(strcmp($item['type'], 'image/jpeg') == 0) {
		      $extension = '.jpg';
		  }
		  else if(strcmp($item['type'], 'image/png') == 0) {
		      $extension = '.png';
		  }
		  else if(strcmp($item['type'], 'image/gif') == 0) {
		      $extension = '.gif';
		  }
		  else {
		      //如果type不是以上三者，我们就从图片原名称里面去截取判断去取得(处于严谨性)    
		      $substr = strrchr($item['name'], '.');
		      if(FALSE == $substr) {
		          $files[$index]['error'] = 8002;
		          continue;
		      }

		      //取得元名字的扩展名后，再通过扩展名去给type赋上对应的值
		      if(strcasecmp($substr, '.jpg') == 0 || strcasecmp($substr, '.jpeg') == 0 || strcasecmp($substr, '.jfif') == 0 |strcasecmp($substr, '.jpe') == 0 ) {
		          $files[$index]['type'] = 'image/jpeg';
		      }
		      else if(strcasecmp($substr, '.png') == 0) {
		          $files[$index]['type'] = 'image/png';
		      }
		      else if(strcasecmp($substr, '.gif') == 0) {
		          $files[$index]['type'] = 'image/gif';
		      }
		      else {
		          $files[$index]['error'] = 8003;
		          continue;
		      }
		      $extension = $substr;
		  }

		  //对临时文件名加密，用于后面生成复杂的新文件名
		  $md5 = md5_file($item['tmp_name']);
		  //取得图片的大小
		  $imageInfo = getimagesize($item['tmp_name']);
		  $rawImageWidth = $imageInfo[0];
		  $rawImageHeight = $imageInfo[1];

		  //设置图片上传路径，放在upload文件夹，以年月日生成文件夹分类存储，
		  //rtrim(base_url(), '/')其实就是网站的根目录，大家自己处理
		  $path = rtrim(ROOT_PATH, '/') . '/public/upload/' . date('Ymd') . '/';
		  //确保目录可写
		  $this->ensure_writable_dir($path);
		  //文件名
		  $name = "$md5.0x{$rawImageWidth}x{$rawImageHeight}{$extension}";
		  //加入图片文件没变化到，也就是存在，就不必重复上传了，不存在则上传
		  $ret = file_exists($path . $name) ? true : move_uploaded_file($item['tmp_name'], $path . $name);
		  if($ret === false) {
		      $files[$index]['error'] = 8004;
		      continue;
		  }
		  else {
		      $files[$index]['path'] = date('Ymd'). '/' . $name;        //存图片路径
		      $files[$index]['success'] = true;            //图片上传成功标志
		      $files[$index]['width'] = $rawImageWidth;    //图片宽度
		      $files[$index]['height'] = $rawImageHeight;    //图片高度
		      $success ++;    //成功+1
		  }
		}
  
		//将图片已json形式返回给js处理页面  ，这里大家可以改成自己的json返回处理代码
		return json_encode(array(
			'total' => count($files),
			'success' => $success,
			'files' => $files,
		));
    }

    /**
* 确保文件夹存在并可写
*
* @param string $dir
*/
protected function ensure_writable_dir($dir) {
    if(!file_exists($dir)) {
        mkdir($dir, 0766, true);
        chmod($dir, 0766);
        chmod($dir, 0777);
    }
    else if(!is_writable($dir)) {
        chmod($dir, 0766);
        chmod($dir, 0777);
        if(!is_writable($dir)) {
            throw new FileSystemException("目录 $dir 不可写");
        }
    }
}

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
	                    return $img_url;
	              }else{
		               	return false;
	              }
	    }else{
	        return false;
	    }       
	    return false;
	}
}