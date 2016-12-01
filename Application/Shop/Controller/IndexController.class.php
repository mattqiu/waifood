<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;

use Common\Model\AddressModel;
use Common\Model\ContentModel;
use Common\Model\DateModel;
use Common\Model\UserModel;

class IndexController extends BaseController
{

    public function index()
    {
        $promotion  =  ContentModel::getGroupContent(ContentModel::PROMOTION);
        $newArrival  =  ContentModel::getGroupContent(ContentModel::NEW_ARRIVAL);
        $recommend  =  ContentModel::getGroupContent(ContentModel::RECOMMEND);
        $this->assign('catShow',true);
        $this->assign('recommend',$recommend);
        $this->assign('newArrival',$newArrival);
        $this->assign('promotion',$promotion);
        $title = C('config.WEB_SITE_TITLE');
        $keywords = C('config.WEB_SITE_KEYWORD');
        $description = C('config.WEB_SITE_DESCRIPTION');
        $this->assign('title', $title);
        $this->assign('keywords', $keywords);
        $this->assign('description', $description);
        $this->display();
    }

    public function cart(){
        $this->assign ( 'title', 'My shopping cart' );
        $this->display();
    }

    public function cashier(){
        $user = UserModel::getUser();
        if(empty($user)){
            //没登录的先登录
            session('backurl','/index/cashier.html');
            $this->redirect('/login/index');
        }
        if($dateData = DateModel::getFutureDay(27)){
            $this->assign ( 'beyond',DateModel::DELIVERTIME_BEYOND);
            $this->assign ( 'dateData',$dateData);
        }

        $times = str2arr(lbl('delivertime'),"\r\n");//配送时段
        $address =AddressModel:: getUserAddress($user['id']);
        $this->assign ( 'title', 'Cashier' );
        $this->assign ( 'times', $times );
        $this->assign ( 'address', $address );
        $this->display();
    }

    /**
     * 咨询中心
     */
    function consult (){
        $contentid = I('id');
        if(regex($contentid,'number')){

        }
        $this->display();
    }

    /**
     * 群发推荐朋友
     * 
     * @param unknown $emailist            
     */
    public function sendmail($emaillist = '')
    {
        if (isN($emaillist)) {
            $this->error('the email cannot be empty.');
        }
        $emaillist = str_replace("，", ",", $emaillist);
        $arr = str2arr($emaillist);
        $arr = arr2clr($arr);
        $newarr = array();
        foreach ($arr as $k => $v) {
            if (is_email($v)) {
                $newarr[] = $v;
            }
        }
        if (count($newarr) == 0) {
            $this->error('the email is illegal.');
        }
        $content = lbl('tpl_massemail');
        if ($content != "") {
            $url = get_furl(get_userid());
            $url = "<a href='" . $url . "' target='_blank'>" . $url . "</a>";
            $content = str_replace('{$shopurl}', $url, $content);
            foreach ($newarr as $k => $v) {
                send_mail($v, 'hi,recommend you a good site to shop.', $content);
            }
            $this->success('Succeed.');
        } else {
            $this->error('Sorry, system error.');
        }
    }

    //生成验证码
    public function verify(){
        verify();
    }

}
?>