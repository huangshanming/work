<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\Cate as cateModel;

class Cate extends Controller
{
	public function get_list()
	{	
		$cateModel = new cateModel();
		$list = $cateModel->get_list(1);
		return json(code('e200'),$list,'获取分类列表成功');
	}
}