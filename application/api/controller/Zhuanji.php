<?php
namespace app\api\controller;
use think\Db;
use think\Controller;

class Zhuanji extends Controller
{
	public function upload() {
		$user_info = User::is_login(true);
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
		if(!$user_info['jiapu_id'])
		{
			return json(code('e400'),'','未加入家谱');
		}
		$title 	= input('title');
		$brief 	= input('brief');
		$detail = input('detail');
		$photo 	= input('photo');
		if(!trim($title) || !trim($brief) || !trim($detail) || !trim($photo)) {
			return json(code('e400'),'','参数不完整');
		}
		$photo = Image::saveBase64Image($photo);
		if(!$photo)
		{
			return json(code('e400'),'','图片上传失败');
		}
		$data['photo'] 			= $photo;
		$data['brief'] 			= $brief;
		$data['title'] 			= $title;
		$data['detail'] 		= $detail;
		$data['jiapu_id'] 		= $user_info['jiapu_id'];
		$data['create_time'] 	= time();
		$ret = Db::name('zhuanji')->insert($data);
		if($ret) {
			$id = Db::name('zhuanji')->getLastInsID();
			return json(code('e200'),$id,'上传成功');
		}
		return json(code('e400'),'','上传失败');
	}

	public function get_list()
	{
		$user_info = User::is_login(true);
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
		if(!$user_info['jiapu_id'])
		{
			return json(code('e400'),'','未加入家谱');
		}
		$last_id 			= input('last_id');
		$where 				= [];
		$where['jiapu_id'] 	= $user_info['jiapu_id'];
		$where['status']	= 1;
		if($last_id) {
			$where['id'] = array('lt',$last_id);
		}
		$pagesize = intval(input('pagesize'));
		if(!$pagesize) $pagesize = 10;
		$data = Db::name('zhuanji')->where($where)->order('id desc')->limit($pagesize)->select();
		foreach ($data as &$row) {
			$row['photo'] = config('photo_path').$row['photo'];
		}
		return json(code('e200'),$data,'');
	}
	public function detail()
	{
		$id = intval(input('id'));
		if(!$id) {
			return json(code('e400'),'','参数错误');
		}
		$where 				= [];
		$where['id'] 		= $id;
		$where['status']	= 1;
		$detail = Db::name('zhuanji')->where($where)->find();
		if($detail) {
			$detail['photo'] = config('photo_path').$detail['photo'];
			$detail['create_time'] = date('Y-m-d H:i:s',$detail['create_time']);
		}
		return json(code('e200'),$detail,'');
	}
}