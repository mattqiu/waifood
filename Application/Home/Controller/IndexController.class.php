<?php

namespace Home\Controller;

use Admin\Model\BannerModel;
use Common\Model\ContentModel;
use Common\Model\OrderModel;
use Common\Model\UserModel;
use Home\Model\WeixinModel;
use Think\Log;

class IndexController extends BaseController {
	public function index() {
	    $where=array();
		$where['status']=1;
		$where['pid']=2; 
		$channel=M('Channel')->where($where)->order('sort asc')->select();
        $promotion  =  ContentModel::getGroupContent(ContentModel::PROMOTION);
        $newArrival  =  ContentModel::getGroupContent(ContentModel::NEW_ARRIVAL);
        $recommend  =  ContentModel::getGroupContent(ContentModel::RECOMMEND);
        $banner = BannerModel::getBannerByType(BannerModel::WX_BANNER);// 幻灯片
        $this->assign('banner',$banner);
		$this->assign('recommend',$recommend);
		$this->assign('newArrival',$newArrival);
		$this->assign('promotion',$promotion);
		$this->assign('list',$channel);
		$this->display ();
	} 

	public function img(){
	    $font=(LIB_PATH.'\Think\Verify\ttfs\1.ttf');
	    $pic='1.jpg';
	    $ctrl = new \Think\Image ( 1, $pic );
	    $img = $ctrl->text ( '001',$font,250,'#ffffff',5)->save ( '2.jpg' );
	    we('ok');
	}

    /**订单微信支付
     * @return bool
     */
    public function payWeixin(){
        if(!FROM_WEIXIN){
            $this->error('Please open it in wechat');
            return false;
        }
        $orderId =  I('orderno');
        $isWeiPay = WeixinModel::_weiXinVersion(5);
        if($isWeiPay){
            $userid = get_userid();;
            if($userid<1){
                GLog('jsApi pay','用户没有登录'.$userid);
                $this->error('Please sign in.');
                return false;
            }
            $order = OrderModel::getOrderByOrderno($orderId);
            if(empty($order)){
                GLog('jsApi pay','订单不存在');
                $this->error('Order does not exist');
                return false;
            }
            $js = WeixinModel::getOrderSelfWxPay($order,$userid);
            $this->assign("return_url","/member/order.html");
            if($js === true){
                $this->assign("isPayed",true);
                $this->display('payWeixin');
            }else{
                $this->assign('jsApiParameters',$js);
                $this->display('payWeixin');
            }
        }else{
            GLog("payweixin","微信版本不支持微信支付:".json_encode($_SERVER),Log::ERR);
            $this->error('Your current wechat does not support wechat pay
Your order has been paid successfully');
            return false;
        }
    }

}
?>