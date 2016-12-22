<?php
namespace Common\Model;
use Home\Model\WeixinModel;
use Think\Log;
use Think\Model;
use Org\Util\WxJsSdk;
class WxJsSdkModel extends Model {
    const WX_TOKEN_KEY = 'WX_TOKEN::WAIF00D';
    public static function getWxJsSdk(){
       // if(FROM_WEIXIN){
            $weixinShare = new WxJsSdk();
            return $weixinShare->getSignPacket();
      //  }
        return false;
    }

    /*
      * 获取token
      */
    public static function getWxToken(){
        if(!$token = S(self::WX_TOKEN_KEY)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?"
                . "grant_type=client_credential&appid=%s&secret=%s";
            $url = sprintf($url, C('WECHAT_APPID'), C('WECHAT_APPSECRET'));
            $res = WeixinModel::http_get($url);
            if(!empty($res)){
                $tt = json_decode($res, true);
                if(isset($tt['access_token'])){
                    $token = $tt['access_token'];
                    S(self::WX_TOKEN_KEY, $token, 7000);
                    return $token;
                }else{
                    GLog("setFoodImage","获取access_token失败",Log::ERR);
                    return false;
                }
            }else{
                GLog("setFoodImage","请求$url 失败",Log::ERR);
                return false;
            }
        }
        return $token;
    }

    public static function clearWxToken(){
        S(self::WX_TOKEN_KEY,null);
    }
}

?>