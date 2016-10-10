<?php
// 购物车类
namespace Home\Controller;

use Think\Controller;

class WechatController extends Controller { 

    protected $wechatObj;

    /**
     * 初始化微信接口类
     *
     * @return \Org\Util\Wechat
     */
    public function _initialize()
    {
        if (! C('config.WEB_SITE_WECHAT')) {
            echo ('access denid.');
            exit();
        } 
        $this->wechatObj = get_wechat_obj();
    }

    /**
     * 微信接口主程序
     */
    public function index()
    {
        $wechat = $this->wechatObj;
        $wechat->valid();
        $type = $wechat->getRev()->getRevType();
        // 注册新用户
        // $this->register ( $wechat->getRevFrom () );
        
        switch ($type) {
            case $wechat::MSGTYPE_TEXT:
                
                // 文字处理
                $msg = $wechat->getRevContent();
                $this->getKeyword($wechat, $msg);
                break;
            case $wechat::MSGTYPE_EVENT:
                
                // 事件处理
                $msg = $wechat->getRevEvent();
                switch (strtolower($msg['event'])) {
                    case 'subscribe':
                        $this->subscribe($wechat->getRevFrom());
                        
                        $this->getKeyword($wechat, 'subscribe');
                        break;
                    case 'unsubscribe':
                        $this->unsubscribe($wechat->getRevFrom());
                        break;
                    case 'click':
                        $this->getKeyword($wechat, $msg['key'], 'click');
                        break;
                    case 'view':
                        
                        // $wechat->text($this->getOpenUrl($msg['EventKey'],$wechat->getRevFrom()))->reply();
                        break;
                    case 'location':
                        
                        // $msg = $wechat->getRevEventGeo ();
                        // S ( 'pos_' . $wechat->getRevFrom (), serialize ( $msg ) );
                        break;
                    case 'scan':
                        
                        // $this->scan ( $wechat, $msg ['key'] );
                        break;
                }
                
                break;
            case $wechat::MSGTYPE_IMAGE:
                
                // 返回收到的图片地址
                $msg = $wechat->getRevPic();
                $wechat->text($msg)->reply();
                break;
            case $wechat::MSGTYPE_LOCATION:
                
                // 返回当前地理位置
                $msg = $wechat->getRevGeo();
                $wechat->text(arr2str($msg))->reply();
                break;
            case $wechat::MSGTYPE_LINK:
                break;
            case $wechat::MSGTYPE_MUSIC:
                break;
            case $wechat::MSGTYPE_NEWS:
                break;
            case $wechat::MSGTYPE_VOICE:
                
                // 语音处理：返回语音识别成文字结果
                $msg = $wechat->getRevVoice();
                $this->getKeyword($wechat, $msg['recognition']);
                
                break;
            case $wechat::MSGTYPE_VIDEO:
                break;
            
            default:
            // $weixin->text ( "help info" )->reply ();
        }
    }

    /**
     * 取关键词表的关键词ID
     *
     * @param unknown $obj            
     * @param unknown $msg            
     */
    private function getKeyword($obj, $msg, $type = '')
    {
        $db = $this->getMyReply($msg, $obj->getRevFrom());
        if ($db) {
            $this->sendReplySingle($obj, $db);
        }else{
        	$obj->transfer_customer_service ( )->reply ();
        }
    }

