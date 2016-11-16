<?php
namespace Common\Model;
use Think\Model;
use Org\Util\WxJsSdk;
class WxJsSdkModel extends Model {

    public static function getWxJsSdk(){
       // if(FROM_WEIXIN){
            $weixinShare = new WxJsSdk();
            return $weixinShare->getSignPacket();
      //  }
        return false;
    }
}

?>