<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;

use Admin\Model\BannerModel;
use Common\Model\AddressModel;
use Common\Model\CodeModel;
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
        $banner = BannerModel::getBannerByType(BannerModel::PC_BANNER);// huo
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
        if($dateData = DateModel::getFutureDay(27)){
            $this->assign ( 'dateData',$dateData);
            $this->assign ( 'beyond',DateModel::DELIVERTIME_BEYOND);
            $this->assign ( 'hours', date('H'));
            $this->assign ( 'min', date('i'));
        }

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
        $this->display();
    }


    public function subWishList(){
        $data = empty ( $data ) ? $_POST : $data;
        if(isN($data['ext1'])){
            apiReturn(CodeModel::ERROR,'Sorry, your name cannot be empty!');
        }
        if(isN($data['ext2'])){
            apiReturn(CodeModel::ERROR,'Sorry, phone number cannot be empty!');
        }
        if(!is_email($data['ext3'])){
            apiReturn(CodeModel::ERROR,'Sorry, email is illegal!');
        }
        if(isN($data['ext5'])){
            apiReturn(CodeModel::ERROR,'Sorry, subject cannot be empty!');
        }
        if(isN($data['ext6'])){
            apiReturn(CodeModel::ERROR,'Sorry, wish list can not be empty!');
        }
        $data ['addip'] = get_client_ip ();
        if (false !== D ( "form" )->add ( $data )) {
            //管理员邮件：
            //send_mail();
            $to=C('config.WEB_SITE_COPYRIGHT');
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
            apiReturn(CodeModel::CORRECT,'Congratulations, submitted successfully!');
        } else {
            apiReturn(CodeModel::ERROR,'Sorry, submission failed!');
        }
    }

    //生成验证码
    public function verify(){
        verify();
    }

}
?>