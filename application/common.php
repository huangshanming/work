<?php
use think\Db;
use Org\Xb\RongCloud;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//返回前端json数据
function json($code='',$arr='',$msg='')
{
	$data = [];
	$data['code'] = $code;
	$data['data'] = $arr;
	$data['msg'] = $msg;
	return $data;
}

//获取融云token
function get_rongyun_token($id,$name,$photo) 
{
    $url = 'https://api.cn.rong.io/user/getToken.json';

    $postData = 'userId='.$id.'&name='.$name.'&portraitUri='.$photo;

    // post提交-推送
    $row = object_array(json_decode(request_post_push($url, $postData)));
    if($row['code'] == '200' ){ // 返回码200 为正常
        return $row['token'];
    }else{
        return '';
    }
}

//生成token
function get_token($user_id)
{
	$str = '';
	$str = md5($user_id.get_rand(10).time());
	return $str;
}

//获取随机字符串
function get_rand($length = 10) { 
	// 密码字符集，可任意添加你需要的字符 
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|'; 
	$password = ''; 
	for ( $i = 0; $i < $length; $i++ ) 
	{ 
		$password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
	} 
	return $password; 
} 


/**
 * 根据配置项获取对应的key和secret
 * @return array key和secret
 */
function get_rong_key_secret(){
    // 判断是需要开发环境还是生产环境的key
    if (config('RONG_IS_DEV')) {
        $key=config('RONG_DEV_APP_KEY');
        $secret=config('RONG_DEV_APP_SECRET');
    }else{
        $key=config('RONG_PRO_APP_KEY');
        $secret=config('RONG_PRO_APP_SECRET');
    }
    $data=array(
        'key'=>$key,
        'secret'=>$secret
        );
    return $data;
}

/**
 * 获取融云token
 * @param  integer $uid 用户id
 * @return integer      token
 */
function get_rongcloud_token($uid){
    // 从数据库中获取token
    $token=model('OauthUser')->getToken($uid,1);
    // 如果有token就返回
    if ($token) {
        return $token;
    }
    // 获取用户昵称和头像
    $user_data=Db::name('User')->field('name,photo')->getById($uid);
    $user_data['photo'] = $user_data['photo']?$user_data['photo']:'';
    // 用户不存在
    if (empty($user_data)) {
        return false;
    }
    // 获取头像url格式
    $photo=get_url($user_data['photo']);
    // 获取key和secret
    $key_secret=get_rong_key_secret();
    // 实例化融云
    $rong_cloud=new RongCloud($key_secret['key'],$key_secret['secret']);
    // 获取token
    $token_json=$rong_cloud->getToken($uid,$user_data['name'],$photo);
    $token_array=json_decode($token_json,true);
    // 获取token失败
    if ($token_array['code']!=200) {
        return false;
    }
    $token=$token_array['token'];
    $data=array(
        'uid'=>$uid,
        'type'=>1,
        'nickname'=>$user_data['name'],
        'head_img'=>$photo,
        'access_token'=>$token
        );
    // 插入数据库
    $result=model('OauthUser')->addData($data);
    if ($result) {
        return $token;
    }else{
        return false;
    }
}



/**
 * 获取完整网络连接
 * @param  string $path 文件路径
 * @return string       http连接
 */
function get_url($path){
    // 如果是空；返回空
    if (empty($path)) {
        return '';
    }
    // 如果已经有http直接返回
    if (strpos($path, 'http://')!==false) {
        return $path;
    }
    // 判断是否使用了oss
    $alioss=C('ALIOSS_CONFIG');
    if (empty($alioss['KEY_ID'])) {
        return 'http://'.$_SERVER['HTTP_HOST'].$path;
    }else{
        return 'http://'.$alioss['BUCKET'].'.'.$alioss['END_POINT'].$path;
    }
    
}

/**
 * 更新融云头像
 * @param  integer $uid 用户id
 * @return boolear      操作是否成功
 */
function refresh_rongcloud_token($uid){
    // 获取用户昵称和头像
    $user_data=Db::name('User')->field('username,avatar')->getById($uid);
    // 用户不存在
    if (empty($user_data)) {
        return false;
    }
    $avatar=get_url($user_data['avatar']);
    // 获取key和secret
    $key_secret=get_rong_key_secret();
    // 实例化融云
    $rong_cloud=new \Org\Xb\RongCloud($key_secret['key'],$key_secret['secret']);
    // 更新融云用户头像
    $result_json=$rong_cloud->userRefresh($uid,$user_data['username'],$avatar);
    $result_array=json_decode($result_json,true);
    if ($result_array['code']==200) {
        return true;
    }else{
        return false;
    }
}

//生成订单号
function build_order_num()
{
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $orderSn;
}


//返回错误码
function code($code)
{
	//2数据请求   3参数类型   4操作   5登录
	$data = [
		'e200' 	=> 	200,//请求成功
		'e201'	=> 	201,//数据不存在
		'e202'	=> 	202,//数据重复
		'e300'	=> 	300,//缺少必要参数
		'e400'  =>	400,//操作失败
        'e401'  =>  401,//没有权限
		'e500'	=>	500,//用户未登录
	];
	return $data[$code];
}

//获取图片全路径
function getPicUrl($url,$type = 'index')
{
    return config('photo_path').$url;
}

//订单状态转换 1待付款 2已付款 3已发货 4完成 -1用户取消 -2商户取消 -3删除
function getOrderStatus($status) 
{
    switch ($status) {
        case '1':
            return '待付款';
            break;
        case '2':
            return '已付款';
            break;
        case '3':
            return '已发货';
            break;
        case '4':
            return '完成';
            break;
        case '-1':
            return '用户取消';
            break;
        case '-2':
            return '商户取消';
            break;
        case '-3':
            return '删除';
            break;
        default:
            return '';
            break;
    }
}   




