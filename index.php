<?php
$hostname = php_uname('n');
if (strtolower($hostname) == 'pc-20160819tbmr') {
    define('DEPLOY_ENV', 'local'); // 本地模式
    define('HIDDEN_ENV_FLAG', true); // 环境标示显示
}elseif(strtolower($hostname) == 'izuf62o440hd7lymnaldnwz'){
    define('DEPLOY_ENV', 'test'); // 测试环境
    define('HIDDEN_ENV_FLAG', true); // 环境标示显示
} else {
    define('DEPLOY_ENV', 'pro'); // 线上模式
    define('HIDDEN_ENV_FLAG', false); // 环境标示
}
require './Application/Common/Common/share.php';

define ('APP_DEBUG', true);
define ('DIR_SECURE_FILENAME', false);
//define('NO_CACHE_RUNTIME', true);
define ('FROM_WEIXIN', isFromWeixin());
define ('APP_PATH', './Application/');
define ('GLOBAL_CONFIG', APP_PATH. 'Common/Conf/setting.php');
require './ThinkPHP/ThinkPHP.php';

?>