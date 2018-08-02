<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\Jiapu as jiapuModel;
use app\api\model\User as userModel;
use app\api\model\Personnel as personnelModel;
use app\api\model\Group as groupModel;

class Jiapu
{

	//家谱总图
	public function show()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$jiapu=Db::name('user')->where(['id'=>$user_id])->find()['jiapu_id'];
		if(!$jiapu)
		{
			return json(code('e201'),'','您还未创建家谱');
		}
		$list = Db::name('personnel')->where(['jiapuid'=>$jiapu])->select();
		$max_layer = -99;
		$data = '[';
		foreach ($list as $row) {
			if($row['layer'] > $max_layer) {
				$max_layer = $row['layer'];
			}
			$imgs[$row['id']] = $row['photo']?config('photo_path').$row['photo']:'';
			$sex = $row['sex']==1?'M':'F';
			if($row['joy']) {
				$row['name'] = $row['name'].' ('.$row['joy'].')';
			}
			if(!$imgs[$row['id']]) $imgs[$row['id']] = $row['sex']==1?"http://zupu.com/images/m_icon.jpg":"http://zupu.com/images/f_icon.jpg";
			// $a = $row['sex']==1?'["C", "I"]':'["B", "H", "L", "S"]';
			$data .= '{key:'.$row['id'].',n:"'.$row['name'].'",s:"'.$sex.'"';
			$data .= $row['mother']==0?'':',m:'.$row['mother'];
			$data .= ',layer:'.$row['layer'];
			$data .= ',img:"'.$imgs[$row['id']].'"';
			$data .= $row['husband']==0?'':',hb:'.$row['husband'];
			$data .= $row['father']==0?'':',f:'.$row['father'];
			$data .= $row['wife']==0?'},':',ux:'.$row['wife'].'},';
		}
		$data = $data.']';
		// $imgs = json_encode($imgs);
		$arr = [];
		$arr['data'] = $data;
		// $arr['imgs'] = $imgs;
		$arr['max_layer'] = $max_layer;
		return json(code('e200'),$arr,'操作成功');
	}

	public function create()
	{
		$user_info = User::is_login(true);
		$user_id = $user_info['id'];
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
		$jiapu=Db::name('user')->where(['id'=>$user_id])->find()['jiapu_id'];
		if($jiapu)
		{
			return json(code('e202'),'','您已有家谱');
		}
		$name = input('name');
		$password = input('password');
		$photo = input('photo');

		if(is_null($name) || is_null($password) || is_null($photo))
		{
			return json(code('e300'),'','信息填写不完整');
		}
		$photo = Image::saveBase64Image($photo);
        if(!$photo)
        {
            return json(code('e400'),'','头像保存失败');
        }
		$data = array();
		$data['uid'] = $user_id;
		$data['name'] = $name;
		$data['password'] = md5($password);
		$data['photo'] = $photo;
		$data['create_time'] = time();
		$had = Db::name('jiapu')->where('name',$name)->value('id');
		if($had) {
			return json(code('e400'),'','该家谱名称已被占用，请更改名称');
		}
		Db::startTrans();
		$jiapu_id = Db::name('jiapu')->insertGetId($data);
		$ret = Db::name('user')->where(['id'=>$user_id])->update(['jiapu_id'=>$jiapu_id]);
		$pData = array();
		$pData['jiapuid'] = $jiapu_id;
		$pData['uid'] = $user_id;
		$pData['sex'] = $user_info['sex'];
		$pData['name'] = $user_info['nickname'];
		$pData['birthday'] = date('Y-m-d',$user_info['birthday']);
		$pData['create_time'] = time();
		$pData['power'] = 10;
		$pData['layer'] = 0;
		$j 				= Db::name('personnel')->insert($pData);
		$rong 			= new Rongyun();
		$rong_create 	= $rong->create_group($user_id, $jiapu_id, $name, $photo);
		if($ret && $jiapu_id && $j && $rong_create)
		{
			Db::commit();
			return json(code('e200'),'','恭喜您！成功创建家谱！');
		}else
		{
			Db::rollback();
			return json(code('e400'),'','操作失败');
		}
	}	

	//是否已经创建过
	public function is_create()
	{
		$user_id = User::is_login();
		if($user_id)
		{
			$jiapu=Db::name('user')->where(['id'=>$user_id])->find()['jiapu_id'];
			if($jiapu)
			{
				return json(code('e202'),'','已有家谱');
			}else
			{
				return json(code('e200'),'','');
			}
		}else
		{
			return json(code('e500'),'','用户未登录:');
		}
	}

	//是否为家谱创建者
	public function is_creator()
	{
		$user_id = User::is_login();
		$jiapuModel = new jiapuModel();
		if($user_id)
		{
			$jiapu=Db::name('user')->where(['id'=>$user_id])->find()['jiapu_id'];
			if($jiapu)
			{
				$rong = new Rongyun();
				$jiapuid = $jiapuModel->is_creator($user_id,$jiapu);
				if(!$jiapuid) {
					return json(code('e400'),$rong->groupSync($user_id, $jiapu),'');
				}else{
					return json(code('e200'),'','');
				}
			}else {
				return json(code('e400'),'','');
			}
		}else
		{
			return json(code('e500'),'','用户未登录:');
		}
	}

	//加入家谱
	public function join() 
	{
		$user_id = User::is_login();
		if($user_id)
		{
			$jiapu=Db::name('user')->where(['id'=>$user_id])->find()['jiapu_id'];
			if($jiapu)
			{
				return json(code('e202'),'','您已有家谱');
			}else
			{
				$jiapuModel = new jiapuModel();
				$userModel = new userModel();
				$name = input('name');
				$password = input('password');
				$jiapu_id = $jiapuModel->check($name,$password);
				$rong 	  = new Rongyun();
				$rong_ret = $rong->join_group($user_id, $jiapu_id) ;
				if($jiapu_id) {
					$ret = $userModel->update_jiapu($user_id,$jiapu_id);
					if($ret) {
						if(!$rong_ret) {
							return json(code('e200'),'','家谱加入成功, 家族群聊加入失败');
						}
						return json(code('e200'),'','加入成功');
					}else{
						return json(code('e400'),'','加入失败');
					}
				}else {
					return json(code('e201'),'','家谱不存在或密码错误！');
				}
			}
		}else
		{
			return json(code('e500'),'','用户未登录:');
		}
	}

	//获取家族权限界面列表
	public function get_power_list()
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$jiapuModel = new jiapuModel();
		$userModel = new userModel();
		$personnelModel = new personnelModel();
		$jiapu_id = $jiapuModel->get_by_user($user_id);
		if(!$jiapu_id) {
			return json(code('e201'),'','家谱信息不存在');
		}
		$user_list = $userModel->get_by_jiapu($jiapu_id,$user_id);
		$personnel_list = $personnelModel->getAllByJiapuId($jiapu_id);
		$data = [];
		$data['user_list'] = $user_list;
		$data['personnel_list'] = $personnel_list;
		return json(code('e200'),$data,'');
	}

	//获取家族聊天成员列表
	public function get_chat_list()
	{
		$user_info = User::is_login(true);
		$user_id = $user_info['id'];
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$jiapuModel = new jiapuModel();
		$userModel = new userModel();
		$personnelModel = new personnelModel();
		$jiapu_id = $user_info['jiapu_id'];
		if(!$jiapu_id) {
			return json(code('e201'),'','家谱不存在');
		}
		$user_list = $userModel->get_by_jiapu($jiapu_id,$user_id);
		foreach ($user_list as &$row) {
			$row['photo'] = config('photo_path').$row['photo'];
		}
		$groupModel = new groupModel();
		$group 		= $groupModel->get_by_jiapuId($jiapu_id);
		if($group) {
			$group['photo'] 	= config('photo_path').$group['photo'];
			$group['id'] 		= 0 - $group['id']; 
			$group['nickname'] 	= $group['name']; 
			$user_list[]		= $group;
		}
		return json(code('e200'),$user_list,'');
	}
}