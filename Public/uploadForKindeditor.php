<?php

//require '../../../ThinkPHP/Extend/Vendor/Lxlib/loader.php';
require '../Application/Shop/Controller/ImagesController.class.php';
$originalConf = require  '../Application/Common/Conf/uploadconfig.php';

$scope = isset($_GET['scope']) ? $_GET['scope'] : '';
$callback = isset($_GET['back']) ? $_GET['back']:(isset($_GET['callback']) ? $_GET['callback'] : '');

if (!preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\”])*$/", $callback)) {
    $data = array('error' => 1, 'message' => "invalid callback");
    response($data);
}
if (!preg_match("/\w+/", $scope)) {
    $data = array('error' => 1, 'message' => "invalid scope");
    response($data);
}

$config = $originalConf[$scope];
if (!$config) {
    $data = array('error' => 1, 'message' => "没有在配置文件中找到对应的scope");
    response($data);
}

$image = new ImagesController();
$fileInfo = $image->upload($config);

if(false !== $fileInfo){
    if(is_array($fileInfo)){
        if(count($fileInfo)==1){
            $data = array('error' => 0, 'url'=>$fileInfo[0]);
        }else{
            dump($fileInfo);
            $arr=array();
            foreach($fileInfo as $k=>$file){
                array_push($arr,$file);
            } dump($arr);exit;
            $data = array('error' => 0,'url'=> $arr);
        }
    }
}else{
    $data = array('error' => 1,'url'=> '');
  //  $data = array('code' => 400);
}
response($data);

function response($data) {
    $callback = isset($_GET['back']) ? $_GET['back']:(isset($_GET['callback']) ? $_GET['callback'] : '');
    $url = $callback . "?callback=$_GET[callback]&s=" . json_encode($data);
    header("Location: " . $url);
}

?>
