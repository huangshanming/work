<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class Order extends Model{

	public function build($data)
	{
		return $this->insert($data);
	}

	//根据用户id和订单号查询
	public function get_info($user_id,$order_num)
	{
		$where = [];
		$where['user_id'] = $user_id;
		$where['order_num'] = $order_num;
		$where['status'] = array('gt',0);
		return $this->where($where)->find();
	}

	//获取订单
	public function get_list($user_id,$condition='',$last_id=null,$status=null)
	{
		$where = [];
		if(!$user_id)
		{
			return false;
		}
		if($last_id)
		{
			$where['o.id'] = array('lt',$last_id);
		}
		$where['o.user_id'] = $user_id;
		if($status!=null) $where['o.status'] = $status;
		return $this
			->alias('o')
			->field('o.*,g.picture')
			->join('goods g','g.id=o.goods_id','left')
			->where($where)
			->where('o.status>=0')
			->where($condition)
			->order('o.id desc')
			->limit(5)
			->select();
	}

	//获取众筹列表
	public function get_crowd_list($jiapu_id=null,$status=1,$pagesize=5,$condition='',$last_id=null) 
	{
		$where = [];
		if(!$jiapu_id)
		{
			return false;
		}
		$where['o.jiapu_id'] = $jiapu_id;
		if($last_id)
		{
			$where['o.id'] = array('lt',$last_id);
		}
		if($status!=null) $where['o.status'] = $status;
		return $this
			->alias('o')
			->field('o.*,g.picture,u.name,u.nickname')
			->join('goods g','g.id=o.goods_id','left')
			->join('user u','o.user_id=u.id','left')
			->where($where)
			->where($condition)
			->order('o.id desc')
			->limit($pagesize)
			->select();
	}

	public function get_count($user_id,$status='') 
	{
		$where = [];
		$where['user_id'] = $user_id;
		$where['status'] = array('neq','-3');
		if($status) {
			$where['status'] = $status;
		}
		return $this->where($where)->count();
	}

	//检查库存
	public function check_num($orderId) {
		$where = [];
		$where['id'] = $orderId;
		$order_number = $this->where($where)->value('number');
		$goodsAttr = new GoodsAttr();
		$where = [];
		$where['id'] = $attr_id;
		$where['status'] = 1;
		$attr_count = $goodsAttr->where($where)->value('count');
		if(!$attr_count || !$order_number) {
			return false;
		}
		if($attr_count>=$order_number) {
			return true;
		}else {
			return false;
		}
	}

	//确认收货
	public function collect_goods($user_id,$order_id) 
	{
		$where = [];
		$where['user_id'] = $user_id;
		$where['id'] = $order_id;
		$where['status'] = 3;
		$order_id = $this->where($where)->value('id');
		if($order_id) {
			return $this->where(['id'=>$order_id])->update(['status'=>4]);
		}else {
			return false;
		}
	}

}