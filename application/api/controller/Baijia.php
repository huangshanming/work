<?php
namespace app\api\controller;
use think\Db;
use think\Controller;

class Baijia extends Controller
{
	public function get_list() {
		$page = input('page');
		$pagesize = input('pagesize');
		$count = Db::name('Baijia')->count();
		if(!$pagesize || !is_numeric($pagesize)) {
			$pagesize = 10;
		}
		$max_page = ceil($count/$pagesize);
		if(!$page || !is_numeric($page) || $page<=0 || $page>$max_page) {
			$page = 1;
		}
		$offset = ($page-1)*$pagesize;
		$list = Db::name('Baijia')
			->limit($offset,$pagesize)
			->select();
		$data = [];
		$data['list'] = $list;
		$data['page'] = $page;
		$data['max_page'] = $max_page;
		$data['count'] = $count;
		return json(code('e200'),$data,'');
	}
	public function detail() {
		$id = input('id');
		if(!is_numeric($id)) {
			return json(code('e201'),'','数据不存在');
		}
		$list = Db::name('Baijia')
			->where('id',$id)
			->find();
		$data = [];
		$data['title'] = $list['name'];
		$data['detail'] = htmlspecialchars_decode($list['content']);
		return json(code('e200'),$data,'');
	}
}