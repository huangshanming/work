<?php
namespace app\admin\model;
use think\Model;
/**
 * 基础model
 */
class Order extends Model{
    public function get_list($page,$pagesize,array $where)
    {
        $total = ($page-1)*$pagesize;
        return $this
        	// ->field('id,user_id,order_num,title,create_time,attr_name_1,attr_name_2,attr_value_1,attr_value_2,status')
        	->where($where)
        	->order('id desc')
            ->limit($total,$pagesize)
        	->select();
    }

    public function get_count(array $where)
    {
    	return $this->where($where)->count();
    }

    public function update_orders($order_id,$data)
    {	
    	$where = [];
    	$where['id'] = $order_id;
    	return $this->where($where)->update($data);
    }	
    

}
