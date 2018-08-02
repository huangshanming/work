<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\JiapuPower as jiapuPowerModel;
use app\api\model\Jiapu as jiapuModel;
use app\api\model\Personnel as personnelModel;

class Jiapupower extends Controller
{
	public function get_user_list() {
		$user_id = intval(input('user_id'));
		if(!$user_id) {
			return json(code('e201'),'','管理员id为空');
		}
		$jiapuPowerModel = new jiapuPowerModel();
		$personnelModel = new personnelModel();
		$power = $jiapuPowerModel->get_by_user($user_id);
		$list = $personnelModel->get_by_ids($power);
		return json(code('e200'),$list,'');
	}

	public function update_by_user() {
		$user_id = intval(input('user_id'));
		if(!$user_id) {
			return json(code('e201'),'','管理员id为空');
		}
		$ids = trim(input('ids'));
		$jiapuPowerModel = new jiapuPowerModel();
		$ret = $jiapuPowerModel->update_by_user($user_id,$ids);
		if($ret) {
			return json(code('e200'),'','修改权限成功');
		}
		return json(code('e400'),'','修改权限失败');
	}
}