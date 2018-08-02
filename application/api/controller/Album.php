<?php
namespace app\api\controller;
use think\Db;
use think\Controller;

class Album extends Controller
{
	public function get_list()
	{
		$user_info = User::is_login(true);
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
		if(!$user_info['jiapu_id']) 
		{
			return json(code('e400'),'','您还没有加入家族');
		}
		$count = Db::name('album')
			->where('status',1)
			->where('jiapu_id',$user_info['jiapu_id'])
			->count();
		$page 			= intval(input('page'));
		$pagesize 		= intval(input('pagesize'));
		$page 			= $page?$page:1;
		$pagesize 		= $pagesize?$pagesize:20;
        if($page<=0 || $page>$count || !is_numeric($page))
        {
            $page = 1;
        }
        $total = ($page-1) * $pagesize;
        $list = Db::name('album')
			->where('status',1)
			->where('jiapu_id',$user_info['jiapu_id'])
			->limit($total,$pagesize)
			->order('id desc')
			->select();
		foreach ($list as &$row) {
			$row['photo'] = config('photo_path').$row['photo'];
		}
		$data = [];
		$data['list'] 		= $list;
		$data['count'] 		= $count;
		$data['page']		= $page;
		$data['pagesize']	= $pagesize;
		return json(code('e200'),$data,'');

	}

	public function upload() 
	{
		$user_info = User::is_login(true);
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
		if(!$user_info['jiapu_id']) 
		{
			return json(code('e400'),'','您还没有加入家族');
		}
		$data = [];
		$photo = input('photo');
		$remark = input('remark');
        if(strlen($photo)>config('img_len'))
        {
            $photo = Image::saveBase64Image($photo);
            if(!$photo)
            {
                return json(code('e400'),'','头像保存失败');
            }
            $data['photo'] = $photo;
        }
        $data['remark'] 		= $remark;
        $data['user_id'] 		= $user_info['id'];
        $data['jiapu_id'] 		= $user_info['jiapu_id'];
        $data['create_time'] 	= time();
        $ret = Db::name('album')->insert($data);
        if($ret) 
        {
        	return json(code('e200'),'','上传成功');
        }
        return json(code('e400'),'','上传失败');

	}
}