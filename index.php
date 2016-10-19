<?php
$hostname = php_uname('n');
if (strtolower($hostname) != 'pc-20160819tbmr') {
    define('APP_DEBUG', false);
    define('DEPLOY_ENV', 'pro'); // 线上模式
    define('HIDDEN_ENV_FLAG', false); // 环境标示
} else {
    define ('APP_DEBUG', true);
    define('DEPLOY_ENV', 'local'); // 本地模式
    define('HIDDEN_ENV_FLAG', true); // 环境标示显示
}
require './Application/Common/Common/share.php';
define ('DIR_SECURE_FILENAME', false);
define ('APP_PATH', './Application/');
define ('GLOBAL_CONFIG', APP_PATH. 'Common/Conf/setting.php');
require './ThinkPHP/ThinkPHP.php';

?>