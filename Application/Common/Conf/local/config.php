<?php
return array(
    // '配置项'=>'配置值'
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
	'DB_NAME' => 'waifood',
	'DB_USER' => 'root',
	'DB_PWD' => 'root',
    'DB_PORT' => '3306',
    'SHOW_ERROR_MSG' => true,
    'DB_PREFIX' => 'my_',
    // Redis配置
    "REDIS_HOST"=>'192.168.2.250',
    "REDIS_PORT"=>'6379',
    'DOMAIN' => 'http://www.waifood.com/',

    'WECHAT_APPID' => 'wx50d2c7139fe6cd3e',
    'WECHAT_APPSECRET' => 'a7cd48cf28e8c4adb043c0e3a9ffb7a4',
    'UPLOAD_PATH'=>'D:/WAMP/waifood/Public/',//图片上传位置
);
?>