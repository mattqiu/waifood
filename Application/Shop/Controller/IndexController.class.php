<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;

use Admin\Model\BannerModel;
use Common\Model\AddressModel;
use Common\Model\CodeModel;
use Common\Model\ConfigModel;
use Common\Model\ContentModel;
use Common\Model\DateModel;
use Common\Model\OrderModel;
use Common\Model\UserModel;

class IndexController extends BaseController
{

    public function index()
    {
        $orderstr = 'sort1 desc';
        $promotion  =  ContentModel::getGroupContent(ContentModel::PROMOTION,$orderstr);
        $newArrival  =  ContentModel::getGroupContent(ContentModel::NEW_ARRIVAL,$orderstr);
        $recommend  =  ContentModel::getGroupContent(ContentModel::RECOMMEND,$orderstr);
        $banner = BannerModel::getBannerByType(BannerModel::PC_BANNER);// 幻灯片
        $this->assign('banner',$banner);
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
        $startimt = '';
        $stardate = '';
        $nowtime = intval(date('H'));
        $nowdate = date('Y-m-d');
        if($dateData = DateModel::getFutureDay(27)){
            foreach($dateData as $val){
                if(!isset($val['isholiday']) && !$startimt){
                    if($nowdate == $val['time'] && $nowtime < intval(DateModel::DELIVERTIME_BYDAYTIME)){
                        $startimt = 'Today('.$val['time'].')';
                        $stardate = $val['time'];
                    }else if($nowdate != $val['time'] && $nowtime){
                        $day = intval(date('d',strtotime($val['time']) - strtotime($nowdate)));
                        if($day ==2){
                            $startimt = 'Tomorrow('.$val['time'].')';
                        }else{
                            $startimt = $val['time'];
                        }
                        $stardate = $val['time'];
                    }
                }
            }
            //计算重庆的到货时间
            $cqEndDate = date('Y-m-d',strtotime("{$stardate} +1 days"));
            $day = intval(date('d',strtotime($cqEndDate) - strtotime($stardate)));
            if($day ==2){
                $cqEndDate = 'Tomorrow('.$cqEndDate.')';
            }

            $this->assign ( 'cqEndDate',date('Y-m-d',strtotime("{$val['time']} +1 days")));
            $this->assign ( 'cqEndDate',$cqEndDate);
            $this->assign ( 'startimt',$startimt);
            $this->assign ( 'dateData',$dateData);
            $this->assign ( 'beyond',DateModel::DELIVERTIME_BEYOND);
            $this->assign ( 'hours', date('H'));
            $this->assign ( 'min', date('i'));
        }
        $this->assign ( 'deliverinfo',lbl('deliverinfo') );
        $times = str2arr(lbl('delivertime'),"\r\n");//配送时段
        $address =AddressModel:: getUserAddress($user['id']);
        $this->assign ( 'title', 'Cashier' );
        $this->assign ( 'times', $times );
        $this->assign ( 'address', $address );
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

    public function wishlist(){
        $user = UserModel::getUser();
        if(empty($user) || !regex($user['id'],'number')){
            session('backurl','/index/wishlist.html');
            $this->redirect('/login/index');
        }
        $this->display();
    }


    public function subWishList(){
        $data = empty ( $data ) ? $_POST : $data;
        $user = UserModel::getUser();
        $data['ext1'] = $user['username'];
        $data['ext2'] = $user['telephone'];
        $data['ext3'] = $user['email'];
//        if(isN($data['ext1'])){
//            apiReturn(CodeModel::ERROR,'Sorry, your name cannot be empty!');
//        }
//        if(isN($data['ext2'])){
//            apiReturn(CodeModel::ERROR,'Sorry, phone number cannot be empty!');
//        }
//        if(!is_email($data['ext3'])){
//            apiReturn(CodeModel::ERROR,'Sorry, email is illegal!');
//        }
        if(isN($data['ext5'])){
            apiReturn(CodeModel::ERROR,'Please input the subject.');
        }
        if(isN($data['ext6'])){
            apiReturn(CodeModel::ERROR,'Please input some words.');
        }
        $data ['addip'] = get_client_ip ();
        if (false !== D ( "form" )->add ( $data )) {
            //管理员邮件：
            //send_mail();
            $to=ConfigModel::getConfig('WEB_SITE_COPYRIGHT');//获取管理员邮箱C('config.WEB_SITE_COPYRIGHT');
            $subject='[waifood]new wishlist '.get_username(get_userid());
            $html='';
            $html.= "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"> \n";
            $html.= "    <tr class=\"row0\">\n";
            $html.= "      <td width=\"100\" >反馈：</td>\n";
            $html.= "      <td>Wishlist </td>\n";
            $html.= "    </tr>\n";
            $html.= "     <tr class=\"row0\">\n";
            $html.= "      <td>创建时间：</td>\n";
            $html.= "      <td>".time_format()."</td>\n";
            $html.= "    </tr>\n";
            $html.= "     <tr class=\"row0\">\n";
            $html.= "      <td>姓名：</td>\n";
            $html.= "      <td>".$data['ext1']."</td>\n";
            $html.= "    </tr>\n";
            $html.= "    <tr class=\"row0\">\n";
            $html.= "      <td>电话：</td>\n";
            $html.= "      <td>".$data['ext2']."</td>\n";
            $html.= "    </tr>\n";
            $html.= "    <tr class=\"row0\">\n";
            $html.= "      <td>Email：</td>\n";
            $html.= "      <td>".$data['ext3']."</td>\n";
            $html.= "    </tr> \n";
            $html.= "    <tr class=\"row0\">\n";
            $html.= "      <td>主题：</td>\n";
            $html.= "      <td>".$data['ext5']."</td>\n";
            $html.= "    </tr>\n";
            $html.= "    <tr class=\"row0\">\n";
            $html.= "      <td>备注：</td>\n";
            $html.= "      <td>".$data['ext6']."</td>\n";
            $html.= "    </tr>\n";
            $html.= "</table>\n";
            $body=$html;
            if(send_mail($to,$subject,$body)){
            }
            apiReturn(CodeModel::CORRECT,'Thanks, we will feedback to your ASAP');
        } else {
            apiReturn(CodeModel::ERROR,'Failed to submit.');
        }
    }

    public function pay(){
        $orderno = I('orderno');
        if($orderno){
            $order = OrderModel::getOrderByOrderno($orderno);
            $this->assign ( 'order',$order);
            $this->display();
        }else{
            $this->redirect('/member/order');
        }
    }

    //生成验证码
    public function verify(){
        verify();
    }

}
?>