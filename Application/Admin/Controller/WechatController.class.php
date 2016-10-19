<?php

namespace Admin\Controller;

class WechatController extends BaseController {
	public function _initialize() { 
		if(C('config.WEB_SITE_WECHAT')==false){
			E('微信接口未启用！');
		}
	}

	public function setting(){ 
	    if(IS_POST){
	        $data=$_POST;
	        foreach($data as $k=>$v){
	            if(!($k=="PREFIX"||$k=='FID')){
	                if(!is_number($v)){
	                    $this->error('对不起，除了推广参数，其余设置均不能为空且必须是数字！');
	                }
	            }
	        }
	        if(isN($data['FID'])){
	            $data['FID']='fid';
	        }
	        $config['COUPON']=$data;
	        $filename=APP_PATH."/Common/Conf/coupon.php";
	        arr2file($filename,$config);
	        $this->success('恭喜，参数已保存！');
	    }else{
	        $this->assign('title','参数设置');
	        $this->display();
	    }
	}
	
	/**
	 * 回复列表，分页
	 */
	public function reply() {
		$usereal = null; 
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		$status = I ( 'status' );
		
		switch ($searchtype) {
			case '0' :
				$usereal = $keyword;
				break; 
		}
		
		if (is_numeric ( $status )) {
			$where ['status'] = $status;
		}
		
		if (! isN ( $usereal )) {
			$where ['title'] = array (
					'like',
					'%' . $usereal . '%' 
			);
		} 
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "wechat_reply" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
		$list = $rs->select ();
		
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( 'page', $page->show () );
		}
		
		$this->assign ( "keyword", $keyword );
		$this->assign ( "status", $status );
		$this->assign ( "searchtype", $searchtype );
		
