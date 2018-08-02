<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept,user_token");
header('Access-Control-Allow-Methods: GET, POST, PUT');
define('APP_PATH', __DIR__ . '/../application/');
define('PUBLIC_PATH', __DIR__ . '/');
define('PAY_PATH', __DIR__.'/pay/'); 
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
