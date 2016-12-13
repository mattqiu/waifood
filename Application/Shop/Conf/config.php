<?php

defined('THINK_PATH') or exit();
return array( 
'BIND_MODULE' =>'Shop',
    'TMPL_ACTION_ERROR'     =>  'Other:dispatch_jump', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>   'Other:dispatch_jump',
		
    /* 支付设置 */
    'payment' => array(
       
        'alipay' => array(
            // 收款账号邮箱
            'email' => C('config.ALIPAY_EMAIL'),
            // 加密key，开通支付宝账户后给予
            'key' => C('config.ALIPAY_KEY'),
            // 合作者ID，支付宝有该配置，开通易宝账户后给予
            'partner' => C('config.ALIPAY_PARTNERID')
        ),
        'palpay' => array(
            'business' =>  C('config.PAYPAL_BUSINESS'),
            'lang' =>  'en_GB',//zh_HK 繁体中文 en_GB 英文
        )
    )
);
