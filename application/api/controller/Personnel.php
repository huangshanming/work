<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use app\api\model\Personnel as personnelModel;
use app\api\model\JiapuPower as JiapuPowerModel;
use app\api\model\Jiapu as JiapuModel;

class Personnel extends Controller
{
	//添加亲人
	public function add()
	{
		$user_info = User::is_login(true);
		$user_id = @$user_info['id'];
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$JiapuPowerModel 	= new JiapuPowerModel();
		$JiapuModel 		= new JiapuModel();
		$relation 			= input('relation');
		$name 				= input('name');
		$birthday 			= input('birthday');
		$userinter 			= input('userinter');
		$card_id 			= input('card_id');
		$personnel 			= new personnelModel();
		$jiapu_list 		= $JiapuPowerModel->get_by_user($user_id);
		$jiapu_arr 			= explode(',', $jiapu_list);
		$card_info 			= $personnel->getById($card_id);
		$is_creator 		= $JiapuModel->is_creator($user_id,$card_info['jiapuid']);
		$layer 				= $card_info['layer'];
		if(in_array($card_id, $jiapu_arr) || $is_creator)
		{
			if($relation==1&&!$card_info['father']&&!$card_info['mother'])
			{
				$layer += 1;
				Db::startTrans();
				$f_data['name'] = $name;
				$f_data['joy'] = input('joy');
				$f_data['sex'] = '1';
				$f_data['birthday'] = $birthday;
				$f_data['jiapuid'] = $card_info['jiapuid'];
				$f_data['detail'] = input('detail');
				$f_data['layer'] = $layer;
				$f_id = $personnel->insert($f_data);
				$m_data['name'] = input('m_name');
				$m_data['sex'] = '2';
				$m_data['joy'] = input('m_joy');
				$m_data['birthday'] = input('m_birthday');
				$m_data['jiapuid'] = $card_info['jiapuid'];
				$m_data['husband'] = $f_id;
				$f_data['detail'] = input('m_detail');
				$m_data['layer'] = $layer;
				$m_id = $personnel->insert($m_data);
				$wf_data['wife'] = $m_id;
				$wf = $personnel->modify(['id'=>$f_id],$wf_data);
				$p_data['mother'] = $m_id;
				$p_data['father'] = $f_id;
				$p_res = $personnel->modify(['id'=>$card_id],$p_data);
				if($f_id && $m_id && $p_res && $wf){
					Db::commit();
					return json(code('e200'),'','添加成功');
				}else{
					Db::rollback();
					return json(code('e400'),'','添加失败');
				}
			}else if($relation==2&&!$card_info['father']&&!$card_info['mother'])
			{
				$layer += 1;
				Db::startTrans();
				$m_data['name'] = $name;
				$m_data['joy'] = input('joy');
				$m_data['sex'] = '2';
				$m_data['birthday'] = $birthday;
				$f_data['detail'] = input('detail');
				$m_data['jiapuid'] = $card_info['jiapuid'];
				$m_data['layer'] = $layer;
				$m_id = $personnel->insert($m_data);
				$f_data['name'] = input('f_name');
				$f_data['sex'] = '1';
				$f_data['joy'] = input('f_joy');
				$f_data['detail'] = input('f_detail');
				$f_data['birthday'] = input('f_birthday');
				$f_data['jiapuid'] = $card_info['jiapuid'];
				$f_data['wife'] = $m_id;
				$f_data['layer'] = $layer;
				$f_id = $personnel->insert($f_data);
				$hb_data['husband'] = $f_id;
				$hb = $personnel->modify(['id'=>$f_id],$hb_data);
				$p_data['mother'] = $m_id;
				$p_data['father'] = $f_id;
				$p_res = $personnel->modify(['id'=>$card_id],$p_data);
				if($f_id && $m_id && $p_res && $hb){
					Db::commit();
					return json(code('e200'),'','添加成功');
				}else{
					Db::rollback();
					return json(code('e400'),'','添加失败');
				}
			}else if($relation==5)
			{
				$layer = $layer - 1;
				$people_info = $personnel->getById($card_id);
				if(!$people_info||(!$people_info['wife']&&!$people_info['husband'])){
					return json(code('e400'),'','请先添加其配偶');
				}
				$father = $people_info['sex']==1?$card_id:$people_info['husband'];
				$mother = $people_info['sex']==2?$card_id:$people_info['wife'];
				$b_data['name'] = $name;
				$b_data['sex'] = '1';
				$b_data['detail'] = input('detail');
				$b_data['joy'] = input('joy');
				$b_data['birthday'] = $birthday;
				$b_data['jiapuid'] = $card_info['jiapuid'];
				$b_data['father'] = $father;
				$b_data['mother'] = $mother;
				$b_data['layer'] = $layer;
				$b_id = $personnel->insert($b_data);
				if($b_id){
					return json(code('e200'),'','添加成功');
				}else{
					return json(code('e400'),'','添加失败');
				}
			}else if($relation==6){
				$layer = $layer - 1;
				$people_info = $personnel->getById($card_id);
				if(!$people_info||(!$people_info['wife']&&!$people_info['husband'])){
					return json(code('e400'),'','请先添加其配偶');
				}
				$father = $people_info['sex']==1?$card_id:$people_info['husband'];
				$mother = $people_info['sex']==2?$card_id:$people_info['wife'];
				$b_data['name'] = $name;
				$b_data['sex'] = '2';
				$b_data['detail'] = input('detail');
				$b_data['joy'] = input('joy');
				$b_data['birthday'] = $birthday;
				$b_data['jiapuid'] = $card_info['jiapuid'];
				$b_data['father'] = $father;
				$b_data['mother'] = $mother;
				$b_data['layer'] = $layer;
				$b_id = $personnel->insert($b_data);
				if($b_id){
					return json(code('e200'),'','添加成功');
				}else{
					return json(code('e400'),'','添加失败');
				}
			}else if($relation==3){
				$people_info = $personnel->getById($card_id);
				if(!$people_info||(!$people_info['sex']==2)){
					return json(code('e400'),'','请勿给女性增加妻子角色');
				}
				Db::startTrans();
				$w_data['name'] = $name;
				$w_data['sex'] = '2';
				$w_data['detail'] = input('detail');
				$w_data['joy'] = input('joy');
				$w_data['birthday'] = $birthday;
				$w_data['jiapuid'] = $card_info['jiapuid'];
				$w_data['husband'] = $card_id;
				$w_data['layer'] = $layer;
				$w_id = $personnel->insert($w_data);
				$h_data['wife'] = $w_id;
				$h_id = $personnel->modify(['id'=>$card_id],$h_data);
				if($w_id && $h_id){
					Db::commit();
					return json(code('e200'),'','添加成功');
				}else{
					Db::rollback();
					return json(code('e400'),'','添加失败');
				}
			}else if($relation==4){
				$people_info = $personnel->getById($card_id);
				if(!$people_info||(!$people_info['sex']==1)){
					return json(code('e400'),'','请勿给男性增加丈夫角色');
				}
				Db::startTrans();
				$h_data['name'] = $name;
				$h_data['sex'] = '1';
				$h_data['joy'] = input('joy');
				$h_data['detail'] = input('detail');
				$h_data['birthday'] = $birthday;
				$h_data['jiapuid'] = $card_info['jiapuid'];
				$h_data['wife'] = $card_id;
				$h_data['layer'] = $layer;
				$h_id = $personnel->insert($h_data);
				$w_data['wife'] = $h_id;
				$w_id = $personnel->modify(['id'=>$card_id],$w_data);
				if($w_id && $h_id){
					Db::commit();
					return json(code('e200'),'','添加成功');
				}else{
					Db::rollback();
					return json(code('e400'),'','添加失败');
				}
			}else
			{
				return json(code('e202'),'','重复添加');
			}
		}else
		{
			return json(code('e401'),'','对不起！您没有这个权限');
		}

	}