		$this->display ();
	}
	
	private function getUrl($url=''){
		if($url==''){
			return $url;
		}else{
			if(substr($url,0,4)!='http'){
				$host = 'http://'.I('server.HTTP_HOST');
				if(substr($url,0,1)=='/'){
					return $host.$url;
				}else{
					return $host.'/'.$url;
				}
			}else{
				return $url;
			}
		}
	}
	// 添加回复
	public function addReply($pid = 0) {
		if (IS_POST) {
			$db = D ( "wechat_reply" );
			$data = empty ( $data ) ? $_POST : $data;
			if ($data ['MsgType'] == '') {
				$this->error ( '必须选择回复类型！' );
			}
			$items = ($data ['items']);
			$data ['ArticleCount'] = count ( $items ) / 4;
			$data ['addip'] = get_client_ip ();
			
			$data = $db->create ( $data );
			
			if ($data) {
				$id = $db->add ( $data );
				if ($id !== false) {
				
					if ($data ['MsgType'] == 'news') {
						
						for($i = 0; $i < count ( $items ); $i += 4) {
							$array = array ();
							$array ['replyid'] = $id;
							$array ['Title'] = $items [$i];
							$array ['PicUrl'] = $this->getUrl($items [$i + 1]);
							$array ['Url'] = $items [$i + 2];
							$array ['Description'] = $items [$i + 3];
							$dataList [] = $array;
						}
						M ( 'wechat_reply_detail' )->addAll ( $dataList );
					}
					
					$this->success ( "添加回复成功！" );
				} else {
					$this->error ( '添加回复失败！' );
				}
			} else {
				
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "wechat_reply" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			
			$this->display ();
		}
	}
	
	// 编辑回复
	public function editReply() {
		$id = I ( 'id' );
		if (IS_POST) {
			$db = D ( "wechat_reply" );
			$data = empty ( $data ) ? $_POST : $data;
			
			if ($data ['MsgType'] == '') {
				$this->error ( '必须选择回复等级！' );
			}
			if ($data ['MsgType'] == 'news') {
				$data ['Content'] = null;
			} else {
				$data ['items'] = null;
			}
			$items = ($data ['items']);
			$data ['ArticleCount'] = count ( $items ) / 4;
			
			$dbrs = $db->create ( $data );
			
			if ($dbrs) {
				if ($db->save ( $dbrs ) !== false) {
					$where = array ();
					$where ['replyid'] = $id;
					$db = M ( "wechat_reply_detail" )->where ( $where )->delete ();
					
					if ($data ['MsgType'] == 'news') {
						
						for($i = 0; $i < count ( $items ); $i += 4) {
							$array = array ();
							$array ['replyid'] = $id;
							$array ['Title'] = $items [$i];
							$array ['PicUrl'] = $this->getUrl($items [$i + 1]);
							$array ['Url'] = $items [$i + 2];
							$array ['Description'] = $items [$i + 3];
							$dataList [] = $array;
						}
						M ( 'wechat_reply_detail' )->addAll ( $dataList );
					}
					$this->success ( "编辑回复成功！" );
				} else {
					$this->error ( '编辑回复失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			
			$db = M ( "wechat_reply" )->find ( $id );
			$this->assign ( "db", $db );
			
			$where = array ();
			$where ['replyid'] = $id;
			$detail = M ( "wechat_reply_detail" )->where ( $where )->order('id asc')->select ();
			$this->assign ( "detail", $detail );
			
			$this->display ('editReply');
		}
	}
	
	// 删除回复
	public function deleteReply($id) {
		$db = M ( "wechat_reply" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * 消息列表，分页
	 */
	public function msg() { 
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		$status = I ( 'status' );
		 
		
		if (! isN ( $keyword )) {
			$where ['Content'] = array (
					'like',
					'%' . $keyword . '%' 
			);
		}
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "wechat_msg" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
		$list = $rs->select ();
		
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( 'page', $page->show () );
		} 
		$this->assign ( "keyword", $keyword ); 
		$this->assign ( "searchtype", $searchtype );
		
		$this->display ();
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
	
		$weixin = new \Org\Util\Wechat();
		// $openid = 'o0ehLt1pOAIEFZtPD4ghluvjamf0';
		$restr = $weixin->sendMsg ( $content, $openid, $type );
		return $restr;
	}
	// 添加消息
	public function addMsg($pid = 0) {
		if (IS_POST) {
			$db = D ( "wechat_msg" );
			$data = empty ( $data ) ? $_POST : $data;
			if ($data ['MsgType'] == '') {
				$this->error ( '必须选择消息类型！' );
			} 
			
			$data = $db->create ( $data );
			
			if ($data) {
				if ($db->add ( $data ) !== false) {
					
					//调用api:目前只支持发送文本
					$content = $data['Content']; 
					$this->send($content);
					 
					
					$this->success ( "添加消息成功！" );
				} else {
					$this->error ( '添加消息失败！' );
				}
			} else {
				
				$this->error ( $db->getError () );
			}
		} else {  
			$this->display ();
		}
	}
	
	// 编辑消息
	public function editMsg() {
		$id = I ( 'id' );
		if (IS_POST) {
			$db = D ( "wechat_msg" );
			$data = empty ( $data ) ? $_POST : $data;

			if ($data ['MsgType'] == '') {
				$this->error ( '必须选择消息类型！' );
			}
			
			$data = $db->create ( $data );
			
			if ($data) {
				if ($db->save ( $data ) !== false) {
					$this->success ( "编辑消息成功！" );
				} else {
					$this->error ( '编辑消息失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$db = M ( "wechat_msg" )->find ( $id );
			$this->assign ( "db", $db );
			$this->display ();
		}
	}
	
	// 删除消息
	public function deleteMsg($id) {
		$db = M ( "wechat_msg" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * 获取菜单
	 */
	public function getMenu() {
		/* 加载微信SDK */
		$weixin = get_wechat_obj();
		$str = $weixin->getMenu ();
		// $str = M ( 'wechat' )->where ( 'id=1' )->getField ( 'menu' );
		if (  $str ) {
			$this->success ( $str );
		} else {
			$this->error ( '服务器菜单更新失败！' );
		}
	}
	
	/**
	 * 设置菜单
	 */
	public function setMenu() {
		/* 加载微信SDK */
		$menu = M ( 'wechat' )->getField ( 'menu' );
		$weixin = get_wechat_obj (); 
		$menu=(json_decode($menu,true));   
		$str = $weixin->createMenu ( $menu); 
		if ($str) {
			$this->success ( '服务器菜单已更新！' );
		} else {
			$this->error ( '服务器菜单更新失败！' );
		}
	}
	
	/**
	 * 本地保存菜单
	 */
	public function menu() {
		if (IS_POST) {
			
			$sub1 = array ();
			$sub2 = array ();
			$sub3 = array ();
			
			$data = empty ( $data ) ? $_POST : $data;
			$items = '';
			$items = implode ( $data, ',' );
			$data ['submenu1'] = false;
			$data ['submenu2'] = false;
			$data ['submenu3'] = false;
			// 菜单1
			for($i = 1; $i < 19; $i ++) {
				$data ['menu1'] [] = $data ['item1_' . $i];
			}
			for($i = 3; $i < 19; $i ++) {
				if (isN ( $data ['menu1'] [$i] ) == false) {
					$data ['submenu1'] = true;
					break;
				}
			}
			if ($data ['submenu1']) {
				for($i = 3; $i < 19; $i += 3) {
					if (isN ( $data ['menu1'] [$i + 1] ) && isN ( $data ['menu1'] [$i + 2] )) {
					} else {
						if (isN ( $data ['menu1'] [$i + 1] ) == false) {
							$data ['menu1'] [$i + 2] = '';
							$subs1 [] = array (
									'type' => 'click',
									'name' => $data ['menu1'] [$i],
									'key' => $data ['menu1'] [$i + 1] 
							);
						} else {
							$subs1 [] = array (
									'type' => 'view',
									'name' => $data ['menu1'] [$i],
									'url' => $data ['menu1'] [$i + 2] 
							);
						}
					}
				}
				
				$sub1 = array (
						'name' => $data ['menu1'] [0],
						'sub_button' => $subs1 
				);
			} else {
				if (isN ( $data ['menu1'] [1] ) == false) {
					$sub1 = array (
							'type' => 'click',
							'name' => $data ['menu1'] [0],
							'key' => $data ['menu1'] [1] 
					);
				} else if (isN ( $data ['menu1'] [2] ) == false) {
					
					$sub1 = array (
							'type' => 'view',
							'name' => $data ['menu1'] [0],
							'url' => $data ['menu1'] [2] 
					);
				}
			}
			// 菜单2
			for($i = 1; $i < 19; $i ++) {
				$data ['menu2'] [] = $data ['item2_' . $i];
			}
			for($i = 3; $i < 19; $i ++) {
				if (isN ( $data ['menu2'] [$i] ) == false) {
					$data ['submenu2'] = true;
					break;
				}
			}
			
			if ($data ['submenu2']) {
				for($i = 3; $i < 19; $i += 3) {
					if (isN ( $data ['menu2'] [$i + 1] ) && isN ( $data ['menu2'] [$i + 2] )) {
					} else {
						if (isN ( $data ['menu2'] [$i + 1] ) == false) {
							$data ['menu2'] [$i + 2] = '';
							$subs2 [] = array (
									'type' => 'click',
									'name' => $data ['menu2'] [$i],
									'key' => $data ['menu2'] [$i + 1] 
							);
						} else {
							$subs2 [] = array (
									'type' => 'view',
									'name' => $data ['menu2'] [$i],
									'url' => $data ['menu2'] [$i + 2] 
							);
						}
					}
				}
				
				$sub2 = array (
						'name' => $data ['menu2'] [0],
						'sub_button' => $subs2 
				);
			} else {
				if (isN ( $data ['menu2'] [1] ) == false) {
					$sub2 = array (
							'type' => 'click',
							'name' => $data ['menu2'] [0],
							'key' => $data ['menu2'] [1] 
					);
				} else if (isN ( $data ['menu2'] [2] ) == false) {
					$sub2 = array (
							'type' => 'view',
							'name' => $data ['menu2'] [0],
							'url' => $data ['menu2'] [2] 
					);
				}
			}
			
			// 菜单3
			for($i = 1; $i < 19; $i ++) {
				$data ['menu3'] [] = $data ['item3_' . $i];
			}
			for($i = 3; $i < 19; $i ++) {
				if (isN ( $data ['menu3'] [$i] ) == false) {
					$data ['submenu3'] = true;
					break;
				}
			}
			
			if ($data ['submenu3']) {
				for($i = 3; $i < 19; $i += 3) {
					if (isN ( $data ['menu3'] [$i + 1] ) && isN ( $data ['menu3'] [$i + 2] )) {
					} else {
						if (isN ( $data ['menu3'] [$i + 1] ) == false) {
							$data ['menu3'] [$i + 2] = '';
							$subs3 [] = array (
									'type' => 'click',
									'name' => $data ['menu3'] [$i],
									'key' => $data ['menu3'] [$i + 1] 
							);
						} else {
							$subs3 [] = array (
									'type' => 'view',
									'name' => $data ['menu3'] [$i],
									'url' => $data ['menu3'] [$i + 2] 
							);
						}
					}
				}
				
				$sub3 = array (
						'name' => $data ['menu3'] [0],
						'sub_button' => $subs3 
				);
			} else {
				if (isN ( $data ['menu3'] [1] ) == false) {
					$sub3 = array (
							'type' => 'click',
							'name' => $data ['menu3'] [0],
							'key' => $data ['menu3'] [1] 
					);
				} else if (isN ( $data ['menu3'] [2] ) == false) {
					$sub3 = array (
							'type' => 'view',
							'name' => $data ['menu3'] [0],
							'url' => $data ['menu3'] [2] 
					);
				}
			}
			
			// 生成menu
			$menu = array ();
			if (count ( $sub1 ) > 0) {
				$menu ['button'] [] = $sub1;
			}
			if (count ( $sub2 ) > 0) {
				$menu ['button'] [] = $sub2;
			}
			if (count ( $sub3 ) > 0) {
				$menu ['button'] [] = $sub3;
			}
			
			$menu = jsencode ( $menu );
			$data=array (
			    'menu' => $menu,
			    'items' => $items
			) ; 
			$find=M('wechat')->getField('id');
			if($find){ 
			 $db = M ( 'wechat' )->data($data)->where('id='.$find)->save(); 
			}else{

			    $db = M ( 'wechat' )->data($data)->add();
			}
			if ($db !== false) {
				$this->success ( '菜单更新成功！' );
			} else {
				$this->error ( '菜单更新失败！' );
			}
		} else {
			$db = M ( 'wechat' )->field ( 'items' )->find ();
			$data = str2arr ( $db ['items'] );
			$this->assign ( 'db', $data );
			$this->display ();
		}
	}
}
?>