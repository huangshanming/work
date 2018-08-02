<?php
namespace app\api\model;
use think\Model;
/**
 * 基础model
 */
class Goods extends Model{
    public function add_goods(array $data)
    {
        return $this->insertGetId($data);
    }

    public function get_count($status=null,$condition='')
    {
    	$where = [];
    	if($status!==null)
    	{
    		$where['g.status'] = $status;
            $where['r.status'] = $status;
    	}
        $where['r.count'] = array('gt',0);
    	return $this->alias('g')
            ->join('goods_attr r','r.goods_id=g.id','left')
            ->where($where)
            ->where($condition)
            ->count();
    }	

    public function get_list($status=null, $page=1, $pagesize=10,$condition='',$orderBy='')
    {
        $where = [];
        if($status!==null)
        {
            $where['g.status'] = $status;
            $where['r.status'] = $status;
        }
        $where['r.count'] = array('gt',0);
        $total = ($page-1)*$pagesize;
        $this->alias('g')
            ->field('g.id,g.title,g.cate_id,g.picture,g.status,g.create_time,c.name as cate_name,r.price,r.origin_price,r.attr_info,r.attrs_id,r.id as attr_id, r.count')
            ->join('cate c','c.id=g.cate_id','left')
            ->join('goods_attr r','r.goods_id=g.id','left')
            ->where($where)
            ->where($condition)
            ->limit($total,$pagesize);
        if($orderBy) {
            return $this->order($orderBy)->select();
        }else {
            return $this->order('id desc')->select();
        }
            
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

    public function get_goods($id)
    {
        $where = [];
        $where['g.id'] = $id;
        return $this->alias('g')
        ->field('g.id,g.title,g.icon1,g.icon2,g.icon3,g.icon4,g.cate_id,g.detail,g.picture')
        ->where($where)
        ->find();
    }

}
