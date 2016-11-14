<?php
defined('THINK_PATH') or exit();
return array(
/* 支付设置 */
'payment' => array(
        'tenpay' => array(
            
            // 加密key，开通财付通账户后给予
            'key' => '',
            
            // 合作者ID，财付通有该配置，开通财付通账户后给予
            'partner' => ''
        ),
        'alipay' => array(
            
            // 收款账号邮箱
            'email' => '',
            
            // 加密key，开通支付宝账户后给予
            'key' => '',
            
            // 合作者ID，支付宝有该配置，开通易宝账户后给予
            'partner' => ''
        ),
        'palpay' => array(
            'business' => ''
        ),
        'yeepay' => array(
            'key' => '',
            'partner' => ''
        ),
        'kuaiqian' => array(
            'key' => '',
            'partner' => ''
        ),
        'unionpay' => array(
            'key' => '',
            'partner' => ''
        )
    ),
    /*微信支付*/
		'WXPAY' => array(
        
        // =======【基本信息设置】=====================================
        // 微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
        'APPID' => 'wx68999e0e01c2020f',
        
        // 受理商ID，身份标识
        'MCHID' => '10018698',
        
        // 商户支付密钥Key。审核通过后，在微信发送的邮件中查看
        'KEY' => '33cf13c0686401b395ee4aa263836291',
        
        // JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
        'APPSECRET' => '872bff48b06302e3062808b487830634',
        
        // =======【JSAPI路径设置】===================================
        // 获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
        'JS_API_CALL_URL' =>  C('DOMAIN').'Home/pay/call?showwxpaytitle=1',
        
        // =======【证书路径设置】=====================================
        // 证书路径,注意应该填写绝对路径
        'SSLCERT_PATH' => '/alidata/www/weimicn_com/test/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_cert.pem',
        'SSLKEY_PATH' => '/alidata/www/weimicn_com/test/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_key.pem',
        
        // =======【异步通知url设置】===================================
        // 异步通知url，商户根据实际开发过程设定
        'NOTIFY_URL' =>  C('DOMAIN').'Home/pay/payNotify',
        
        // =======【curl超时设置】===================================
        // 本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
        'CURL_TIMEOUT' => 30,
        'CURLOP_TIMEOUT' => 30
    )
);
?>