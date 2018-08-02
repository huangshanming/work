<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Session;
use think\Request;

class User extends Controller
{
	
	/**
    * 登陆
    */ 
    public function login()
    {   
        if(is_null(input('username')) || is_null(input('password')))
        {
            return json(code('e300'),'','缺少必要参数');
        }
        $where['name']=input('username');
        $where['password']=md5(input('password'));
        $where['status'] = 1;

        //用户是否存在
        $user=Db::name('admin')->where($where)->find();
        if(!$user){
            return json(code('e201'),'','帐号或密码错误');
        }else{
            $token = get_token($user['id']);
            $uData = [];
            $uData['token'] = $token;
            $uData['login_ip'] = Request::instance()->ip();
            $uData['login_time'] = time();
            $ret = Db::name('admin')->where('id='.$user['id'])->update($uData);
            if(!$ret)
            {
                return json(code('e400'),'','登录异常');
            }
            $data = [];
            $data['token'] = $token;
            return json(code('e200'),$data,'登录成功');
        }
    }

    //是否登录
    public static function is_login($get_info = false)
    {
        $where = [];
        $where['token'] = input('user_token');
        if($get_info)
        {
            $ret = Db::name('admin')->where($where)->find();
        }else
        {
            $ret = Db::name('admin')->field('id')->where($where)->find()['id'];
        }
        if($ret)
        {
            return $ret;
        }else
        {
            return false;
        }
    }

    //是否登录
    public function has_login()
    {
        $where = [];
        $where['token'] = input('user_token');
        $ret = Db::name('admin')->field('id')->where($where)->find()['id'];
        if($ret)
        {
            return json(code('e200'),'','');
        }else
        {
            return json(code('e500'),'','用户未登录');
        }
    }

    //修改密码
    public function change_pas()
    {
        $user_id = self::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $old = md5(input('old'));
        $new = md5(input('new'));
        $where = [];
        $where['password'] = $old;
        $where['id'] = $user_id;
        $data = [];
        $data['password'] = $new;
        $ret = Db::name('admin')->where($where)->update($data);
        if($ret)
        {
            return json(code('e200'),'','修改成功');
        }else
        {
            return json(code('e400'),'','修改失败');
        }
    }

    //获取余额
    public function get_balance()
    {
        $user_id = self::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $where = [];
        $where['id'] = $user_id;
        $info = Db::name('admin')
            ->field('balance,bank_account,bank,branch,payee,payee_id')
            ->where($where)
            ->find();
        return json(code('e200'),$info,'');
    }

    //获取银行信息
    public function get_bank_info()
    {
        $user_id = self::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $where = [];
        $where['id'] = $user_id;
        $info = Db::name('admin')
            ->field('bank_account,bank,branch,payee,payee_id')
            ->where($where)
            ->find();
        return json(code('e200'),$info,'');
    }

    //修改银行信息
    public function update_bank()
    {
        $user_id = self::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $where = [];
        $where['id'] = $user_id;
        $admin_info = Db::name('admin')->where('id',$user_id)->find();
        $cipher = input('cipher');
        if(md5($cipher)!=$admin_info['cipher']) {
            return json(code('e201'),'','提现密码错误');
        }
        $data = [];
        $data['bank_account'] = input('bank_account');
        $data['branch'] = input('branch');
        $data['bank'] = input('bank');
        $data['payee'] = input('payee');
        $data['payee_id'] = input('payee_id');
        $ret = Db::name('admin')
            ->where($where)
            ->update($data);
        if($ret) {
            return json(code('e200'),'','');
        }else {
            return json(code('e400'),'','修改失败');
        }
    }

    //修改提现密码
    public function update_cipher()
    {
        $user_id = self::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $where = [];
        $where['id'] = $user_id;
        $admin_info = Db::name('admin')->where('id',$user_id)->find();
        $cipher = input('cipher');
        $new_cipher = input('new_cipher');
        if(md5($cipher)!=$admin_info['cipher']) {
            return json(code('e201'),'','提现密码错误');
        }
        if(!$new_cipher) {
            return json(code('e400'),'','提现密码不能为空');
        }
        $data = [];
        $data['cipher'] = md5($new_cipher);
        $ret = Db::name('admin')
            ->where($where)
            ->update($data);
        if($ret) {
            return json(code('e200'),'','');
        }else {
            return json(code('e400'),'','修改失败');
        }
    }
    //提现
    public function put_forward()
    {
        $user_id = self::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $where = [];
        $where['id'] = $user_id;
        $admin_info = Db::name('admin')->where('id',$user_id)->find();
        $balance = $admin_info['balance'];
        $cipher = input('cipher');
        $money = input('money');
        if(md5($cipher)!=$admin_info['cipher']) {
            return json(code('e201'),'','提现密码错误');
        }
        if(!$money || !is_numeric($money)) {
            return json(code('e400'),'','提现金额不能为空');
        }
        if($balance<$money) {
            return json(code('e400'),'','提现金额大于账户余额');
        }
        $sql = 'update '.config('prefix').'admin set balance=balance-'.$money.' where id="'.$user_id.'"';
        Db::startTrans();
        $ret = Db::execute($sql);
        $data = [];
        $data['money'] = $money;
        $data['bank'] = $admin_info['bank'];
        $data['branch'] = $admin_info['branch'];
        $data['bank_account'] = $admin_info['bank_account'];
        $data['payee'] = $admin_info['payee'];
        $data['payee_id'] = $admin_info['payee_id'];
        $data['create_time'] = time();
        $data['status'] = 1;
        $data['admin_id'] = $user_id;
        $ret2 = Db::name('put_forward')->insert($data);
        if($ret&&$ret2) {
            Db::commit();
            return json(code('e200'),'','');
        }else {
            Db::rollback();
            return json(code('e400'),'','修改失败');
        }
    }

    //获取提现列表
    public function get_balance_list() 
    {
        $user_id = self::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $p = 1;
        $pagesize = 10;
        if(input('pagesize') && is_numeric(input('pagesize'))) 
        {
            $pagesize = input('pagesize');
        } 
        if(input('p') && is_numeric(input('p'))) 
        {
            $p = input('p');
        }
        $count = Db::name('put_forward')->where('admin_id',$user_id)->count();
        $max_page = ceil($count/$pagesize);
        if($p>$max_page || $p<1) {
            $p = 1;
        }
        $total = ($p-1)*$pagesize;
        $list = Db::name('put_forward')
            ->limit($total,$pagesize)
            ->order('id desc')
            ->select();
        foreach ($list as &$row) {
            $row['create_time'] = date('Y-m-d H:i:s',$row['create_time']);
            if($row['status'] == 1) {
                $row['status'] = '待处理';
            }else if($row['status'] == 2){
                $row['status'] = '已处理';
            }
        }
        $data = [];
        $data['list'] = $list;
        $data['p'] = $p;
        $data['pagesize'] = $pagesize;
        $data['max_page'] = $max_page;
        $data['count'] = $count;
        return json(code('e200'),$data,''); 
    }
}