<?php
namespace app\api\controller;
use think\Db;
use think\Controller;

class Card extends Controller
{
	//获取名片信息
	public function get_card_info()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$card_id = input('card_id');
		if(!$card_id)
		{
			return json(code('e300'),'','数据传送错误');
		}else
		{
			$card_info = Db::name('Personnel')
					->field('id,name,photo,sex,birthday,death_date,joy,detail')
					->where(['id'=>$card_id])
					->find();
			if(!$card_info)
			{
				return json(code('e201'),$card_info,'该名片不存在');
			}
			$card_info['photo'] = config('photo_path').$card_info['photo'];
			$card_info['power'] = $this->has_power($user_id,$card_id);
			$card_info['birthday'] = date('Y-m-d',$card_info['birthday']);
			$card_info['death_date'] = date('Y-m-d',$card_info['death_date']);
			return json(code('e200'),$card_info,'获取数据成功');	
		}	
	}

	//判断用户是否有修改权限
	public function has_power($user_id,$card_id) 
	{
		if(!$user_id)
		{
			return false;
		}
		if(!$card_id) 
		{
			return false;
		}
		$personnel = Db::name('personnel')->field('id,jiapuid')->where(['id'=>$card_id])->find();
		$jiapuid = $personnel['jiapuid'];
		//是否为创建者
		$is_createor = Db::name('jiapu')->where(['id'=>$jiapuid,'uid'=>$user_id])->value('id');
		$key = false;
		if($is_createor) {
			$key = true;
		}else {
			$power = Db::name('jiapuPower')->where(['user_id'=>$user_id])->value('power');
			$power_arr = [];
			if($power) {
				$power_arr = explode(',',$power);
			}
			if(in_array($card_id,$power_arr)) {
				$key = true;
			}
		}
		return $key;
	}

	//修改名片
	public function update()
	{
		$user_id = User::is_login();
		$card_id = input('card_id');
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		if(!$card_id) 
		{
			return json(code('e201'),'','名片不存在');
		}
		$key = $this->has_power($user_id,$card_id);
		if($key) {
			$data = array();
			$photo = input('photo');
			if(strlen($photo)>config('img_len'))
			{
				$photo = Image::saveBase64Image($photo);
				if(!$photo)
				{
					return json(code('e400'),'','头像保存失败');
				}
				$data['photo'] = $photo;
			}
			$data['death_date'] = strtotime(input('death_date'));
			$data['birthday'] = strtotime(input('birthday'));
			$data['name'] = input('name');
			$data['joy'] = input('joy');
			$data['detail'] = input('detail');
			$ret = Db::name('personnel')->where(['id'=>$card_id])->update($data);
			if($ret)
			{
				return json(code('e200'),'','修改名片成功');
			}else
			{
				return json(code('e400'),'','修改失败');
			}
		}else
		{
			return json(code('e401'),'','对不起！您没有这个权限');
		}

	}

}