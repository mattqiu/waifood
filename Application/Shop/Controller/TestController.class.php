<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;

class TestController extends BaseController {
    function test(){
        
        $ip=file_get_contents("http://api.map.baidu.com/highacciploc/v1?qcip=&qterm=pc&ak=7jEcZBZR91Am7YnnGtaEEu0GoxZlIcOj");
        $ip = json_decode($ip,JSON_UNESCAPED_UNICODE);
        dump($ip);
        $this->show("test page");
    }
    function test1(){
        C("HTML_CACHE_ON",false);
        $ip=file_get_contents("http://api.map.baidu.com/location/ip?ak=7jEcZBZR91Am7YnnGtaEEu0GoxZlIcOj&coor=bd09ll&r=". rand());
        $ip = json_decode($ip,JSON_UNESCAPED_UNICODE);
        dump(rand());
        dump($ip);
        dump($ip["content"]["address_detail"]["city"]);
        dump(strstr($ip["content"]["address_detail"]["city"], "成都"));
        dump(strstr($ip["content"]["address_detail"]["city"], "成都")?"1":"0");
        $this->show("test page");
    }
    
    
}
?>