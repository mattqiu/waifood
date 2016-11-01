<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	//const APPID = 'wx621e3eed6c5d3df7';
    public static $APPID = '';
	//受理商ID，身份标识
	//const MCHID = '1224825602';
    public static $MCHID = '';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	//const KEY = '3cfoodweixinpay101lingdian147258';
    public static $KEY = '';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	//const APPSECRET = '57c7f9f034b1590d5e953febfbdf7bbd';
    public static $APPSECRET = '';
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = 'http://www.yankeer.com/common/pay/jsapicall';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = '/data/www/yankeer/code/Public/cacert/apiclient_cert.pem';
	const SSLKEY_PATH = '/data/www/yankeer/code/Public/cacert/apiclient_key.pem';
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = 'http://www.yankeer.com/common/pay/weixinback';
  //  const NOTIFY_TEST_URL = 'http://test.keloop.com/common/pay/weixinback';//测试的异步返回地址
	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
   // const URL = 'http://www.3cfood.com/common/pay/';

   // const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx621e3eed6c5d3df7&secret=57c7f9f034b1590d5e953febfbdf7bbd&code=%s&grant_type=authorization_code';
   // const USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s';

    public function __construct($account){
        self::$APPID = $account['appid'];
        self::$MCHID = $account['mchid'];
        self::$KEY = $account['key'];
        self::$APPSECRET = $account['appsecret'];
    }

}

