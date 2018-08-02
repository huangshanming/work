<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class GoodsAttr extends Model{
	public function get_by_GoodsId($goodsId)
	{
		$sql = '';
		$sql = 'select a.id,a.goods_id,a.attr_info,a.price,a.origin_price,a.count,a.sale from '.config('prefix').'goods_attr a where a.status=1 and a.goods_id="'.$goodsId.'"';
		return $this->query($sql);
	}

	public function get_by_GoodsId_AttrId($goodsId,$attr_id)
	{
		$sql = '';
		$sql = 'select g.title,g.admin_id,a.id,a.goods_id,a.attr_info,a.price,a.origin_price,a.count,a.sale from '.config('prefix').'goods_attr a left join '.config('prefix').'goods g on g.id=a.goods_id where a.status=1 and a.goods_id="'.$goodsId.'" and a.id="'.$attr_id.'" limit 1';
		return $this->query($sql)[0];
	}

	public function get_by_id($attr_id)
	{
		$where = [];
		$where['status'] = 1;
		$where['id'] = $attr_id;
		return $this
		->field('id,count,price,goods_id')
		->where($where)
		->find();
	}

	//获取订单详情界面信息
	public function get_info($attr_id)
	{
		$where = [];
		$where['r.status'] = 1;
		$where['r.id'] = $attr_id;
		$where['r.count'] = array('gt',0);
		$where['g.status'] = 1;

		return $this
			->alias('r')
			->field('r.count,r.attr_info,r.origin_price,g.picture,r.price,g.title,r.goods_id,g.cate_id')
			->join('goods g','g.id=r.goods_id')
			->where($where)
			->find();
	}

	//改变库存
	public function change_num($attr_id,$num)
	{
		if($num<0)
		{
			$where = [];
			$where['id'] = $attr_id;
			$attr_count = $this->where($where)->value('count');
			if(($attr_count+$num)<0)
			{
				return false;
			}
		}
		$update = [];
		$update['count'] = $attr_count+$num;
		return $this->where($where)->update($update);
	}

	//检测库存
	public function check_num($attr_id,$num) {
		$where = [];
		$where['id'] = $attr_id;
		$where['status'] = 1;
		$attr_count = $this->where($where)->value('count');
		if(!$attr_count || !$num || !is_numeric($num)) {
			return false;
		}
		if($num>=$attr_count) {
			return true;
		}else {
			return false;
		}
	}
}