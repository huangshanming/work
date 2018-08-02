<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class User extends Model{
	public function get_by_id($user_id)
	{
		$where = [];
		$where['id'] = $user_id;
		$where['status'] = 1;
		return $this
			->where($where)
			->field('id,name,nickname,address,phone')
			->find();
	}

	//修改用户家谱id
	public function update_jiapu($user_id,$jiapu_id) 
	{
		if(!is_numeric($user_id) || !is_numeric($jiapu_id)) {
			return false;
		}
		$where = [];
		$where['id'] = $user_id;
		$data = [];
		$data['jiapu_id'] = $jiapu_id;
		return $this->where($where)->update($data);
	}

	//通过家谱id获取用户
	public function get_by_jiapu($jiapu_id,$user_id)
	{
		$where = [];
		$where['jiapu_id'] = $jiapu_id;
		$where['status'] = 1;
		$where['id'] = array('neq',$user_id);
		return $this
			->where($where)
			->field('id,name,photo,nickname,address,phone')
			->select();
	}

}