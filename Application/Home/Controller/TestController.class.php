<?php
// 购物车类
namespace Home\Controller;

class TestController extends BaseController
{

    public function index()
    {
        $weChat = get_wechat_obj();
        // $openid='o7dnet6IL31Mwp05jIWruW7dS40k';
        // $user=$weChat->getUserInfo($openid);
        $data = array();
        $data['touser'] = 'o7dnet6IL31Mwp05jIWruW7dS40k';
        $data['msgtype'] = 'text';
        $data['text'] =array('content'=>'thank you for order, we will delvier on time.');
        $ret = $weChat->sendCustomMessage($data);
        we($ret);
    }
}
?>