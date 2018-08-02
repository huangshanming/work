<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use app\admin\model\Cate as cateModel;
use app\admin\model\Attrs as attrsModel;
use app\admin\model\Goods as goodsModel;
use app\admin\model\GoodsAttr as goodsAttrModel;

class Attrs extends Controller
{
	public function get_list() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$p = 1;
        $pagesize = 10;
        if(input('pagesize') && is_numeric(input('pagesize'))) 
        {
            $pagesize = input('pagesize');
        } 
        if(input('p') && is_numeric(input('p'))) 
        {
            $p = input('p');
        }
		$attrsModel = new attrsModel();
		$where = [];
		$where['admin_id'] = $user_id;
		$where['status'] = 1;
        $count = Db::name('attrs')->where($where)->count();
        if($p>$count || $p<1) {
        	$p = 1;
        }
		$list = $attrsModel->get_list($p,$pagesize,$where);
		foreach ($list as &$row) {
			$row['val_list'] = Db::name('attr_value')->where('attr_id',$row['id'])
			->where('status',1)->select();
		}
		$data = [];
		$data['list'] = $list;
		$data['pagesize'] = $pagesize;
		$data['count'] = $count;
		$data['p'] = $p;
        $data['max_page'] = ceil($count/$pagesize);
		return json(code('e200'),$data,'');
	}

	public function get_all_list() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$attrsModel = new attrsModel();
		$where = [];
		$where['admin_id'] = $user_id;
		$where['status'] = 1;
		$list = $attrsModel->field('id,name')->where($where)->select();
		return json(code('e200'),$list,'');
	}

	public function add() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$pagesize = 10;
        if(input('pagesize') && is_numeric(input('pagesize'))) 
        {
            $pagesize = input('pagesize');
        }
        $name = input('name');
		$attrsModel = new attrsModel();
        if($attrsModel->had_create($user_id,$name)) {
        	return json(code('e400'),'','重复创建');
        }
        $insertData = [];
        $insertData['admin_id'] = $user_id;
        $insertData['name'] = $name;
        $insertData['create_time'] = time();
        $insertData['status'] = 1;
        $id = Db::name('attrs')->insertGetId($insertData);
        if(!$id) {
			return json(code('e400'),'','添加失败');
        }
        $data = [];
		$where = [];
		$where['admin_id'] = $user_id;
        $count = Db::name('attrs')->where($where)->count();
        $data['max_page'] = ceil($count/$pagesize);
        $list = [];
        $list['id'] = $id;
        $list['name'] = $name;
        $list['admin_id'] = $user_id;
        $list['val_list'] = [];
        $data['list'] = $list;
        return json(code('e200'),$data,'');
	}

	public function add_value() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$attr_id = input('attr_id');
		$value = input('value');
		if(!$attr_id) {
			return json(code('e400'),'','添加失败');
		}
		$where = [];
		$where['id'] = $attr_id;
		$where['admin_id'] = $user_id;
		$id = Db::name('attrs')->where($where)->value('id');
		if(!$id) {
			return json(code('e400'),'','添加失败');
		}
		$data = [];
		$data['attr_id'] = $attr_id;
		$data['value'] = $value;
		if(Db::name('attr_value')->where($data)->where('status',1)->value('id')) {
			return json(code('e400'),'','重复添加'); 
		}
		$val_id = Db::name('attr_value')->insertGetId($data);
		if(!$val_id) {
			return json(code('e400'),'','添加失败');
		}
		$data['id'] = $val_id;
		return json(code('e200'),$data,'添加成功');
	}

	public function del_value() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$id = input('id');
		if(!$id) {
			return json(code('e400'),'','删除失败');
		}
		$where = [];
		$where['id'] = $id;
		$ret = Db::name('attr_value')->where($where)->update(['status'=>2]);
		if(!$ret) {
			return json(code('e400'),'','删除失败');
		}
		return json(code('e200'),'','成功');
	}

	public function del() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$id = input('id');
		if(!$id) {
			return json(code('e400'),'','删除失败');
		}
		$attr_id = Db::name('attrs')->where('id',$id)->where('admin_id',$user_id)->value('id');
		if(!$attr_id) {
			return json(code('e400'),'','没有该属性');
		}
		$ret = Db::name('attrs')->where('id',$id)->where('admin_id',$user_id)->update(['status'=>2]);
		if(!$ret) {
			return json(code('e400'),'','删除失败');
		}
		return json(code('e200'),'','删除成功');
	}

	public function get_values() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$id = input('id');
		if(!$id) {
			return json(code('e400'),'','获取失败');
		}
		$where = [];
		$where['attr_id'] = $id;
		$where['status'] = 1;
		$list = Db::name('attr_value')->where($where)->select();
		return json(code('e200'),$list,'');
	}

	public function get_by_goods() {
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$id = input('id');
		if(!$id) {
			return json(code('e400'),'','获取失败');
		}
		$where = [];
		$where['goods_id'] = $id;
		$where['status'] = 1;
		$list = Db::name('goods_attr')->where($where)->select();
		foreach ($list as &$row) {
			$attr_info = json_decode($row['attr_info'],true);
			$str = '';
			if($attr_info!=[]) {
				foreach ($attr_info['attrs'] as $key=>$val) {
					$str .= '['.$val.':'.$attr_info['vals'][$key].'] ';
				}
			}
			$row['attr_info'] = $str;
			$row['create_time'] = date('Y-m-d H:i:s',$row['create_time']);
		}
		return json(code('e200'),$list,'');
	}
}