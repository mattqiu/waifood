<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;

use Common\Model\ContentModel;

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