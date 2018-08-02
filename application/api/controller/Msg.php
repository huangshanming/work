<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\Jiapu as jiapuModel;
use app\api\model\User as userModel;
use app\api\model\Personnel as personnelModel;
use app\api\model\Msg as msgModel;

class Msg
{
	public function get_list()
	{
		$msgModel = new msgModel();
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$condition = [];
		$pagesize = 10;
		if(input('pagesize') && is_numeric(input('pagesize'))) {
			$pagesize = input('pagesize');
		}
		$last_id = '';
		if(input('last_id')) {
			$last_id = input('last_id');
			$condition['id'] = array('lt',$last_id);
		}
		$condition['user_id'] = $user_id;
		$list = $msgModel->get_list($condition,$pagesize);
		foreach ($list as &$row) {
			$row['con'] = json_decode($row['con']);
			date('Y-m-d',intval($row['create_time']));
		}
		return json(code('e200'),$list,'');

	}

	public function birth_list()
	{
		$msgModel 	= new msgModel();
		$userModel 	= new userModel();
		$user_info 	= User::is_login(true);
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
		$user_id 	= $user_info['id'];
		$jiapu_id 	= $user_info['jiapu_id'];
		$user_list  = $userModel->field('birthday, id, nickname')->where('jiapu_id', $jiapu_id)->where('status', 1)->select();
		$now 		= date('m-d', time());
		$birth_list = [];
		$self_birth = [];
		if($user_list) {
			foreach ($user_list as &$row) {
				if($row['birthday'] && $now == date('m-d', $row['birthday'])) {
					if($row['id'] == $user_id) {
						$row['type'] = 2;
						$self_birth  = $row;
					}else {
						$row['type']  = 1;
						$birth_list[] = $row;
					}
				}
			}
			if($self_birth != []) {
				array_unshift($birth_list, $self_birth);
			}
		}
		return json(code('e200'), $birth_list, '');
	}

}