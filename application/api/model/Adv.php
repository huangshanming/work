<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class Adv extends Model{
	public function get_list($type,$num)
	{
		$where = [];
		$where['type'] = $type;
		$where['status'] = 1;
		return $this->where($where)->order('id desc')->limit($num)->select();
	}
}