<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\User as userModel;
use app\api\model\GoodsAttr as goodsAttrModel;
use app\api\model\Order as orderModel;
use app\api\model\Goods as goodsModel;
use app\api\model\Msg as msgModel;
use app\api\model\CrowdFunding as CrowdFundingModel;

class Order extends Controller
{
	//确认订单信息
	public function confirm()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$attr_id = input('attr_id');
		$userModel = new userModel();
		$goodsAttrModel = new goodsAttrModel();
		$user_info = $userModel->get_by_id($user_id);
		$goods_info = $goodsAttrModel->get_info($attr_id);
		if(!$goods_info)
		{
			return json(code('e400'),'','该商品已下架或库存不足');
		}
		if($goods_info['picture']) $goods_info['picture'] = config('photo_path').$goods_info['picture'];
		$data = [];
		$data['user_info'] = $user_info;
		$data['goods_info'] = $goods_info;
		return json(code('e200'),$data,'获取订单信息成功');

	}

	//生成订单
	public function build()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$goodsAttrModel = new goodsAttrModel();
		$goodsModel = new goodsModel();
		$orderModel = new orderModel();
		$userModel = new userModel();
		$number = input('number');
		if(!is_numeric($number)) return json(code('e400'),'','商品输量选择错误');
		$attr_id = input('attr_id');
		$attr_info = $goodsAttrModel->get_by_id($attr_id);
		if(!$attr_info) return json(code('e400'),'','无该商品信息');
		$goods_id = $attr_info['goods_id'];
		$goods_info = $goodsAttrModel->get_by_GoodsId_AttrId($goods_id,$attr_id);
		if(!$goods_info) return json(code('e400'),'','无该商品信息');
		$title = $goods_info['title'];
		$attr_str = $goods_info['attr_info'];
		$admin_id = $goods_info['admin_id'];
		$jiapu_id = $userModel->where('id',$user_id)->value('jiapu_id');
		$status = 1;
		$create_time = time();
		$address = input('address');
		$phone = input('phone');
		$name = input('name');
		$remark = input('remark');
		$order_num = build_order_num();
		$price = $attr_info['price'];
		$data = [];
		$data['user_id'] = $user_id;
		$data['jiapu_id'] = $jiapu_id;
		$data['goods_attr_id'] = $attr_id;
		$data['goods_id'] = $goods_id;
		$data['order_num'] = $order_num;
		$data['title'] = $title;
		$data['price'] = $price;
		$data['number'] = $number;
		$data['name'] = $name;
		$data['remark'] = $remark;
		$data['phone'] = $phone;
		$data['address'] = $address;
		$data['attr_info'] = $attr_str;
		$data['admin_id'] = $admin_id;
		$data['status'] = $status;
		$data['create_time'] = $create_time;
		Db::startTrans();
		$change_num = 0-$number;
		$ret_1 = $goodsAttrModel->change_num($attr_id,$change_num);
		$ret_2 = $orderModel->build($data);
		if($ret_1 && $ret_2)
		{
			$arr = [];
			$arr['goods_name'] = $title;
			$arr['attr_id'] = $attr_id;
			$arr['num'] = $number;
			$arr['order_num'] = $order_num;
			$arr['price'] = $price;
			$msgModel = new msgModel();
			$msg_ret = $msgModel->order($user_id,$arr);
			if(!$msg_ret) {
				Db::rollback();
				return json(code('e400'),'','订单发生错误');
			}
			Db::commit();
			$data = [];
			$data['order_num'] = $order_num;
			return $this->pay_by_balance($order_num);
			// json(code('e200'),$data,'下单成功');
		}else{
			Db::rollback();
			return json(code('e400'),'','订单发生错误');
		}
	}

	private function pay_by_balance($order_num) 
	{
		$user_info = User::is_login(true);
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
		$user_id 	= $user_info['id']; 
		$orderModel = new orderModel();
		$order_info = $orderModel->get_info($user_id,$order_num);
		$money 		= $order_info['price']*$order_info['number'];
		if(!$order_info || ($user_info['balance']<$money))
		{
			if($user_info['balance']<$money) {
				return json(code('e400'),'','亲链币不足');
			}
			return json(code('e400'),'','非法操作');
		}else
		{
			Db::startTrans();
			$where 				= [];
			$data 				= [];
			$where['order_num']	= $order_num;
			$data['status']		= 2;
			$data['nice_pay']	= $money;
			$ret 				= $orderModel->where($where)->update($data);
			$sql = 'update '.config('prefix').'user set balance=balance - :money WHERE id = :id';
			$ret2 = Db::execute($sql,['id'=>$user_id,'money'=>$money]);
			if($ret && $ret2) {
				Db::commit();
				return json(code('e200'),'','操作成功'.$money.'   '.$user_info['balance']);
			}else {
				Db::rollback();
				return json(code('e400'),'','操作失败');
			}
		}
	}

	//获取订单信息
	public function get_order_info()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$orderModel = new orderModel();
		$order_num = input('order_num');
		$order_info = $orderModel->get_info($user_id,$order_num);

		if(!$order_info)
		{
			return json(code('e400'),'','非法操作');
		}else
		{
	        $outTradeNo=$order_info['order_num'];//创建订单表的时候注意要有这样一个生成唯一订单号的字段，并且不能太短，不然会报错的
	        //商品名称
	        $goodsName=$order_info['title'];
	        $wxpayApi=PAY_PATH.'wx/lib/WxPay.Api.php';//引入
	        include($wxpayApi);
	        $input = new \WxPayUnifiedOrder();
	        $input->SetBody($goodsName);
	        $input->SetAttach($goodsName);
	        $input->SetOut_trade_no($outTradeNo);//订单号
	        $totalPrice = $order_info['price'] * $order_info['number'];
	        if(!is_numeric($totalPrice)) {
	        	return json(code('e400'),'','订单异常');
	        }
	        $cret = $orderModel->where('order_num',$order_num)->update(['nice_pay'=>$totalPrice]);
	        $totalPrice=($totalPrice*100);
	        $input->SetTotal_fee($totalPrice);//总额 int  单位 分
	        // $input->SetTime_start(date("YmdHis"));
	        // $input->SetTime_expire(date("YmdHis", time() + 600));//失效时间
	        // $input->SetGoods_tag("test");
	        $input->SetNotify_url("http://www.****.com/index.php/index/Order/notify");
	        $input->SetTrade_type("NATIVE");
	        $input->SetProduct_id($order_info['goods_id']);//商品id
	        $result = \WxPayApi::unifiedOrder($input);
	        $url = 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.urlencode(@$result["code_url"]);
	        $order_info['url'] = $url;
			return json(code('e200'),$order_info,'获取成功');
		}
	}

	//查询是否支付成功
	public function get_pay_status() {
        $wxpayApi=PAY_PATH.'wx/lib/WxPay.Api.php';//引入
        include($wxpayApi);
        $order_num 	= input('order_num');
		$queryOrderInput = new \WxPayOrderQuery();
		$queryOrderInput->SetOut_trade_no($order_num);
		$result = \WxPayApi::orderQuery($queryOrderInput);
		if($result["return_code"] == "SUCCESS" 
			&& $result["result_code"] == "SUCCESS")
			{
			//支付成功
			if($result["trade_state"] == "SUCCESS"){
				$succCode = 1;
				$this->finish($order_num);
				return json(code('e200'),$succCode,'获取成功');
			}
			//用户支付中
			else if($result["trade_state"] == "USERPAYING"){
				$succCode = 2;
				return json(code('e200'),$succCode,'获取成功');
			}
		}
		
		//如果返回错误码为“此交易订单号不存在”则直接认定失败
		if(@$result["err_code"] == "ORDERNOTEXIST")
		{
			$succCode = 0;
		} else{
			//如果是系统错误，则后续继续
			$succCode = 2;
		}
		return json(code('e200'),$succCode,'获取成功');
	}

	protected function finish($order_num) 
	{
		$orderModel 		= new orderModel();
		$where 				= [];
		$data 				= [];
		$where['order_num']	= $order_num;
		$data['status']		= 2;
		$ret 				= $orderModel->where($where)->update($data);
		if($ret) {
			return true;
		}else {
			return false;
		}
	}	

	public function crowd_by_balance() 
	{
		$user_info = User::is_login(true);
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
		$user_id 	= $user_info['id'];
		$orderModel = new orderModel();
		$CrowdFundingModel = new CrowdFundingModel();
		$order_id 	= intval(input('order_id'));
		$order_info = Db::name('order')
			->where('id',$order_id)
			->where('crowd_funding',1)
			->where('status',1)
			->find();
		$money 		= input('money');
		if(!$order_id || !is_numeric($money) || !$order_info)
		{
			return json(code('e400'),'','非法操作'.$order_id);
		}else
		{
			if(($order_info['number'] * $order_info['price'] - $order_info['nice_pay']) < $money) {
				return json(code('e400'),'','支付金额大于众筹剩余金额');
			}
			if($user_info['balance'] < $money) {
				return json(code('e400'),'','亲链币不足');
			}
			$outTradeNo 			= build_order_num();
	        $data 					= [];
	        $data['crowd_num'] 		= $outTradeNo;
	        $data['order_id'] 		= $order_id;
	        $data['user_id'] 		= $user_id;
	        $data['money'] 			= $money;
	        $data['create_time'] 	= time();
	        $data['status'] 		= 1;
	        $ret  					= $CrowdFundingModel->insert($data);
	        if(($order_info['number'] * $order_info['price'] - $order_info['nice_pay']) == $money) {
	        	$sql = 'update '.config('prefix').'order set nice_pay=nice_pay + :money,status = 2 WHERE id = :id';
				$ret2 = Db::execute($sql,['id'=>$order_id,'money'=>$money]);
	        }else {
				$sql = 'update '.config('prefix').'order set nice_pay=nice_pay + :money WHERE id = :id';
				$ret2 = Db::execute($sql,['id'=>$order_id,'money'=>$money]);
	        }

			$sql2 = 'update '.config('prefix').'user set balance=balance - :money WHERE id = :id';
			$ret3 = Db::execute($sql2,['id'=>$user_id,'money'=>$money]);
			if($ret && $ret2 && $ret3) {
				Db::commit();
				return json(code('e200'),'','操作成功');
			}else {
				Db::rollback();
				return json(code('e400'),'','操作失败');
			}
		}
	}

	public function crowd() 
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$orderModel = new orderModel();
		$CrowdFundingModel = new CrowdFundingModel();
		$order_id 	= intval(input('order_id'));
		$order_info = Db::name('order')
			->where('id',$order_id)
			->where('crowd_funding',1)
			->where('status',1)
			->find();
		$money 		= input('money');
		if(!$order_id || !is_numeric($money) || !$order_info)
		{
			return json(code('e400'),'','非法操作');
		}else
		{
	        $outTradeNo 			= build_order_num();
	        $data 					= [];
	        $data['crowd_num'] 		= $outTradeNo;
	        $data['order_id'] 		= $order_id;
	        $data['user_id'] 		= $user_id;
	        $data['money'] 			= $money;
	        $data['create_time'] 	= time();
	        $data['status'] 		= 0;
	        $ret  					= $CrowdFundingModel->insert($data);
	        if(!$ret) {
	        	return json(code('e400'),'','出现异常');
	        }
	        //创建订单表的时候注意要有这样一个生成唯一订单号的字段，并且不能太短，不然会报错的
	        //商品名称
	        $goodsName=$order_info['title'];
	        $wxpayApi=PAY_PATH.'wx/lib/WxPay.Api.php';//引入
	        include($wxpayApi);
	        $input = new \WxPayUnifiedOrder();
	        $input->SetBody($goodsName);
	        $input->SetAttach($goodsName);
	        $input->SetOut_trade_no($outTradeNo);//订单号
	        $money=($money*100);
	        $input->SetTotal_fee($money);//总额 int  单位 分
	        // $input->SetTime_start(date("YmdHis"));
	        // $input->SetTime_expire(date("YmdHis", time() + 600));//失效时间
	        // $input->SetGoods_tag("test");
	        $input->SetNotify_url("http://www.****.com/index.php/index/Order/notify");
	        $input->SetTrade_type("NATIVE");
	        $input->SetProduct_id($order_info['goods_id']);//商品id
	        $result = \WxPayApi::unifiedOrder($input);
	        $url = 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.urlencode(@$result["code_url"]);
	        $order_info['url'] 			= $url;
	        $order_info['crowd_num'] 	= $outTradeNo;
			return json(code('e200'),$order_info,'获取成功');
		}
	}

	//查询众筹是否支付成功
	public function get_crowd_status() {
        $wxpayApi=PAY_PATH.'wx/lib/WxPay.Api.php';//引入
        include($wxpayApi);
        $crowd_num 	= input('crowd_num');
		$queryOrderInput = new \WxPayOrderQuery();
		$queryOrderInput->SetOut_trade_no($crowd_num);
		$result = \WxPayApi::orderQuery($queryOrderInput);
		if($result["return_code"] == "SUCCESS" 
			&& $result["result_code"] == "SUCCESS")
			{
			//支付成功
			if($result["trade_state"] == "SUCCESS"){
				$succCode = 1;
				$this->crowd_finish($crowd_num);
				return json(code('e200'),$succCode,'获取成功');
			}
			//用户支付中
			else if($result["trade_state"] == "USERPAYING"){
				$succCode = 2;
				return json(code('e200'),$succCode,'获取成功');
			}
		}
		
		//如果返回错误码为“此交易订单号不存在”则直接认定失败
		if(@$result["err_code"] == "ORDERNOTEXIST")
		{
			$succCode = 0;
		} else{
			//如果是系统错误，则后续继续
			$succCode = 2;
		}
		return json(code('e200'),$succCode,'获取成功');
	}

	protected function crowd_finish($crowd_num) 
	{
		$CrowdFundingModel 	= new CrowdFundingModel();
		$where 				= [];
		$data 				= [];
		$where['crowd_num']	= $crowd_num;
		$data['status']		= 1;
		$crowd_info 		= $CrowdFundingModel->where($where)->find();
		if(!$crowd_info) {
			return false;
		}
		$order_info = Db::name('order')
			->where('id',$crowd_info['order_id'])
			->where('crowd_funding',1)
			->where('status',1)
			->find();
		Db::startTrans();
		$ret 				= $CrowdFundingModel->where($where)->update($data);
		if(($order_info['number'] * $order_info['price'] - $order_info['nice_pay']) <= $crowd_info['money']) {
        	$sql = 'update '.config('prefix').'order set nice_pay=nice_pay + :money,status = 2 WHERE id = :id';
			$ret2 = Db::execute($sql,['id'=>$crowd_info['order_id'],'money'=>$crowd_info['money']]);
        }else {
			$sql = 'update '.config('prefix').'order set nice_pay=nice_pay + :money WHERE id = :id';
			$ret2 = Db::execute($sql,['id'=>$crowd_info['order_id'],'money'=>$crowd_info['money']]);
        }
		if($ret && $ret2) {
			Db::commit();
			return true;
		}else {
			Db::rollback();
			return false;
		}
	}	

	//获取全部订单
	public function get_list()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$last_id = '';
		if(input('last_id')) {
			$last_id = input('last_id');
		}
		$status = null;
		if(is_numeric(input('status'))&&input('status')) {
			$status = input('status');
		}
		$condition = [];
		if(input('order_num')) {
			$condition['o.order_num'] = input('order_num');
		}
		$orderModel = new orderModel();
		$list = $orderModel->get_list($user_id,$condition,$last_id,$status);
		foreach ($list as &$row) {
			//1待付款 2已付款 3已发货 4完成
			if($row['status'] == 1 && $row['crowd_funding'] == 1) {
				$row['status_msg'] = '众筹中';
			}else if($row['status'] == 1) {
				$row['status_msg'] = '待付款';
			}else if($row['status'] == 2) {
				$row['status_msg'] = '已付款';
			}else if($row['status'] == 3) {
				$row['status_msg'] = '已发货';
			}else if($row['status'] == 4) {
				$row['status_msg'] = '完成';
			}
			$row['picture'] = config('photo_path').$row['picture'];
		}
		$all = 0;
		$waitPay = 0;
		$waitSend = 0;
		$sended = 0;
		$final = 0;
		$all = $orderModel->get_count($user_id);
		$waitPay = $orderModel->get_count($user_id,1);
		$waitSend = $orderModel->get_count($user_id,2);
		$sended = $orderModel->get_count($user_id,3);
		$final = $orderModel->get_count($user_id,4);
		$count = [
			'all' 		=> $all,
			'waitPay' 	=> $waitPay,
			'waitSend' 	=> $waitSend,
			'sended' 	=> $sended,
			'final'		=> $final
		];
		return json(code('e200'),$list,$count);
	}

	//下单支付
	public function order_pay() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$order_num = input('post.order_num');
		$orderModel = new orderModel();
		$goodsAttrModel = new goodsAttrModel();
		$order_info = $orderModel->get_info($user_id,$order_num);
		if(!$order_info) {
			return json(code('e201'),'','用户订单不存在');
		}
		$goods_attr_id = $order_info['goods_attr_id'];
		$number = $order_info['number'];
		$checkNum = $goodsAttrModel->check_num($goods_attr_id,$number);
		if($checkNum) {
			
		}else {
			return json(code('e201'),'','库存不足或已下架！');
		}
	}

	//确认收货
	public function collect_goods() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$id = input('post.id');
		$orderModel = new orderModel();
		$goodsModel = new goodsModel();
		$data = $orderModel->where('id',$id)->find();
		$goods_id = $data['goods_id'];
		$admin_id = $goodsModel->where('id',$goods_id)->value('admin_id');
		$nice_pay = $data['nice_pay'];
		if($nice_pay<0) {
			return json(code('e400'),'','订单异常');
		}
		Db::startTrans();
		$ret = $orderModel->collect_goods($user_id,$id);
		$sql = "update ".config('prefix')."admin set balance=balance+".$nice_pay." where id='".$admin_id."'";
		$ret2 = Db::execute($sql);
		if($ret && ($ret2||$nice_pay==0)) {	
			Db::commit();
			$arr = [];
			$arr['goods_name'] = $data['title'];
			$arr['attr_id'] = $data['goods_attr_id'];
			$arr['num'] = $data['number'];
			$arr['order_num'] = $data['order_num'];
			$arr['price'] = $data['price'];
			$msgModel = new msgModel();
			$msgModel->order_finshed($user_id,$arr);
			return json(code('e200'),'','');
		}else {
			Db::rollback();
			return json(code('e400'),$ret2,'确认收货失败'.$sql);
		}
	}

	//众筹订单
	public function crowd_funding()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$goodsAttrModel = new goodsAttrModel();
		$goodsModel = new goodsModel();
		$orderModel = new orderModel();
		$userModel = new userModel();
		$number = input('number');
		if(!is_numeric($number)) return json(code('e400'),'','商品输量选择错误');
		$attr_id = input('attr_id');
		$attr_info = $goodsAttrModel->get_by_id($attr_id);
		if(!$attr_info) return json(code('e400'),'','无该商品信息');
		$goods_id = $attr_info['goods_id'];
		$goods_info = $goodsAttrModel->get_by_GoodsId_AttrId($goods_id,$attr_id);
		if(!$goods_info) return json(code('e400'),'','无该商品信息');
		$title = $goods_info['title'];
		$attr_str = $goods_info['attr_info'];
		$admin_id = $goods_info['admin_id'];
		$jiapu_id = $userModel->where('id',$user_id)->value('jiapu_id');
		$status = 1;
		$create_time = time();
		$address = input('address');
		$phone = input('phone');
		$name = input('name');
		$remark = input('remark');
		$order_num = build_order_num();
		$price = $attr_info['price'];
		$data = [];
		$data['user_id'] = $user_id;
		$data['jiapu_id'] = $jiapu_id;
		$data['goods_attr_id'] = $attr_id;
		$data['goods_id'] = $goods_id;
		$data['order_num'] = $order_num;
		$data['title'] = $title;
		$data['price'] = $price;
		$data['number'] = $number;
		$data['name'] = $name;
		$data['remark'] = $remark;
		$data['phone'] = $phone;
		$data['address'] = $address;
		$data['attr_info'] = $attr_str;
		$data['admin_id'] = $admin_id;
		$data['status'] = $status;
		$data['crowd_funding'] = 1;
		$data['create_time'] = $create_time;
		Db::startTrans();
		$change_num = 0-$number;
		$ret_1 = $goodsAttrModel->change_num($attr_id,$change_num);
		$ret_2 = $orderModel->build($data);
		if($ret_1 && $ret_2)
		{
			Db::commit();
			$data = [];
			$data['order_num'] = $order_num;
			return json(code('e200'),$data,'众筹成功');
		}else{
			Db::rollback();
			return json(code('e400'),'','众筹失败');
		}
	}

	//获取众筹列表
	public function get_crowd()
	{
		$goodsAttrModel = new goodsAttrModel();
		$goodsModel = new goodsModel();
		$orderModel = new orderModel();
		$userModel = new userModel();
		$CrowdFundingModel = new CrowdFundingModel;
		$user_info = User::is_login(true);
		$user_id = $user_info['id'];
		$jiapu_id = $user_info['jiapu_id'];
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		if(!$jiapu_id)
		{
			return json(code('e400'),'','您还未参与家谱');
		}
		$condition = [];
		$pagesize = 10;
		if(input('pagesize') && is_numeric(input('pagesize'))) {
			$pagesize = input('pagesize');
		}
		$last_id = '';
		if(input('last_id')) {
			$last_id = input('last_id');
		}
		$condition['o.crowd_funding'] = 1;
		$list = $orderModel->get_crowd_list($jiapu_id,1,$pagesize,$condition,$last_id) ;
		foreach ($list as &$row) {
			$row['people_num'] = $CrowdFundingModel->get_count($row['id']);
			$row['picture'] = config('photo_path').$row['picture'];
			date('Y-m-d',intval($row['create_time']));
		}
		return json(code('e200'),$list,'');
	}

	//获取某个订单的众筹人员列表
	public function get_crowd_man() 
	{
		$CrowdFundingModel = new CrowdFundingModel;
		$user = User::is_login();
		if(!$user)
		{
			return json(code('e500'),'','用户未登录');
		}
		$order_id = input('order_id');
		if(!intval($order_id))
		{
			return json(code('e400'),'','参数错误');
		}
		$list = $CrowdFundingModel->get_list($order_id);
		
		return json(code('e200'),$list,'');
	}

}