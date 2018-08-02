<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use think\Session;

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

        //用户是否存在
        $user=Db::name('user')->where($where)->find();
        if(!$user){
            return json(code('e201'),'','帐号或密码错误');
        }else{
            $token = get_token($user['id']);
            $ret = Db::name('user')->where('id='.$user['id'])->update(['token'=>$token]);
            if(!$ret)
            {
                return json(code('e400'),'','登录异常');
            }
            $data = [];
            $data['token'] = $token;
            $user['photo'] = config('photo_path').$user['photo'];
            $data['user_info'] = $user;
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
            $ret = Db::name('user')->where($where)->find();
        }else
        {
            $ret = Db::name('user')->field('id')->where($where)->find()['id'];
        }
        if($ret)
        {
            return $ret;
        }else
        {
            return false;
        }
    }

    public function register()
    {
        if(is_null(input('username')) || is_null(input('password')) || is_null(input('birthday')) || is_null(trim(input('nickname'))) || is_null(input('sex')))
        {
            return json(code('e300'),'','缺少必要参数');
        }
        $where          = [];
        $username       = input('username');
        $where['name']  = $username;
        $birthday       = strtotime(input('birthday'));
        $nickname       = trim(input('nickname'));
        $sex            = intval(input('sex'));
    	$regs=Db::name('user')->where($where)->find();
        if($regs){
            return json(code('e202'),'','用户已存在');
        }else{
            $data                   = [];
            $data['name']           = $username;
            $data['password']       =md5(input('password'));
            $data['create_time']    =time();
            $data['birthday']       = $birthday;
            $data['nickname']       = $nickname;
            $data['sex']            = $sex;
            $data['login_ip']       = $_SERVER["REMOTE_ADDR"];
            $reg=Db::name('user')->insert($data);
            if($reg){
                return json(code('e200'),'','恭喜您，注册成功');
            }else{
                return json(code('e400'),'','注册失败');
            }    
        }
    }

    /**
     * 传递一个、或者多个用户id
     * 获取用户头像用户名；用来组合成好友列表
     */
    public function get_user_info(){
        $uids=input('post.uids');
        // 组合where数组条件
        $map=array(
            'id'=>array('in',$uids)
            );
        $data=Db::name('User')
            ->field('id,name,photo,nickname,birthday,sex,phone,address')
            ->where($map)
            ->select();
        return json(code('e200'),$data,'获取用户信息成功');
    }

     public function get_info(){
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $map = [];
        $map['id'] = $user_id;
        $data=Db::name('User')
            ->field('id,name,photo,nickname,birthday,sex,phone,address')
            ->where($map)
            ->find();
        $data['photo'] = config('photo_path').$data['photo'];
        $data['birthday'] = date('Y-m-d',$data['birthday']);
        return json(code('e200'),$data,'获取用户信息成功');
    }


    public function rong(){
        // 模拟id为89的用户的登录过程
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $user_data=Db::name('User')->field('id,name,photo')->where(['id'=>$user_id])->find();
        $_SESSION['user']=array(
            'id'=>$user_data['id'],
            'name'=>$user_data['name'],
            'photo'=>$user_data['photo']
            );
        // 获取融云key
        $rong_key_secret=get_rong_key_secret();
        $user_data['photo'] = $user_data['photo']?$user_data['photo']:'';
        $assign=array(
            'uid'=>$user_data['id'], // 用户id
            'photo'=>$user_data['photo'],// 头像
            'name'=>$user_data['name'],// 用户名
            'rong_key'=>$rong_key_secret['key'],// 融云key
            'rong_token'=>get_rongcloud_token($user_data['id'])//获取融云token
            );
        return json(code('e200'),$assign,'登录聊天成功');
    }

    public function update_info()
    {
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        
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
        $data['sex'] = input('sex');
        $data['address'] = input('address');
        $data['phone'] = input('phone');
        $data['birthday'] = strtotime(input('birthday'));
        $data['nickname'] = input('nickname');
        $ret = Db::name('User')->where(['id'=>$user_id])->update($data);
        if($ret)
        {
            return json(code('e200'),'','修改信息成功');
        }else
        {
            return json(code('e400'),'','修改失败');
        }
    }

} 