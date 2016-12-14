<?php
namespace Home\Controller;

use Common\Model\UserModel;
use Common\Model\WxJsSdkModel;
use Think\Controller;

class BaseController extends Controller
{

    public function _initialize()
    {
        // 载入模板
        if (C('config.WEB_SITE_TEMPLATE')) {
            C('DEFAULT_THEME', C('config.WEB_SITE_TEMPLATE'));
        }
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