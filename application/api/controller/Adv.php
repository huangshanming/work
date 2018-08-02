<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\Adv as advModel;

class Adv extends Controller
{
	public function get_model()
	{
		$advModel = new advModel();
		$list = $advModel->get_list(1,7);
		foreach ($list as &$row) {
			$row['picture'] = config('photo_path').$row['picture'];
		}
		return json(code('e200'),$list,'获取成功');
	}

	public function get_detail_left()
	{
		$advModel = new advModel();
		$list = $advModel->get_list(2,4);
		foreach ($list as &$row) {
			$row['picture'] = config('photo_path').$row['picture'];
		}
		return json(code('e200'),$list,'获取成功');
	}

	public function get_detail_right()
	{
		$advModel = new advModel();
		$list = $advModel->get_list(3,2);
		foreach ($list as &$row) {
			$row['picture'] = config('photo_path').$row['picture'];
		}
		return json(code('e200'),$list,'获取成功');
	}
}