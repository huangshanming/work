<?php
namespace app\admin\model;
use think\Model;
/**
 * 基础model
 */
class Goods extends Model{
    public function add_goods(array $data)
    {
        return $this->insertGetId($data);
    }

    public function get_count($admin_id,$status=null,$condition='')
    {
    	$where = [];
    	if($status!==null)
    	{
    		$where['status'] = $status;
    	}
    	$where['admin_id'] = $admin_id;
    	return $this->where($where)->where($condition)->count();
    }	

    public function get_list($admin_id, $status=null, $page=1, $pagesize=10,$condition='')
    {
        $where = [];
        if($status!==null)
        {
            $where['g.status'] = $status;
        }
        $where['g.admin_id'] = $admin_id;
        $total = ($page-1)*$pagesize;
        return $this->alias('g')
            ->field('g.id,g.title,g.cate_id,g.picture,g.status,g.create_time,c.name as cate_name,a.name as admin_name')
            ->join('cate c','c.id=g.cate_id','left')
            ->join('admin a','a.id=g.admin_id','left')
            ->where($where)
            ->where($condition)
            ->limit($total,$pagesize)
            ->order('id desc')
            ->select();
    }

    public function get_by_attrId($admin_id,$attrId)
    {
        $where = [];
        $where['g.admin_id'] = $admin_id;
        $where['r.id'] = $attrId;
        return $this->alias('g')
        ->field('g.id,r.id as attr_id,g.title,g.icon1,g.icon2,g.icon3,g.icon4,r.price,r.count,g.cate_id,r.attr_info,r.attrs_id,g.detail')
        ->join('goods_attr r','r.goods_id=g.id')
        ->where($where)
        ->find();

    }

    public function update_goods($data,$where)
    {
        $id = $where['id'];
        $admin_id = $where['admin_id'];
        if(!is_numeric($id) || !is_numeric($admin_id)) return false;
        $where = [];
        $where['id'] = $id;
        $where['admin_id'] = $admin_id;
        return $this->where($where)->update($data);
    }

    public function del($admin_id,$goods_id) 
    {
        $where = [];
        $where['id'] = $goods_id;
        $where['admin_id'] = $admin_id;
        return $this->where($where)->update(['status'=>2]);
    }

    public function get_by_id($admin_id,$id)
    {
        $where = [];
        $where['g.admin_id'] = $admin_id;
        $where['g.id'] = $id;
        return $this->alias('g')
        ->field('g.id,g.title,g.picture,g.icon1,g.icon2,g.icon3,g.icon4,g.cate_id,g.detail')
        ->where($where)
        ->find();

    }

}
