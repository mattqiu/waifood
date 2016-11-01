<?php
// 购物车类
namespace Home\Controller;

use Common\Model\UserModel;
use Think\Controller;

class WeixinController extends Controller {

    protected $conf='';
    private  $returnUrl="http://www.waifood.com/home/"; //回调地址

    public function _initialize() {
        $this->conf=C('config');
    }

    /**
     * 微信登录回调方法
     * @throws Exception
     */
    public function weixin_callback(){
        $code = I('get.code');
        if (empty($code)) {
            GLog('weixin:login:code','code is empty');
            return false;
        }
        $state = I('get.state');
        $verifyState = session('verify_state') ? session('verify_state'): I('get.rand');
        if (!$state or $state != $verifyState) {
            GLog('weixin:login:state','Before and after the state');
            return false;
        }
        $conf =  $this->conf;
        $appid = $conf['WECHAT_APPID'];
        $appsecret = $conf['WECHAT_APPSECRET'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code" ;
        import('ORG.Util.HttpRequest');
        $data = json_decode(\HttpRequest::http_get($url), true);
        trace("user ".var_export($data ,true));
        GLog('weixin:login:token',json_encode($data));
        if (!isset($data['access_token'])) {
            GLog('weixin:login:token','获取access_token错误返回');
            return false;
        }
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $data['access_token'] . '&openid=' . $data['openid'];
        $user = json_decode(\HttpRequest::http_get($url), true);
        S('openid_' . openid(),$user);//缓存微信用户信息
        trace("user ".var_export($user ,true));
        openid($data['openid']);//缓存openid
        GLog('weixin:login:user',$user);
        if(UserModel::getUserByOpenid($data['openid'])){
            if( UserModel::loginWechat($data['openid'])){
                redirect('/');
            }
        }else{
            if( UserModel::createWechatUser($data['openid'])){
                redirect('/');
            }
        }

//        // 判断是否绑定，提示绑定
//        if (! is_bind($data['openid'])) {
//            redirect(U('Login/bind'));
//        }else{
//
//        }
    }

}
?>