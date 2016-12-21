<?php
namespace Home\Controller;

use Common\Model\UserModel;
use Common\Model\WxJsSdkModel;
use Think\Controller;

class BaseController extends Controller
{

    public function _initialize()
    {
        //特殊节日优惠标示，结束后删除
        if(isset( $_REQUEST['hd']) &&  $_REQUEST['hd']){
            session('hd', $_REQUEST['hd']);
            cookie('hd', $_REQUEST['hd']);
            // 检查用户是否登录 ----活动进入必须保证登录
            $user = UserModel::getUser();
            if(empty($user)){
                $this->redirect ( 'Login/index' );
            }
        }
        // 载入模板
        if (C('config.WEB_SITE_TEMPLATE')) {
            C('DEFAULT_THEME', C('config.WEB_SITE_TEMPLATE'));
        }
//        if(UserModel::isAttention()){
//            $this->assign('subscribe', true); //关注
//        }else{
//            $this->assign('subscribe', false); //未关注
//        }
        //$this->checkLogin();
        $this->setFatherId();
        $this->assign('WxJsSdk',WxJsSdkModel::getWxJsSdk());
        $this->assign('shoptitle', 'Waifood');
    }
    
    /**
     * 记录推广人
     */
    public function setFatherId()
    {
        $fid = I(C('COUPON.FID'), 0);
        $cid = Cookie('fid');
        if ($cid != $fid) {
            Cookie('fid', $fid);
        }
    }


}
?>