<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;

class Index1Controller extends BaseController
{

    public function index()
    {
        $ip = file_get_contents("http://api.map.baidu.com/location/ip?ak=7jEcZBZR91Am7YnnGtaEEu0GoxZlIcOj&coor=bd09ll");
        $ip = json_decode($ip,JSON_UNESCAPED_UNICODE);
        dump($ip);
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
}
?>