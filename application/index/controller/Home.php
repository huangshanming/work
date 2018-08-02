<?php
namespace app\Index\controller;
use think\Db;
use think\Controller;

class Home extends Controller
{
	public function index()
	{
		$this->assign('title','首页');
		return view();
	}
	public function jiapu ()
	{
		$this->assign('title','家谱');
		return view();
	}
	public function jiapu_create ()
	{
		$this->assign('title','家谱创建');
		return view();
	}
	public function personnel_create()
	{
		$this->assign('title','家族成员');
		return view();
	}
	public function chat()
	{
		$this->assign('title','亲缘社交');
        return $this->fetch();
	}
	public function card()
	{
		$this->assign('title','名片');
		return view();
	}
	public function model()
	{
		$this->assign('title','商城');
		return view();
	}
	public function buy()
	{
		$this->assign('title','商城');
		return view();
	}
	public function other_buy()
	{
		$this->assign('title','商城');
		return view();
	}
	public function other_pay()
	{
		$this->assign('title','商城');
		return view();
	}
	public function person_order() 
	{
		$this->assign('title','订单');
		return view();
	}
	public function register() 
	{
		$this->assign('title','注册');
		return view();
	}
	public function jiapu_search() 
	{
		$this->assign('title','搜索');
		return view();
	}
	public function jiapu_power()
	{
		$this->assign('title','家谱权限');
		return view();
	}
	public function baijia()
	{
		$this->assign('title','百家姓');
		return view();
	}
	public function baijia_detail() 
	{
		$this->assign('title','百家姓');
		return view();
	}
	public function news()
	{
		$this->assign('title','我的消息');
		return view();
	}
	public function info()
	{
		$this->assign('title','个人信息');
		return view();
	}
	public function crowd_funding()
	{
		$this->assign('title','众筹列表');
		return view();
	}
	public function album()
	{
		$this->assign('title','相册');
		return view();
	}
	public function album_upload()
	{
		$this->assign('title','相片上传');
		return view();
	}
	public function zhuanji()
	{
		$this->assign('title','人物传记');
		return view();
	}
	public function zhuanji_detail() 
	{
		$this->assign('title','传记');
		return view();
	}
}