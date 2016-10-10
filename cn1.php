<?php
define ( 'APP_DEBUG', true);
define ( 'DIR_SECURE_FILENAME', false );
define('BIND_MODULE','cn');

 define('BUILD_CONTROLLER_LIST','Index,User,Content');
define ( 'APP_PATH', './Application/' );
define ( 'GLOBAL_CONFIG', APP_PATH . 'Common/conf/setting.php' );
require './ThinkPHP/ThinkPHP.php';
?>