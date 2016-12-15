<?php
header('Content-type: text/html; charset=UTF-8');
require './kindeditor/php/JSON.php';

require '../Application/Shop/Controller/ImagesController.class.php';
$originalConf = require  '../Application/Common/Conf/uploadconfig.php';

$callback = isset($_GET['back']) ? $_GET['back']:(isset($_GET['callback']) ? $_GET['callback'] : '');

if (!preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\”])*$/", $callback)) {
    $data = array('error' => 1, 'message' => "invalid callback");
    response($data);
}

$scope = isset($_GET['scope']) ? $_GET['scope'] : '';
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
            $arr=array();
            foreach($fileInfo as $k=>$file){
                array_push($arr,$file);
            }
            $data =array('error' => 0,'url'=> $arr);
        }
    }
}else{
    $data = array('error' => 1,'url'=> '');
}
response($data);

function response($data) {
    $json = new Services_JSON();
    $callback = isset($_GET['back']) ? $_GET['back']:(isset($_GET['callback']) ? $_GET['callback'] : '');
    $url = $callback . "?callback=$_GET[callback]&s=" . $json->encode($data);
    header("Location: " . $url);
}

?>
