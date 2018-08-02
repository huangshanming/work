<?php
namespace app\api\model;
use think\Db;
use think\Model;

class Personnel extends Model
{
	public function getByUid($uid)
	{
		$data = DB::name('personnel')
			->field('id,name,sex,power,birthday,jiapuid,wife,husband,mother,father')
			->where(['uid'=>$uid])
			->find();
		return $data;
	}

	public function getById($id)
	{
		$data = DB::name('personnel')
			->field('id,name,sex,power,jiapuid,wife,husband,mother,father,layer')
			->where(['id'=>$id])
			->find();
		return $data;
	}

	public function getByJiapuId($jiapuid,array $where=array()) 
	{
		$where = $where?$where:'';
		$data = DB::name('personnel')
			->alias('p')
			->field('p.id,p.name,p.uid,p.sex,p.birthday,p.power,p.jiapuid,p.wife,p.husband,u.name as user_name,u.photo')
			->join('user u','u.id=p.uid','left')
			->where('p.uid<>0')
			->where('u.id<>0')
			->where($where)
			->select();
		return $data;
	}

	public function getAllByJiapuId($jiapuid) 
	{
		$where = [];
		$where['jiapuid'] = $jiapuid;
		$data = DB::name('personnel')
			->alias('p')
			->field('p.id,p.name,p.uid,p.sex,p.birthday,p.power,p.jiapuid,p.wife,p.husband')
			->where($where)
			->select();
		return $data;
	}

	public function insert(array $arr)
	{
		if($arr)
		{
			return DB::name('personnel')->insertGetid($arr);
		}else
		{
			return false;
		}
	}

	public function modify(array $where,array $data)
	{
		if($where&&$data)
		{
			return DB::name('personnel')->where($where)->update($data);
		}else
		{
			return false;
		}
	}

	//根据id获取成员
	public function get_by_ids($ids)
	{
		$where['id'] = array('in',$ids);
		return $this->where($where)->field('id,name')->select();
	}
}