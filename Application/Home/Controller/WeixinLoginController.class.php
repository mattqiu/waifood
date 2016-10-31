<?php
/**
 * Copyright (C) 2012-2099 成都零点信息技术有限公司.
 * All rights reserved.
 */
//import("ORG.0xiao.Image");

namespace Home\Controller;

use Think\Controller;

class WeixinLoginController extends AuthController{

    protected $return_url = "http://www.waifood.com/";  //默认跳转地址
    protected $CONF ='';
    const LOGIN_RETURN_URL = "LOGIN_RETURN_URL";

//    // 公共号
//    const SCFOOD_WEIXIN_APP_ID = "wx621e3eed6c5d3df7";
//    const SCFOOD_WEIXIN_SEC = "57c7f9f034b1590d5e953febfbdf7bbd";


    public function __construct(){
        $conf = C('config');
        $this->CONF = $this->CONF?$this->CONF:$conf;
    }

    public function index(){
        $code ='021mO3tP1U2W5615yfpP1fF3tP1mO3tE&state';
        $weChat = get_wechat_obj();

//
//        if ($code == '') {
//            $url = $weChat->getOauthRedirect(get_current_url());
//            redirect($url);
//        } else {
            $accessToken = $weChat->getOauthAccessToken();
     //   }

        dump($weChat);
        dump($accessToken);
        exit;
        $conf = $this->CONF;
        $state = mt_rand(100000,999999);
        session('verify_state', $state);
        $type = I("t","waifood");
        $appID =$conf['WECHAT_APPID'];
        $callback = "http://www.waifood.com/home/weixinLogin/wx_callback";
        $url =  $conf['WECHAT_AUTH2_URL'] ."appid=$appID&redirect_uri=".urlencode($callback)
            ."&response_type=code&scope=snsapi_userinfo&state=$type#wechat_redirect";
        header("Location:$url");
        exit();
    }

    //微信里面 -三餐微信用户回调
    public function wx_callback(){
        $code = I('get.code');
        if (empty($code)) {
            GLog('/////////////////user code','获取用户授权失败');
        }
        $conf = $this->CONF;
        $token = $conf['ACCESS_TOKEN_URL'];//获取access token 的url
        import('ORG.Util.HttpRequest');
        $HttpRequest  = new \HttpRequest();
        $data = json_decode($HttpRequest::get(sprintf($token, $code)), true);
        GLog('///////////======',json_encode($data));
        if (!isset($data['access_token'])) {
            GLog('/////////////',"获取access_token失败");
            return false;
        }
        $user = $HttpRequest::get(sprintf(
            $conf['USER_INFO_URL'],
            $data['access_token'],
            $data['openid']
        ));
        GLog('///////////==========user',json_encode($user));
        exit;
        $this->wxccallback($user);
    }

    private function wxccallback($user){
        GLog("cc_weixincallback","user info:".json_encode($user));
        if (isset($user['errcode'])) {
            GLog("cc_weixinlogin","获取用户信息失败",Log::ERR);
            $this->error("获取用户信息失败",'http://www.3cfood.cc');
            exit();
        }
        cookie('from_type','');
        $url='http://www.3cfood.cc/Coupon/WxCallBack/weiCallback?user_info='.urlencode(base64_encode(json_encode($user) ));
        header('Location:' . $url);
    }

    private function _weiCallback($user){
        GLog("weixincallback","user info:".json_encode($user));
        if (isset($user['errcode'])) {
            GLog("weixinlogin","获取用户信息失败",Log::ERR);
            $this->error("获取用户信息失败",$this->return_url);
            exit();
        }
        if(cookie('list_trush')){
            $this->return_url = cookie('list_trush');
        }
        $openid = isset($user['unionid']) ? $user['unionid'] : $user['openid'];
        $existUser = UserService::isExistThridUser($openid, UserModel::WEIXIN_LOGIN);
        if ($existUser) {
            $con['user_id'] = $existUser['user_id'];
            $data['head_path'] = $user['headimgurl'];
            $data['weixin_push_open_id'] = $user['openid'];  //weixin push openid
            D("User")->where($con)->save($data);
            SessionService::thirdUserLogin($openid, UserModel::WEIXIN_LOGIN);
            cookie('weixinOpenId', $openid, 86400 * 365);
            header('Location:' . $this->return_url);
        } else {
            $openId     = $openid;
            $mediaId    = UserModel::WEIXIN_LOGIN;
            $username   = $user['nickname'];
            $gender     = $user['sex'] == 1 ? 1 : 2;
            $headpath   = $user['headimgurl'];
            $update = array();
            if(empty($openId)){
                GLog("weixinlogin","未能找到第三方授权帐号",Log::ERR);
                $this->error("未能找到第三方授权帐号",$this->return_url);
                exit();
            }else{
                $update['open_id'] = $openId;
                $update['media_id'] = $mediaId;
            }
            $update['weixin_push_open_id'] = $user['openid'];
            $update['nick_name'] = $username;
            if(!empty($headpath)){
                $update['head_path'] = $headpath;
            }
            if(!empty($gender)){
                $update['gender'] = $gender;
            }
            $update["state"] = 0;
            $update['reg_time'] = date('Y-m-d H:i:s');
            $flag = D("User")->add($update);
            if($flag!==false){
                SessionService::thirdUserLogin($openId, $mediaId);
                cookie('weixinOpenId', $openId, 86400 * 365);
                header('Location:' . $this->return_url);
            }else{
                GLog("weixinlogin","微信登录失败",Log::ERR);
                $this->error("微信登录失败",$this->return_url);
            }
        }
    }

}

?>