    /**
     * 单条回复
     *
     * @param unknown $obj            
     * @param unknown $db
     *            $type: 0-文字，1-图片，2-图文，3-语音，4-视频，5-app
     */
    protected function sendReplySingle($obj, $db)
    {
        switch ($db[1]) {
            case '0':
            case 'text':
                
                // 文字
                $obj->text($this->replaceHref($db[0], $obj->getRevFrom()))
                    ->reply();
                break;
            case '1':
                
                // 图片:/Public/uploadfile/file/2014-08-25/53fabf1b81301.jpg
                $img = '@' . dirname($_SERVER['SCRIPT_FILENAME']) . $db['info'];
                $data = array(
                    'media' => $img
                );
                $ret = $obj->sendMedia($data, 'image');
                $obj->image($ret['media_id'])->reply();
                break;
            case '3':
                
                // 语音
                $img = '@' . dirname($_SERVER['SCRIPT_FILENAME']) . $db['info'];
                $data = array(
                    'media' => $img
                );
                $ret = $obj->sendMedia($data, 'voice');
                $obj->voice($ret['media_id'])->reply();
                break;
            case '4':
                
                // 视频
                $img = '@' . dirname($_SERVER['SCRIPT_FILENAME']) . $db['info'];
                $data = array(
                    'media' => $img
                );
                $ret = $obj->sendMedia($data, 'video');
                $obj->video($ret['media_id'])->reply();
                break;
            
            case '2':
            case 'news':
                
                $obj->news($db[0])->reply();
                break;
        }
    }

    /**
     * 读取数据库，自动回复
     *
     * @param string $msg            
     * @param string $openid            
     * @return multitype:string
     */
    private function getMyReply($msg = '', $openid = '', $shopid = 1)
    {
        $where = array();
        $where['status'] = 1;
        $where['title'] = $msg;
        
        $db = M('wechat_reply')->where($where)
            ->order('id desc')
            ->find();
        if ($db == false) {
            $where['title'] = array(
                'like',
                '%' . $msg . '%'
            );
            $db = M('wechat_reply')->where($where)
                ->order('id desc')
                ->find();
        }
        
        if ($db == false) {
            $where = array();
            $where['status'] = 1;
            $where['title'] = 'default';
            $db = M('wechat_reply')->where($where)
                ->order('id desc')
                ->find();
        }
        if ($db) {
            if ($db['MsgType'] == 'text') {
                $reply = array(
                    $db['Content'],
                    'text'
                );
            } else 
                if ($db['MsgType'] == 'news') {
                    $where = array();
                    $where['replyid'] = $db['id'];
                    $detail = M('wechat_reply_detail')->field('Title,Description,PicUrl,Url')
                        ->where($where)
                        ->order('id asc')
                        ->select();
                    foreach ($detail as $k => $v) {
                        $detail[$k]['Url'] = $this->getOpenUrl($detail[$k]['Url'], $openid);
                    }
                    $reply = array(
                        $detail,
                        'news'
                    );
                }
        } else {
            $reply = null;
        }
        
        return $reply;
    }

    /**
     * 给URL加上openid后缀
     *
     * @param string $url            
     * @param string $openid            
     * @return string
     */
    public function getOpenUrl($url = '', $openid = '')
    {
        if (strpos($url, '?')) {
            $url .= '&_openid=' . $openid;
        } else {
            $url .= '?_openid=' . $openid;
        }
        return $url;
    }

    public function getDomainUrl($picurl = '')
    {
        $url = 'http://resource' . PANEL_DOMAIN . '' . $picurl;
        return $url;
    }

    /**
     * 提取文本里的a，加上openid
     *
     * @param string $content            
     */
    public function replaceHref($content = '', $openid = '')
    {
        return preg_replace('/href=[\'|\"](\S+)[\'|\"]/i', "href='" . $this->getOpenUrl('${1}', $openid) . "'", $content);
    }

    /**
     * OPEN用户注册
     *
     * @param unknown $openid            
     */
    public function register($openid)
    {}

    /**
     * 消息记录，api方式
     */
    private function log($data = '')
    {
        M('wechat_msg')->add($data);
    }

    /**
     * 关注，设置会员关注时间和状态
     *
     * @param string $openid            
     */
    private function subscribe($openid = '')
    {}

    /**
     * 取消关注，设置会员取消时间和状态
     *
     * @param string $openid            
     */
    private function unsubscribe($openid = '')
    {}
}
?>