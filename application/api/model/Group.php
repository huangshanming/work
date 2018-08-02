<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class Group extends Model{
	public function get_by_jiapuId($jiapu_id)
	{
		$jiapu_id = intval($jiapu_id);
		if(!$jiapu_id) {
			return false;
		}
		return $this->where('jiapu_id', $jiapu_id)->where('status', 1)->find();
	}
}