<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\Goods as goodsModel;
use app\api\model\Cate as cateModel;
use app\api\model\GoodsAttr as goodsAttrModel;

class Goods extends Controller
{
	public function get_list()
	{
        $goodsModel = new goodsModel();
        $condition = [];
        if(input('cate_id')) {
        	$condition['g.cate_id'] = input('cate_id');
        }
        if(input('search')) {
        	$condition['g.title'] = array('like','%'.input('search').'%');
        }
        $orderBy = '';
        if(input('order')) {
        	$order = input('order');
        	if($order==2) {
        		$orderBy = 'g.sale desc';
        	}else if($order==3) {
        		$orderBy = 'r.price desc';
        	}
        }
        if( input('min') && is_numeric(input('min')) && input('max') && is_numeric(input('max')) ) {
        	$condition['r.price'] = array('between',input('min').','.input('max'));
        }else if(input('min') && is_numeric(input('min'))) {
        	$condition['r.price'] = array('>=',intval(input('min')));
        }else if(input('max') && is_numeric(input('max'))) {
        	$condition['r.price'] = array('<=',intval(input('max')));
        }
        $count = $goodsModel->get_count(1,$condition);
        $page = input('p')?input('p'):1;
        if($page<=0 || $page>$count || !is_numeric($page))
        {
            $page = 1;
        }
        $pagesize = 6;
        $list = $goodsModel->get_list(1,$page,$pagesize,$condition,$orderBy);
        foreach ($list as &$row) {
            $row['picture'] = config('photo_path').$row['picture'];
        }
        $data = [];
        $data['list'] = $list;
        $data['page'] = $page;
        $data['pagesize'] = $pagesize;
        $data['count'] = $count;
        $data['pageCount'] = ceil($count/$pagesize);
        $cateModel = new cateModel();
		$cates = $cateModel->get_list(1);
		foreach ($cates as &$row) {
            $row['photo'] = config('photo_path').$row['photo'];
        }
		$data['cates'] 		= $cates;
		$data['user_info'] 	= User::is_login(true);
        return json(code('e200'),$data,'获取数据成功');
	}


	public function detail()
	{
		$goodsModel = new goodsModel();
		$goodsAttrModel = new goodsAttrModel();
		$id = input('id');
		$goods = $goodsModel->get_goods($id);
		if($goods['icon1']) $goods['icon1'] = config('photo_path').$goods['icon1'];
		if($goods['icon2']) $goods['icon2'] = config('photo_path').$goods['icon2'];
		if($goods['icon3']) $goods['icon3'] = config('photo_path').$goods['icon3'];
		if($goods['icon4']) $goods['icon4'] = config('photo_path').$goods['icon4'];
		if($goods['picture']) $goods['picture'] = config('photo_path').$goods['picture'];
		$attrs = $goodsAttrModel->get_by_GoodsId($id);
		foreach ($attrs as &$row) {
			$str = '';
			$row['attr_info'] = json_decode($row['attr_info'],true);
			if($row['attr_info'] != [] && (count($row['attr_info']['attrs']) == count($row['attr_info']['vals']))) {
				foreach ($row['attr_info']['attrs'] as $key => $val) {
					 $str .= $val.':'.$row['attr_info']['vals'][$key].' ';
				}
			}
			$row['attr'] = $str;
		}
		$list = [];
		$goods['detail'] = htmlspecialchars_decode($goods['detail']);
		$list['goods'] = $goods;
		$list['attrs'] = $attrs;
		return json(code('e200'),$list,'获取数据成功');
	}

	//查库存
	public function count_check()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$attr_id = input('attr_id');
		if(!$attr_id || !is_numeric($attr_id)) 
		{
			return json(code('e400'),'','请选择商品属性');
		}
		$goodsAttrModel = new goodsAttrModel();
		$goodsAttr = $goodsAttrModel->get_by_id($attr_id);
		if(!$goodsAttr)
		{
			return json(code('e400'),'','没有该商品属性');
		}
		if($goodsAttr['count']<=0)
		{
			return json(code('e400'),'','库存不足');
		}
		return json(code('e200'),'','检测通过');
	}
}