	//获取家族成员聊天列表	
	public function get_chat_list()
	{
		$user_id = User::is_login();
		$personnel = new personnelModel();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$personnel_info = $personnel->getByUid($user_id);
		$jiapuid = $personnel_info['jiapuid'];
		$data = $personnel->getByJiapuId($jiapuid);
		$list = array();
		$slist = array();
		foreach ($data as $row) {
			$slist[] = [
				'text' => $row['user_name'].'('.$row['name'].')',
				'uid' => $row['uid'],
				'name' => $row['name'],
				'sex' => $row['sex'],
				'photo' => $row['photo'],
				'user_name' => $row['user_name'],
			];
		}
		$list = $slist;
		return json(code('e200'),$list,'获取家族成员聊天列表成功');
	}

	//获取可添加关系列表
	public function get_relation_list()
	{
		$user_id = User::is_login();
		$personnel = new personnelModel();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$card_id = input('card_id');
		$card_info = $personnel->getById($card_id);
		if(!$card_info) 
		{
			return json(code('e400'),'','没有改族员信息');
		}
		$relation_list = [
			['key'=>1,'val'=>'父亲'], 
			['key'=>2,'val'=>'母亲'], 
			['key'=>3,'val'=>'妻子'], 
			['key'=>4,'val'=>'丈夫'], 
			['key'=>5,'val'=>'儿子'], 
			['key'=>6,'val'=>'女儿'], 
		];
		$remove = [];
		if($card_info['sex']==1) 
		{	
			$remove[] = 4;
		}else if($card_info['sex']==2) 
		{	
			$remove[] = 3;
		}
		if($card_info['mother']||$card_info['father']) 
		{
			$remove[] = 1;
			$remove[] = 2;
		}
		if($card_info['husband']) 
		{
			$remove[] = 4;
		}
		if($card_info['wife']) 
		{
			$remove[] = 3;
		}
		if(($card_info['sex']==1&&!$card_info['wife']) || ($card_info['sex']==2&&!$card_info['husband'])) 
		{
			$remove[] = 5;
			$remove[] = 6;
		}
		foreach ($relation_list as $key => $row) 
		{
			if(in_array($row['key'],$remove))
			{
				unset($relation_list[$key]);
			}
		}
		$data = [];
		foreach ($relation_list as $row) {
			$data[] = $row;
		}
		return json(code('e200'),$data,'');
	}
}