<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class Cate extends Model{
	public function get_list($status=null)
	{
		$where = [];
		if($status!==null)
		{	
			$where['status'] = $status;
		}
		return $this->field('id,name,status,create_time,update_time,photo')->where($where)->select();
	}

}