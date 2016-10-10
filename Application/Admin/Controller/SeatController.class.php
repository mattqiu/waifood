<?php

namespace Admin\Controller;

class SeatController extends BaseController {
	public function index() {
	}


	/**
	 * 订单列表，分页
	 */
	public function order() {
		$orderno = null;
		$username = null;
		$telephone = null;
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		$status = I ( 'status' );
	
		switch ($searchtype) {
			case '0' :
				$orderno = $keyword;
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
	
		if (! isN ( $orderno )) {
			$where ['orderno'] = array (
					'like',
					'%' . $orderno . '%'
			);
		}
		if (! isN ( $username )) {
			$where ['username'] = array (
					'like',
					'%' . $username . '%'
			);
		}
		if (! isN ( $telephone )) {
			$where ['telephone'] = array (
					'like',
					'%' . $telephone . '%'
			);
		} 
			$where['shop_id']=get_role('shop');
	
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
	
		$rs = M ( "order_seat" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
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
	
	// 添加订单
	public function addOrder($pid = 0) {
		if (IS_POST) {
			$db = D ( "order" );
			$data = empty ( $data ) ? $_POST : $data;
			$data ['orderno'] = get_order_no ();
			$data ['addip'] = get_client_ip ();
				
			if (false !== $db->add ( $data )) {
				$this->success ( "添加订单成功！" );
			} else {
				$this->error ( '添加订单失败！' );
			}
		} else {
			$sort = M ( "order_seat" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid );
				
			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$list = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $list );
				
			// 输出当前Order列表
			$list = M ( "order_seat" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
				
			$this->display ();
		}
	}
	
	// 编辑订单
	public function editOrder() {
		$id = I ( 'id' );
		if (IS_POST) {
			$db = D ( "order" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
				
			if ($data) {
				$orderno = $data ['orderno'];
				//如果当前订单是已完成或取消，则不允许修改
				$status=M('order')->where('orderno='.$orderno)->find();
				if ($status == '3' || $status== '4') {
					$this->error('该订单状态不可修改！');
				}
				if ($db->save ( $data ) !== false) {
					if ($data ['status'] == '3' || $data ['status'] == '4') {
						// status:3 已完成，需要加积分
						// status:4 已取消，不做更新操作
	
						if ($data ['status'] == '3') {
							// 增加积分
							$creditrate = C ( 'config.CREDIT_RATE' ) ? 0 : C ( 'config.CREDIT_RATE' );
							$creditrate = is_numeric ( $creditrate ) ? $creditrate : 0;
							if ($creditrate != 0) {
								$db = M ( 'order' )->field ( 'userid,amount' )->where ( 'orderno=' . $orderno )->find ();
								$uid = $db ['userid'];
								$amount = $db ['amount'];
								if (is_numeric ( $uid ) && $uid != 0) {
									$ctrl=A('Admin/Member');
									//参数1：订单消费
									$ret = $ctrl->insertCredit ( $uid, 1, $amount, $orderno );
									if ($ret) {
										return true;
									} else {
										return false;
									}
								}
							} else {
								return true;
							}
						}
	
						//TODO:取消订单需要的操作：返回余额？ 减积分？
						if ($data ['status'] == '4') {
								
						}
					} else {
						$this->updateOrder ( $data ['orderno'] );
					}
					$this->success ( "编辑订单成功！" );
				} else {
					$this->error ( '编辑订单失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
				
			$db = M ( "order_seat" )->find ( $id );
			$this->assign ( "db", $db );
			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$list = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $list );
				
			// 输出当前Order列表
			$list = M ( "order_detail" )->where ( 'orderno=' . $db ['orderno'] )->order ( 'id asc' )->select ();
			$this->assign ( "list", $list );
			$this->display ();
		}
	}
	
	// 删除订单
	public function deleteOrder() {
		$id = I ( 'id' );
		$orderno = M ( "order_seat" )->getFieldbyid ( $id,'orderno' );
	
		if (! isN ( $orderno )) {
			$where=array();
			$where['shop_id']=get_role('shop');
			$where['status']=array('neq',3);
			$db = M ( "order_seat" )->where ($where)->delete ( $id );
			if ($db !== false) {
				$db = M ( "order_detail" )->where ( 'orderno=' . $orderno )->delete ();
				$this->success ( "删除成功！" );
			} else {
				$this->error ( "删除失败" );
			}
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * 订座列表，分页
	 */
	public function seat() {
		$usereal = null;
		$username = null;
		$telephone = null;
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		$status = I ( 'status' );
		
		switch ($searchtype) {
			case '0' :
				$usereal = $keyword;
				break;
			case '1' :
				$username = $keyword;
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
		if (is_numeric( $username )) {
			$where ['shop_id'] =  $username ;
		} 
 
		$where['shop_id']=get_role('shop');
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "seat" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
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
	
	// 添加订座
	public function addSeat($pid = 0) {
		if (IS_POST) {
			$db = D ( "seat" );
			$data = empty ( $data ) ? $_POST : $data;
			if ($data ['usertype'] == '0') {
				$this->error ( '必须选择订座等级！' );
			}
			$data ['addip'] = get_client_ip ();
			$data ['userpwd'] = md5 ( $data ['userpwd'] );
			
			$data = $db->create ( $data );
			
			if ($data) {
				if ($db->add ( $data ) !== false) {
					$this->success ( "添加订座成功！" );
				} else {
					$this->error ( '添加订座失败！' );
				}
			} else {
				
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "seat" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );

			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$list = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $list );
			$this->assign ( "shop_id", session('shop_id') );
			
			$this->display ();
		}
	}
	
	// 编辑订座
	public function editSeat() {
		$id = I ( 'id' );
		if (IS_POST) {
			$db = D ( "seat" );
			$data = empty ( $data ) ? $_POST : $data;
			
			if ($data ['usertype'] == '0') {
				$this->error ( '必须选择订座等级！' );
			}
			
			$userpwd = $data ['userpwd'];
			if (strlen ( $userpwd ) != 32) {
				$data ['userpwd'] = md5 ( $userpwd );
			}
			
			$data = $db->create ( $data );
			
			if ($data) {
				if ($db->save ( $data ) !== false) {
					$this->success ( "编辑订座成功！" );
				} else {
					$this->error ( '编辑订座失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			
			
			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$list = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $list ); 
			
			$db = M ( "seat" )->find ( $id );
			$this->assign ( "db", $db );
			$this->display ();
		}
	}
	
	// 删除订座
	public function deleteSeat($id) { 
		$db = M ( "seat" )->delete ( $id );
		if ($db !== false) { 
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
		 
	}
}
?>