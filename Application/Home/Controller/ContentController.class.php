<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

class ContentController extends BaseController {
	 
	public function index() {
	}
	public function view($id = 0) {
		// 1. 基本信息
		$where = array ();
		$where ['status'] = 1;
		$db = M ( "content" )->where ( $where )->find ( $id );
		if ($db) {
			$this->assign ( "db", $db );
			
			// 2. 栏目信息
			$cateinfo = M ( 'channel' )->where ( 'id=' . $db ['pid'] )->find ();
			
			// 没有下级就取同级栏目列表
			$where = array ();
			$where ['status'] = 1;
			$where ['pid'] = $cateinfo ['pid'];
			$channel = M ( 'channel' )->where ( $where )->order ( 'sort asc' )->select ();
			if ($cateinfo ['pid'] == 0) {
				$this->assign ( 'fathername', $cateinfo ['name']);
			} else {
				$this->assign ( 'fathername', get_data ( $cateinfo ['pid'], 'channel' ) );
			}
			
			// // 3. 当前位置信息
			// $this->assign('location',get_location($db['pid']));
			
			// // 4. 上下篇
			// //上一篇
			// $where = array ();
			// $where ['status'] = 1;
			// $where ['pid'] = $cateinfo ['id'];
			// $where ['id'] = array('neq',$db['id']);
			// $where ['sort'] = array('lt',$db['sort']);
			// $order='sort desc';
			// $rs = M ( "content" )->where ( $where )->order($order)->find ();
			// if($rs){
			// $this->assign('prev','<a href="'.U('Content/view','id='.$rs['id']).'">'.$rs['title'].'</a>');
			// }else{
			// $this->assign('prev','无');
			
			// }
			// //下一篇
			// $where = array ();
			// $where ['status'] = 1;
			// $where ['pid'] = $cateinfo ['id'];
			// $where ['id'] = array('neq',$db['id']);
			// $where ['sort'] = array('gt',$db['sort']);
			// $order='sort asc';
			// $rs = M ( "content" )->where ( $where )->order($order)->find ();
			// if($rs){
			// $this->assign('next','<a href="'.U('Content/view','id='.$rs['id']).'">'.$rs['title'].'</a>');
			// }else{
			// $this->assign('next','无');
			
			// }
			
			// 赋值
			$this->assign ( 'channel', $channel );
			$this->assign ( 'cateinfo', $cateinfo );
			$this->assign ( 'pid', $db ['pid'] );
			$this->assign ( 'channelname', get_cate ( $db ['pid'] ) );
			
			$this->assign ( 'title', $db ['title'] );
			$this->assign ( 'keywords', $db ['keywords'] );
			$this->assign ( 'description', $db ['description'] );
		} else {
			$this->error ( '对不起，您访问的信息不存在！' );
		}
		$this->display ();
	}
	/**
	 * 内容管理
	 */
	public function lists($id = null) {
		// 输出当前Content列表
		$where = array ();
		if (isset ( $id )) {
			$where ['sortpath'] = array (
					'like',
					'%,' . $id . ',%' 
			);
		}
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "content" )->where ( $where )->order ( 'sort desc' )->page ( $p, $row );
		$list = $rs->select ();
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
			$page->setConfig ( 'prev', '上一页' );
			$page->setConfig ( 'next', '下一页' );
			$this->assign ( 'page', $page->showm () );
		}
		
		if ($count == 1) {
			$this->redirect ( 'Content/view?id=' . $list [0] ['id'] );
			exit ();
		}
		
		// 当前栏目信息
		$cateinfo = M ( 'channel' )->where ( 'id=' . $id )->find ();
		if ($cateinfo) {
			// 3. 当前位置信息
			// $this->assign('location',get_location($cateinfo['id']));
			
			$where = array ();
			$where ['status'] = 1;
			$where ['id'] = array (
					'neq',
					$id 
			);
			$where ['sortpath'] = array (
					'like',
					'%,' . $id . ',%' 
			);
			
			// 取下级栏目列表
			$channel = M ( 'channel' )->where ( $where )->order ( 'sort asc' )->select ();
			$this->assign ( 'fathername', $cateinfo ['name'] );
		 
			if ($channel == null) {
				// 没有下级就取同级栏目列表
				$where = array ();
				$where ['status'] = 1;
				$where ['pid'] = $cateinfo ['pid'];
				$channel = M ( 'channel' )->where ( $where )->order ( 'sort asc' )->select ();
				if ($cateinfo ['pid'] == 0) {  
				} else {
					$this->assign ( 'fathername', get_data ( $cateinfo ['pid'], 'channel' ) );
				}
			} else {
				
				$depth = $cateinfo ['depth'];
				$this->assign ( 'title', $cateinfo ['name'] );
				if ($depth == 2) {
					$where = array ();
					$where ['status'] = 1;
					$where ['pid'] = $id;
					$channel = M ( 'channel' )->where ( $where )->order ( 'sort asc' )->select ();
					$this->assign ( 'channel', $channel );
					$this->display ( 'Content/channel' );
				} else {
					$where = array ();
					$where ['status'] = 1;
					$where ['pid'] = $id;
					$db = M ( "channel" )->field ( 'id,name,indexpic' )->where ( $where )->select ();
					if ($db) {
						
						foreach ( $db as $k => $v ) {
							$db [$k] ['sub'] = $this->getSubChannel ( $v ['id'] );
						}
					}
					$this->assign ( "channel", $db );
					$this->display ( 'Content/channel1' );
				}
				exit ();
			}
			
			// 赋值
			$this->assign ( 'channel', $channel );
			$this->assign ( 'pid', $id );
			$this->assign ( 'cateinfo', $cateinfo );
			$this->assign ( 'channelname', $cateinfo ['name'] );
			
			$this->assign ( 'title', $cateinfo ['name'] );
			$this->assign ( 'keywords', $cateinfo ['name'] );
			$this->assign ( 'description', $cateinfo ['name'] );
		} else {
			
			$this->error ( '对不起，您访问的信息不存在！' );
		}
		$this->display ();
	}
	public function getSubChannel($pid = 0) {
		$where = array ();
		$where ['status'] = 1;
		$where ['pid'] = $pid;
		$order = 'sort desc';
		$db = M ( "content" )->field ( 'id,title,indexpic' )->where ( $where )->order ( $order )->select ();
		
		return $db;
	}
}
?>