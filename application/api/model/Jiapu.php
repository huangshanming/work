<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class Jiapu extends Model{
	//验证家谱名密码
	public function check($name,$password) {
		if(!trim($name) || !trim($password)) {
			return false;
		}
		$where = [];
		$where['name'] = $name;
		$where['password'] = md5($password);
		$where['status'] = 1;
		return $this->where($where)->value('id');
	}

	//是否为家谱创建者
	public function is_creator($user_id,$Jiapu_id)
	{	
		$where = [];
		$where['uid'] = $user_id;
		$where['id'] = $Jiapu_id;
		return $this->where($where)->value('id');
	}

	//根据user_id获取家谱
	public function get_by_user($user_id)
	{	
		$where = [];
		$where['uid'] = $user_id;
		return $this->where($where)->value('id');
	}

	
}