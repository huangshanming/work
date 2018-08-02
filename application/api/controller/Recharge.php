<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\User as userModel;
use app\api\model\GoodsAttr as goodsAttrModel;
use app\api\model\Order as orderModel;
use app\api\model\Goods as goodsModel;
use app\api\model\Msg as msgModel;
use app\api\model\Recharge as rechargeModel;

class Recharge extends Controller
{
	public function confirm() 
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$money 			= input('money');
		$rechargeModel 	= new rechargeModel();
		if(!is_numeric($money) || $money<=0)
		{
			return json(code('e400'),'','非法操作');
		}else
		{
	        $outTradeNo 			= build_order_num();
	        $data 					= [];
	        $data['recharge_num'] 	= $outTradeNo;
	        $data['user_id'] 		= $user_id;
	        $data['money'] 			= $money;
	        $data['create_time'] 	= time();
	        $data['status'] 		= 0;
	        $ret  					= $rechargeModel->insert($data);
	        if(!$ret) {
	        	return json(code('e400'),'','出现异常');
	        }
	        //创建订单表的时候注意要有这样一个生成唯一订单号的字段，并且不能太短，不然会报错的
	        //商品名称
	        $goodsName = "亲链币充值";
	        $wxpayApi=PAY_PATH.'wx/lib/WxPay.Api.php';//引入
	        include($wxpayApi);
	        $input = new \WxPayUnifiedOrder();
	        $input->SetBody("亲链币充值");
	        $input->SetAttach("亲链币充值");
	        $input->SetOut_trade_no($outTradeNo);//订单号
	        $money=($money*100);
	        $input->SetTotal_fee($money);//总额 int  单位 分
	        // $input->SetTime_start(date("YmdHis"));
	        // $input->SetTime_expire(date("YmdHis", time() + 600));//失效时间
	        // $input->SetGoods_tag("test");
	        $input->SetNotify_url("http://www.****.com/index.php/index/Order/notify");
	        $input->SetTrade_type("NATIVE");
	        $input->SetProduct_id(1);//商品id
	        $result = \WxPayApi::unifiedOrder($input);
	        $url = 'http://paysdk.weixin.qq.com/qrcode.php?data='.urlencode(@$result["code_url"]);
	        $recharge_info					= [];
	        $recharge_info['url'] 			= $url;
	        $recharge_info['recharge_num'] 	= $outTradeNo;
			return json(code('e200'), $recharge_info, '获取成功');
		}
	}

	//查询是否支付成功
	public function get_recharge_status() {
        $wxpayApi=PAY_PATH.'wx/lib/WxPay.Api.php';//引入
        include($wxpayApi);
        $recharge_num 	= input('recharge_num');
		$queryOrderInput = new \WxPayOrderQuery();
		$queryOrderInput->SetOut_trade_no($recharge_num);
		$result = \WxPayApi::orderQuery($queryOrderInput);
		if($result["return_code"] == "SUCCESS" 
			&& $result["result_code"] == "SUCCESS")
			{
			//支付成功
			if($result["trade_state"] == "SUCCESS"){
				$succCode = 1;
				$this->recharge_finish($recharge_num);
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

	protected function recharge_finish($recharge_num) 
	{
		$rechargeModel 			= new rechargeModel();
		$where 					= [];
		$data 					= [];
		$where['recharge_num']	= $recharge_num;
		$data['status']			= 1;
		$data['pay_time']		= time();
		$recharge_info 			= $rechargeModel->where($where)->find();
		if(!$recharge_info) {
			return false;
		}
		Db::startTrans();
		$ret 				= $rechargeModel->where($where)->update($data);
		$sql = 'update '.config('prefix').'user set balance=balance + :money WHERE id = :id';
		$ret2 = Db::execute($sql,['id'=>$recharge_info['user_id'],'money'=>$recharge_info['money']]);
		if($ret && $ret2) {
			Db::commit();
			return true;
		}else {
			Db::rollback();
			return false;
		}
	}	
}