<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use rongyun\ServerAPI;
use think\Session;
use app\api\model\Group as group_model;
use app\api\model\User as user_model;

class Rongyun extends Controller
{
	private $rongYun;
	public function __construct() {
		$appKey = config('APP_KEY');
        $appSecret = config('APP_SECRET');
        $this->rongYun = new ServerAPI( $appKey, $appSecret );
	}
	public function get_token()
	{
		$user_info = User::is_login(true);
		if(!$user_info)
		{
			return json(code('e500'),'','用户未登录');
		}
        $tx = config('photo_path').$user_info['photo'];
        $token = $this->rongYun->getToken($user_info['id'],$user_info['name'], $tx );
        $token = json_decode( $token, true )['token'];
        Session::set('rongyun_token', $token);
        $data = [];
        $data['token'] = $token;
        return json(code('e200'),$data,'');
	}

	public function create_group($userId, $jiapu_id, $name='', $photo='') 
	{
		$jiapu_id = intval($jiapu_id);
		if(!$jiapu_id) {
			return false;
		}
		$group_model = new group_model();
		$user_model  = new user_model();
		$jiapu_group = $group_model->get_by_jiapuId($jiapu_id);
		if($jiapu_group) {
			return false;
		}
		$data 		= [
			'jiapu_id' 	=> $jiapu_id,
			'name'		=> $name,
			'user_id'	=> $userId,
			'photo'		=> $photo,
			'create_time'=> time(),
		];
		$group_model->startTrans();
		$groupId 	= $group_model->insertGetId($data);
		if(!$groupId) {
			$group_model->rollback();
			return false;
		}
		$userId 	= [];
		$userId[] 	= $userId;
		$ret 		= $this->rongYun->groupCreate($userId, $groupId, $name);
		if($ret) {
			$group_model->commit();
			return true;
		}
		$group_model->rollback();
		return false;
	}

	public function join_group($userId, $jiapu_id) 
	{
		$jiapu_id = intval($jiapu_id);
		if(!$jiapu_id) {
			return false;
		}
		$userId = intval($userId);
		if(!$userId) {
			return false;
		}
		$group_model = new group_model();
		$user_model  = new user_model();
		$jiapu_group = $group_model->get_by_jiapuId($jiapu_id);
		if(!$jiapu_group) {
			return false;
		}
		$ret 		= $this->rongYun->groupJoin($userId, $jiapu_group['id'], $jiapu_group['name']);
		if($ret) {
			return true;
		}
		return false;
	}

	public function groupSync($userId, $jiapu_id)
	{
		$jiapu_id = intval($jiapu_id);
		if(!$jiapu_id) {
			return false;
		}
		$userId = intval($userId);
		if(!$userId) {
			return false;
		}
		$group_model = new group_model();
		$jiapu_group = $group_model->get_by_jiapuId($jiapu_id);
		if(!$jiapu_group) {
			return false;
		}
		$data = [];
		$data[$jiapu_group['id']] = $jiapu_group['name'];
		return $this->rongYun->groupSync($userId, $data);
	}

	public function send() 
	{
		$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
		$appKey 	= config('APP_KEY');
        $appSecret 	= config('APP_SECRET');
        $send_id 	= $user_id;
        $to_id 		= [];
        $to_id[] 	= input('to_id');
        $content	= input('con/a');
        $objectName = input('objectName');
        $rongYun 	= new ServerAPI( $appKey, $appSecret );
		$ret = $rongYun->messagePrivatePublish($send_id, $to_id, $objectName, $content);
		return json(code('e200'),$ret,'');
	}

}