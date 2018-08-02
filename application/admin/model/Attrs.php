<?php
namespace app\admin\model;
use think\Model;
/**
 * 基础model
 */
class Attrs extends Model{
    public function get_attrs(array $where)
    {
        return $this->field('id,name,create_time,update_time,status,admin_id')->where($where)->select();
    }
    public function get_list($page,$pagesize,array $where)
    {
        $total = ($page-1)*$pagesize;
        return $this
        	->field('id,name,create_time,update_time,status,admin_id')
        	->where($where)
        	->order('id desc')
            ->limit($total,$pagesize)
        	->select();
    }
    //是否创建过
    public function had_create($admin_id,$name) {
    	$where = [];
    	$where['status'] = 1;
    	$where['admin_id'] = $admin_id;
    	$where['name'] = $name;
    	return $this->where($where)->value('id');
    }

    

}
