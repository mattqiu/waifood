<?php

class HttpRequest{

    public static function postUrl($url, $params = array(), $timeout = 30){
         Think\Log::record("httprequest","post url:".$url,\Think\Log::INFO);
          //编码特殊字符
        $p = http_build_query($params);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $p);

        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 运行cURL，请求网页
        $data = curl_exec($curl);
        if($data === false){
            Think\Log::record("httprequest","post error:".curl_error($curl),\Think\Log::ERR);
            return false;
        }else{
            Think\Log::record("httprequest","post result:".print_r($data,true),\Think\Log::INFO);
            return $data;
        }
    }

    public static function getUrl($url, $param = array()){
        $url = self::buildUrl($url, $param);
        return self::get($url);
    }

    public static function get($url, $timeout = 30){
        Think\Log::record("httprequest","get url:".$url,\Think\Log::INFO);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resposne = curl_exec($ch);
        return $resposne;
    }

    private static function buildUrl($url, $param){
        $url = rtrim(trim($url),"?");
        $url = $url."?";
        $query = "";
        if(!empty($param)){
            $query = http_build_query($param);
        }
        return $url.$query;
    }

}

?>
