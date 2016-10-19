<?php

namespace Admin\Controller;

class MagicController extends BaseController {
	public function index() {
	}

	/**
	 * 智能点餐列表，分页
	 */
	public function magic() {
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		$status = I ( 'status' );
	
		switch ($searchtype) {
			case '0' :
				$magicno = $keyword;
				break;
			case '1' :
				$username = $keyword;
				break;
			case '2' :
				$telephone = $keyword;
				break;
		}
	
		if (is_numeric ( $status )) {
			$where ['status'] = $status;
		}
 
		$where['shop_id']=get_role('shop');
		 
	
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
	
		$rs = M ( "magic" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
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
	
	// 添加智能点餐
	public function addMagic($pid = 0) {
		if (IS_POST) {
			$db = D ( "magic" );
			$data = empty ( $data ) ? $_POST : $data;
			 
			$data ['addip'] = get_client_ip ();


			//计算分组菜品数量和金额
			$arr=str2arr($data['contentids']);
			$arr=array_filter($arr);
			$arr=array_unique($arr);
			$num= count($arr);
			$where=array();
			$where['id']=array('in',$arr);
			$amount=M('content')->where($where)->sum('price');
			
			$data['num']=$num;
			$data['amount']=$amount;
			
			if (false !== $db->add ( $data )) {
				$this->success ( "添加分组产品成功！" );
			} else {
				$this->error ( '添加分组产品失败！' );
			}
		} else {
			$sort = M ( "magic" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid );
				
			// 输出分组产品列表
			$typelist = M ( "magictype" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "typelist", $typelist );

			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$shoplist = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $shoplist ); 
			
			// 输出当前Magic列表
			$list = M ( "magic" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
				
			$this->display ('addMagic');
		}
	}
	
	// 编辑分组产品
	public function editMagic() {
		$id = I ( 'id' );
		if (IS_POST) {
			$db = D ( "magic" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data ); 
			if ($data) { 
				
				//计算分组产品菜品数量和金额
				$arr=str2arr($data['contentids']);
				$arr=array_filter($arr);
				$arr=array_unique($arr);
				$num= count($arr);
				$where=array();
				$where['id']=array('in',$arr);
				$amount=M('content')->where($where)->sum('price');
				
				$data['num']=$num;
				$data['amount']=$amount;

				if ($db->save ( $data ) !== false) { 
					$this->success ( "编辑分组产品成功！" );
				} else {
					$this->error ( '编辑分组产品失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
				
			$db = M ( "magic" )->find ( $id );
			$this->assign ( "db", $db );

			$where=array();
			$where['id']=array('in',$db['contentids']);
			$list=M('content')->where($where)->select();
			$this->assign ( "list",$list ); 
			
			// 输出分组产品列表
			$typelist = M ( "magictype" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "typelist", $typelist );
			

			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$shoplist = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $shoplist );
			
// 			// 输出当前Magic列表
// 			$list = M ( "magic_detail" )->where ( 'magicno=' . $db ['magicno'] )->order ( 'id asc' )->select ();
// 			$this->assign ( "list", $list );
			$this->display ('editMagic');
		}
	}
	
	// 删除分组产品
	public function deleteMagic() {
		$id = I ( 'id' );
		$db = M ( "magic" )->delete ( $id );
		
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	

	/**
	 * 分组产品列表，分页
	 */
	public function magictype() {
		$list = M ( "magictype" )->select ();
		$this->assign ( "list", $list );
		$this->display ();
	}
	
	// 添加分组
	public function addMagictype($pid = 0) {
		if (IS_POST) {
			$db = D ( "magictype" );
			$data = empty ( $data ) ? $_POST : $data;
	
			//$data ['addip'] = get_client_ip ();
	
			if (false !== $db->add ( $data )) {
				$this->success ( "添加分组成功！" );
			} else {
				$this->error ( '添加分组失败！' );
			}
		} else {
			$sort = M ( "magictype" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid ); 
			$this->display ('addMagictype');
		}
	}
	
	// 编辑分组
	public function editMagictype() {
		$id = I ( 'id' );
		if (IS_POST) {
			$db = D ( "magictype" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
	
			if ($data) {  
				if ($db->save ( $data ) !== false) { 
					$this->success ( "编辑分组成功！" );
				} else {
					$this->error ( '编辑分组失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
	
			$db = M ( "magictype" )->find ( $id );
			$this->assign ( "db", $db ); 
			$this->display ('editMagictype');
		}
	}
	
	// 删除分组 
	public function deleteMagictype() {
		$id = I ( 'id' );
		$db = M ( "magictype" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
}
?>