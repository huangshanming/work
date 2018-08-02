<?php
namespace app\admin\model;
use think\Model;
/**
 * 基础model
 */
class Msg extends Model{
	protected function send($user_id,$type,$data,$send_id='')
	{
		$json_arr = json_encode($data);
		$arr = [];
		$arr['type'] = $type;
		$arr['user_id'] = $user_id;
		$arr['con'] = $json_arr;
		$arr['create_time'] = time();
		if($send_id) $arr['send_id'] = $send_id;
		return $this->insert($arr);
	}

	//发货消息 = 商品名称 + 商品attr_id + 数量 + 总金额 + 订单号 + 快递公司 + 快递单号
	public function send_order($user_id,$data) 
	{
		$arr = [];
		$arr['goods_name'] = $data['goods_name'];
		$arr['attr_id'] = $data['attr_id'];
		$arr['num'] = $data['num'];
		$arr['price'] = $data['price'];
		$arr['order_num'] = $data['order_num'];
		$arr['express'] = $data['express'];
		$arr['express_id'] = $data['express_id'];
		return $this->send($user_id,2,$arr);
	}
}