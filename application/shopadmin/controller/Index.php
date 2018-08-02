<?php
namespace app\shopAdmin\controller;
use think\Db;
use think\Controller;
use think\Session;

class Index extends Controller
{
	public function index() {
 		return view();
 	}
 	public function goods_list_edit() {
 		return view();
 	}
 	public function goods_list_add() {
 		return view();
 	}
 	public function goods_list() {
 		return view();
 	}
 	public function goods_eye() {
 		return view();
 	}
 	public function base_pwd() {
 		return view();
 	}
 	public function login() {
 		return view();
 	}
 	public function order_list() {
 		return view();
 	}
 	public function balance() {
 		return view();
 	}
 	public function balance_list() {
 		$this->assign('title','提现记录');
 		return view();
 	}
 	public function bank_info() {
 		return view();
 	}
 	public function cipher() {
 		return view();
 	}
 	public function attr_add() {
 		return view();
 	}
}