<?php
var_dump(123123);exit;
header('content-type:application:jsonp;charset=utf8');
$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
$allow_origin = array(
    'http://www.3cfood.cc',
    'http://www.3cfood.com',
);
if(in_array($origin, $allow_origin)){
    header('Access-Control-Allow-Origin:'.$origin);
    header('Access-Control-Allow-Methods:POST');
    header('Access-Control-Allow-Headers:x-requested-with,content-type');
}

require '../../../ThinkPHP/Extend/Vendor/Lxlib/loader.php';
require '../../../ThinkPHP/Extend/Vendor/Lxlib/upload/src/LxUpload.class.php';
$originalConf = require '../lingdianit/Conf/uploadconfig.php';
$scope = isset($_GET['scope']) ? $_GET['scope'] : '';
$callback = isset($_GET['back']) ? $_GET['back']:(isset($_GET['callback']) ? $_GET['callback'] : '');
$from = isset($_GET['from']) ? $_GET['from'] : '';
$stream = isset($_REQUEST['stream']) ? $_REQUEST['stream'] : '';
if (!preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\”])*$/", $callback)) {
    $data = array('error' => 1, 'message' => "invalid callback");
    response($data);
}
if (!preg_match("/\w+/", $scope)) {
    $data = array('error' => 1, 'message' => "invalid scope");
    response($data);
}
if (!array_key_exists($from, $originalConf)) {
    $data = array('error' => 1, 'message' => "invalid from");
    response($data);
}

$config = $originalConf[$from][$scope];
if ($config) {
    $bucket = "image-share";
    $config['0xiao_savepath'] = $config['0xiao_savepath'] . "image";    //追加动态目录,为了符合 kindeditor的定义，这里建立个 父母了 image
} else {
    $data = array('error' => 1, 'message' => "没有在配置文件中找到对应的scope");
    response($data);
}

$hostname = php_uname('n');
$ossConfig  = array();
if(stripos($hostname,"pro") !== false) {
    $ossConfig = array(
       "access_id"=>"OwT8CGyYXww6flXT",
       "access_key"=>"m6e07lbffFfiATeiYxoALCzonFtrAT",
       "host"=>"oss-cn-hangzhou-internal.aliyuncs.com",  //内网上传
       "bucket"=>$bucket,
    );
} else {
     $ossConfig = array(
       "access_id"=>"OwT8CGyYXww6flXT",
       "access_key"=>"m6e07lbffFfiATeiYxoALCzonFtrAT",
       "host"=>"oss-cn-hangzhou.aliyuncs.com",
       "bucket"=>$bucket,
    );
}

//$stream
$upload = new LxUpload($ossConfig);
$res = $upload->upload($config['save_project'], $config['0xiao_savepath'], array(
    'fixedWidth' => $config['0xiao_fixed_width'], // 固定宽度限制
    'fixedHeight' => $config['0xiao_fixed_height'], // 固定高度限制
    'maxSize' => $config['maxSize'],  //文件大小限制,字节数 5M = 5*1024*1024 5kb = 5*1024
    'allowExts' => $config['allowExts'] // 允许的文件后缀
),$stream);

if (!$res) {
    $data = array('error' => 1, 'message' => LxUpload::errorInfo());
    response($data);
} else {
    if($stream){
            ajaxSuccess($res);
    }else{
        if(count($res)==1){
            $data = array('error' => 0, 'url'=>$res[0]['filepath']);
        }else{
            $arr=array();
            foreach($res as $k=>$file){
                array_push($arr,$file['filepath']);
            }
            $data = array('error' => 0,'url'=> $arr);
        }
    }
    response($data);
}

function ajaxSuccess($data){
    $result['code'] = 100;
    $result['message'] = 'success';
    $result['data'] = $data;
    header('Content-Type:application/json; charset=utf-8');
    if(preg_match('/^jQuery/',$_GET['callback'])){
        exit($_GET['callback'].'('.json_encode($result).')');
    }else {
        exit(json_encode($result));
    }

}

function response($data) {
    $callback = isset($_GET['back']) ? $_GET['back']:(isset($_GET['callback']) ? $_GET['callback'] : '');
    $url = $callback . "?callback=$_GET[callback]&s=" . json_encode($data);
    header("Location: " . $url);
}



?>
