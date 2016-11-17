<?php
// 购物车类
namespace Shop\Controller;

class WechatController extends BaseController {
	public function index() {
		if(C('config.WEB_SITE_WECHAT')){
			/* 加载微信SDK */
			$weixin = new \Org\Util\Wechat ();
			
			/* 获取请求信息 */
			$data = $weixin->request ();
			/* 获取回复信息 */
			list ( $content, $type ) = $this->replyAuto ( $data );
			
			// 接收到的信息入不同的库
			$this->wechatlog ( $data );
			
			/* 响应当前请求 */
			$weixin->response ( $content, $type,0,$data['FromUserName'] );
				
		}else{
			E('微信接口未启用！');
		}
	}
	private function wechatlog($data) {
		// $ctrl=new \Think\Log();
		// $ctrl->write(json_encode($data));
		M ( 'wechat_msg' )->add ( $data );
	}
	public function replyAuto($data) {
		// 消息类型：text,image,voice,video,location,link
		switch ($data ['MsgType']) {
			case 'text' : // 类型是文本的
				switch(strtolower($data ['Content'])){
					case 'q':
						$reply=$this->getJoke();
						break;
					default:
						$reply = $this->getMyReply ( $data ['Content'], $data ['FromUserName'] );
						break;
				}
				break;
			case 'event' : // 类型是事件的
			               // 事件类型：subscribe,unsubscribe,SCAN(关注过扫描),LOCATION,CLICK,VIEW
				switch ($data ['Event']) {
					case 'subscribe' : // 刚刚关注的
						$reply = $this->getMyReply ( 'default', $data ['FromUserName'] );
						break;
					case 'CLICK' : // 点击的事件
						$reply = $this->getMyReply ( $data ['EventKey'], $data ['FromUserName'] );
						break;
					case 'VIEW' : // 链接
						//$reply = $this->getMyReply ( $this->getUrl ( $data ['EventKey'], $data ['FromUserName'] ), $data ['FromUserName'] );
						
						break;
					
					default :
						$reply = array (
								'暂未支持相关事件',
								'text' 
						);
						break;
				}
				
				break;
			default :
				$reply = array (
						'暂未支持相关消息类型',
						'text' 
				);
				break;
		}
		return $reply;
	}
	
	private function getJoke(){
		$db=M('joke')->order('rand()')->find();
		$reply = array (
				$db ['content'],//.$db['image'],
				'text'
		);
		return $reply;
	}
	/**
	 * 读取数据库，自动回复
	 * 
	 * @param string $msg        	
	 * @param string $openid        	
	 * @return multitype:string
	 */
	private function getMyReply($msg = '', $openid = '') {
		$where = array ();
		$where ['status'] = 1;
		$where ['title'] = $msg;
		$db = M ( 'wechat_reply' )->where ( $where )->order ( 'id desc' )->find ();
		if ($db == false) {
			$where ['title'] = array (
					'like',
					'%' . $msg . '%' 
			);
			$db = M ( 'wechat_reply' )->where ( $where )->order ( 'id desc' )->find ();
		}
		;
		
		if ($db) {
			if ($db ['MsgType'] == 'text') {
				$reply = array (
						$db ['Content'],
						'text' 
				);
			} else if ($db ['MsgType'] == 'news') {
				$where = array ();
				$where ['replyid'] = $db ['id'];
				$detail = M ( 'wechat_reply_detail' )->field ( 'title,description,url,picurl' )->where ( $where )->order ( 'id asc' )->select ();
				$reply = array (
						$detail,
						'news' 
				);
			}
		} else {
			$reply = array (
					'欢迎使用微信！',
					'text' 
			);
		}
		return $reply;
	}
	
	/**
	 * 处理菜单链接事件，加openid参数
	 * 
	 * @param string $url        	
	 * @param string $openid        	
	 */
	private function getUrl($url = null, $openid = '') {
		if($url!=''){
		if (strpos ( '?', $url )) {
			$url = $url . '&openid=' . $openid;
		} else {
			$url = $url . '?openid=' . $openid;
		}
		}
		return $url;
	}
	
	/**
	 * 测试用
	 * 
	 * @param string $openid        	
	 */
	public function test($openid = '') {
		$where = array ();
		$where ['title'] = array (
				'like',
				'%2%' 
		);
		$db = M ( 'wechat_reply' )->where ( $where )->order ( 'id desc' )->find ();
		
		$where = array ();
		$where ['replyid'] = $db ['id'];
		$detail = M ( 'wechat_reply_detail' )->field ( 'title,description,url,picurl' )->where ( $where )->order ( 'id asc' )->select ();
		
		$openid = 'oWeCltz8iJgyQ9eWjnR9gWFQ-SSg';
		$restr = $this->send ( $detail, $openid, 'news' );
		dump ( $restr );
	}
	
	/**
	 * 获取微信用户基础资料
	 *
	 * @param string $openid
	 *        	用户openid
	 * @return array; 响应的数据
	 */
	private function getuser($openid = '') {
		$weixin = new \Org\Util\Wechat ();
		// $openid = 'o0ehLt1pOAIEFZtPD4ghluvjamf0';
		$weuser = $weixin->user ( $openid );
		// dump($weuser);
		return $weuser;
	}
	
	/**
	 * 主动发送消息
	 *
	 * @param string $content
	 *        	内容
	 * @param string $openid
	 *        	发送者用户名
	 * @param string $type
	 *        	类型
	 * @return array 返回的信息
	 */
	private function send($content, $openid = '', $type = 'text') {
		$weixin = new \Org\Util\Wechat ();
		// $openid = 'o0ehLt1pOAIEFZtPD4ghluvjamf0';
		$restr = $weixin->sendMsg ( $content, $openid, $type );
		return $restr;
	}
}
?>