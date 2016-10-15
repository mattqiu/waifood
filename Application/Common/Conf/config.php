<?php
$staticConfig = include 'static.php';
$selfConfig = include DEPLOY_ENV."/config.php";
return array_merge($staticConfig,$selfConfig);
?>