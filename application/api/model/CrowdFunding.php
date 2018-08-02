<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class CrowdFunding extends Model{
	//获取参与人数
	public function get_count($order_id)
	{
		return $this->where('order_id',$order_id)->where('status',1)->count();
	}

	public function get_list($order_id)
	{
		return $this
		->alias('c')
		->field('c.*,u.name,u.nickname')
		->where('order_id',$order_id)
		->where('c.status',1)
		->join('user u','u.id=c.user_id')
		->select();
	}
}