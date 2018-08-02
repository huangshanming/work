<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class JiapuPower extends Model{
	public function get_by_user($user_id) {
		$where = [];
		$where['user_id'] = $user_id;
		return $this->where($where)->value('power');
	}

	public function update_by_user($user_id,$ids) {
		$where = [];
		$where['user_id'] = $user_id;
		$id = $this->where($where)->value('id');
		if($id) {
			$data = [];
			$data['power'] = $ids;
			$data['update_time'] = time();
			return $this->where($where)->update($data);
		}else {
			$data = [];
			$data['power'] = $ids;
			$data['create_time'] = time();
			$data['user_id'] = $user_id;
			return $this->insert($data);
		}
	}
}