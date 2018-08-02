<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use app\admin\model\Order as orderModel;
use app\admin\model\Msg as msgModel;

class Order extends Controller
{
    public function get_list()
    {
    	$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
        $orderModel = new orderModel();
        $condition = [];
        if(input('title')) {
        	$condition['title'] = array('like','%'.input('title').'%');
        }
        if(input('order_num')) {
        	$condition['order_num'] = array('like','%'.input('order_num').'%');
        }
        $count = $orderModel->get_count($condition);
        $page = input('p')?input('p'):1;
        if($page<=0 || $page>$count || !is_numeric($page))
        {
            $page = 1;
        }
        $pagesize = 10;
        $list = $orderModel->get_list($page,$pagesize,$condition);
        foreach ($list as &$row) {
            $row['attr_info'] = json_decode($row['attr_info'],true);
            $str = '';
            if($row['attr_info'] != [] && (count($row['attr_info']['attrs'])==count($row['attr_info']['vals']))) 
            {
                foreach ($row['attr_info']['attrs'] as $key => $val) {
                    $str .= $val.':'.$row['attr_info']['vals'][$key].' ';
                }
            }
            $row['attr_info'] = $str;
        	$row['order_status'] = $row['status'];
        	$row['status'] = getOrderStatus($row['status']);
        }
        $pageCount = ceil($count/$pagesize);
        $data['list'] = $list;
        $data['count'] = $count;
        $data['pageCount'] = $pageCount;
        return json(code('e200'),$data,'');
    }

    //发货
    public function send_order()
    {
    	$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
    	$id = input('id');
    	$data = [];
    	$data['status'] = 3;
    	$data['express'] = input('express');
    	$data['express_id'] = input('express_id');
    	$orderModel = new orderModel();
    	$ret = $orderModel->update_orders($id,$data);
    	if($ret){
            $order_info = $orderModel->where('id',$id)->find();
            $arr = [];
            $arr['goods_name'] = $order_info['title'];
            $arr['attr_id'] = $order_info['goods_attr_id'];
            $arr['num'] = $order_info['number'];
            $arr['order_num'] = $order_info['order_num'];
            $arr['price'] = $order_info['price'];
            $arr['express'] = input('express');
            $arr['express_id'] = input('express_id');
            $msgModel = new msgModel();
            $msgModel->send_order($user_id,$arr);
			return json(code('e200'),'','');
    	}
    	return json(code('e400'),'','操作失败');
    }

    
}
