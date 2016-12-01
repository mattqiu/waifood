<?php
$config = array(
    "LOAD_EXT_FILE" => "coupon",
    'MODULE_ALLOW_LIST' => array(
        'Shop',
        'Home',
        'Admin',
        'Adminzlwx',
        'Wechat'
    ),
    'DEFAULT_MODULE' => 'Shop', // 默认模块
                                
    // '配置项'=>'配置值'
	'DB_TYPE' => 'mysql',
	'DB_HOST' => '127.0.0.1',
	'DB_NAME' => 'waifood',
	'DB_USER' => 'waifood',
	'DB_PWD' => 'ek2bzd82',
	'DB_PORT' => '3306',
    'SHOW_ERROR_MSG' => true,
    'DB_PREFIX' => 'my_',
    
    // 分页设置
    'VAR_PAGESIZE' => 32,
    'DEFAULT_AVATAR' => '/Public/images/txzw.jpg',
    
    // 路由设置
    'URL_CASE_INSENSITIVE' => true,
    'URL_ROUTER_ON' => false,
    'URL_ROUTE_RULES' => array(
        'Content/:id' => 'Content/view'
    ),
    
    // 模型设置
    
    'URL_MODEL' => 2,
    'MODEL_PREFIX' => 'ext_',

		/* 文件上传相关配置 */
		'DOWNLOAD_UPLOAD' => array(
        'mimes' => '', // 允许上传的文件MiMe类型
        'maxSize' => 5 * 1024 * 1024, // 上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,flv,mp4,csv,mp3', // 允许上传的文件后缀
        'autoSub' => true, // 自动子目录保存文件
        'subName' => array(
            'date',
            'Y-m-d'
        ), // 子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Public/uploadfile/file/', // 保存根路径
        'savePath' => '', // 保存路径
        'saveName' => array(
            'uniqid',
            ''
        ), // 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', // 文件保存后缀，空则使用原后缀
        'replace' => false, // 存在同名是否覆盖
        'hash' => true, // 是否生成hash编码
        'callback' => false
    ), // 检测文件是否存在回调函数，如果存在返回文件信息数组
       // 下载模型上传配置（文件上传类配置）
       
    // 本地上传文件驱动配置
    'UPLOAD_LOCAL_CONFIG' => array(),
    'UPLOAD_BCS_CONFIG' => array(
        'AccessKey' => '',
        'SecretKey' => '',
        'bucket' => '',
        'rename' => false
    ),
    
    // RBAC权限配置
    'USER_AUTH_ON' => true, // USER_AUTH_ON 是否需要认证
    'USER_AUTH_TYPE' => 1, // USER_AUTH_TYPE 认证类型
    'USER_AUTH_KEY' => 'authId', // USER_AUTH_KEY 认证识别号
    'REQUIRE_AUTH_MODULE' => '', // REQUIRE_AUTH_MODULE 需要认证模块
    'NOT_AUTH_MODULE' => 'Public', // NOT_AUTH_MODULE 无需认证模块
    'NOT_AUTH_ACTION' => '',
    'USER_AUTH_GATEWAY' => '/Admin/Login', // USER_AUTH_GATEWAY 认证网关
    'USER_AUTH_MODEL' => 'user', // 用户表
                                 // RBAC_DB_DSN 数据库连接DSN
    'RBAC_ROLE_TABLE' => 'my_role', // RBAC_ROLE_TABLE 角色表名称
    'RBAC_USER_TABLE' => 'my_role_user', // RBAC_USER_TABLE 用户表名称
    'RBAC_ACCESS_TABLE' => 'my_access', // RBAC_ACCESS_TABLE 权限表名称
    'RBAC_NODE_TABLE' => 'my_node', // RBAC_NODE_TABLE 节点表名称
    'ADMIN_AUTH_KEY' => 'administrator'
); // 超级管理员
$config2 = require 'coupon.php';
$config = array_merge($config, $config2);

// 加载自定义参数设置
if (file_exists(GLOBAL_CONFIG)) {
    $config1 = require GLOBAL_CONFIG;
    return array_merge($config, $config1);
} else {
    return $config;
}
?> 