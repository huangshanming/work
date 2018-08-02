<?php
namespace app\admin\model;
use think\Model;
/**
 * 基础model
 */
class GoodsAttr extends Model{
    public function add_attr(array $data)
    {
        return $this->insertGetId($data);
    }

    public function update_attr($admin_id,$data,$where)
    {
    	$goods = new Goods();
    	$goods_id = $this->where($where)->value('goods_id');
    	$goods_admin = $goods->where(['id'=>$goods_id])->value('admin_id');
    	if($goods_admin!=$admin_id)
    	{
    		return false;
    	}
    	if($this->where($where)->update($data))
    	{
    		return $goods_id;
    	}else
    	{
    		return false;
    	}
    }

    public function del_attr($admin_id,$goods_id,$id)
    {
        $where = [];
        $where['admin_id'] = $admin_id;
        $where['id'] = $goods_id;
        $goods = new Goods();
        $goods = $goods->where($where)->value('id');
        if(!$goods) {
            return false;
        }
        $where = [];
        $where['id'] = $id;
        $where['goods_id'] = $goods_id;
        return $this->where($where)->update(['status'=>2]);
    }

}
