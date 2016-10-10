<?php
$hostname = php_uname('n');
if (strtolower($hostname) != 'pc-20160819tbmr') {
    define('DEPLOY_ENV', 'pro'); // 线上模式
    define('HIDDEN_ENV_FLAG', false); // 环境标示
} else {
    //echo '<div id="think_page_trace_open" style="height:50px;float:right;text-align: right;overflow:hidden;position:fixed;bottom:0;left:0;color:#000;line-height:30px;cursor:pointer;"><div style="background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px">local</div></div>';
    define('DEPLOY_ENV', 'local'); // 本地模式
    define('HIDDEN_ENV_FLAG', true); // 环境标示显示
}

define ('APP_DEBUG', true);
define ('DIR_SECURE_FILENAME', false);
//define('BIND_MODULE','Shop');
// define('BUILD_CONTROLLER_LIST','Index');
define ('APP_PATH', './Application/');
define ('GLOBAL_CONFIG', APP_PATH . 'Common/conf/setting.php');
require './ThinkPHP/ThinkPHP.php';
?>