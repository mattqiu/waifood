<?php

namespace Admin\Controller;

class OrderpointController extends BaseController {
	public function index() {
	}
	
	/**
	 * 订单列表，分页
	 */

	public function orderpoint() {
		$orderno = null;
		$username = null;
		$telephone = null;
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		$status = I ( 'status' );
		$pay = I ( 'pay' );
		
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
		if (is_numeric ( $pay )) {
			$where ['pay'] = $pay;
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
		//$where['shop_id']=get_role('shop');
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "Orderpoint" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
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
		$this->assign ( "pay", $pay );
		$this->assign ( "searchtype", $searchtype );
		
		$this->display ();
	}
	
	// 添加订单
	public function addOrderpoint($pid = 0) {
		if (IS_POST) {
			$db = D ( "orderpoint" );
			$data = empty ( $data ) ? $_POST : $data;
			$data ['orderno'] = get_order_no ();
			$data ['addip'] = get_client_ip ();
			
			if (false !== $db->add ( $data )) {
				$this->success ( "添加订单成功！" );
			} else {
				$this->error ( '添加订单失败！' );
			}
		} else {
			$sort = M ( "orderpoint" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid );
			 
			
			$this->display ();
		}
	}
	
	// 编辑订单
	public function editOrderpoint() {
		$id = I ( 'id' );
		if (IS_POST) {
			$db = D ( "orderpoint" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			
			if ($data) { 
				if ($db->save ( $data ) !== false) { 
					$this->success ( "编辑订单成功！" );
				} else {
					$this->error ( '编辑订单失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			
			$db = M ( "orderpoint" )->find ( $id );
			$this->assign ( "db", $db );
			 
			$this->display ();
		}
	}
	
	// 删除订单
	public function deleteOrderpoint() {
		$id = I ( 'id' );
		$orderno = M ( "orderpoint" )->getFieldbyid ( $id, 'orderno' );
		
		if (! isN ( $orderno )) {
			$db = M ( "orderpoint" )->where ( 'status<>3' )->delete ( $id );
			if ($db !== false) { 
				$this->success ( "删除成功！" );
			} else {
				$this->error ( "删除失败" );
			}
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * 修改订单详情
	 *
	 * @param number $type
	 *        	0:添加条目
	 *        	1-修改条目
	 *        	2-删除条目
	 *        	3-删除订单
	 *        	4-取消订单
	 * @param number $detail        	
	 */
	public function editOrderDetail($type = null, $detail = null) {
		$arr = str2arr ( $detail . '||', '|' );
		if (isN ( $arr [0] ) || isN ( $arr [1] ) || isN ( $arr [2] )) {
			echo ('参数错误！');
			exit ();
		}
		
		switch ($type) {
			case 0 :
				// 添加条目:
				// 1. 取产品信息
				$db = M ( 'content' )->field ( 'id,title,price,indexpic' )->find ( $arr [1] );
				// 2. 加入详单
				if ($db !== false) {
					$data ['orderno'] = $arr [0];
					$data ['productid'] = $db ['id'];
					$data ['productname'] = $db ['title'];
					$data ['indexpic'] = $db ['indexpic'];
					$data ['price'] = $db ['price'];
					$data ['num'] = $arr [2];
					
					$db = M ( 'order_detail' )->add ( $data );
					if ($db !== false) {
						
						// 3.更新主订单
						if ($this->updateOrder ( $arr [0] )) {
						} else {
							echo ('订单更新失败！');
							exit ();
						}
					} else {
						echo ('订单写入失败！');
						exit ();
					}
				} else {
					echo ('编号为' . $arr [1] . '的产品不存在！');
					exit ();
				}
				
				break;
			
			case 1 :
				// 修改条目:
				// 1. 取产品信息
				$db = M ( 'order_detail' )->where ( 'id=' . $arr [1] )->setField ( 'num', $arr [2] );
				
				if ($db !== false) {
					// 3.更新主订单
					if ($this->updateOrder ( $arr [0] )) {
					} else {
						echo ('订单更新失败！');
						exit ();
					}
				} else {
					echo ('订单更新失败！');
					exit ();
				}
				
				break;
			case 2 :
				// 删除条目
				$db = M ( 'order_detail' )->where ( 'id=' . $arr [1] )->delete ();
				
				if ($db !== false) {
					// 3.更新主订单
					if ($this->updateOrder ( $arr [0] )) {
					} else {
						echo ('订单更新失败！');
						exit ();
					}
				} else {
					echo ('产品删除失败！');
					exit ();
				}
				break;
			case 3 :
				// 删除订单
				$orderno = $arr [0];
				$where = array ();
				$where ['orderno'] = $orderno;
				$db = M ( "order" )->where ( $where )->delete ();
				if ($db !== false) {
					$db = M ( "order_detail" )->where ( $where )->delete ();
					if ($db !== false) {
					} else {
						echo ('订单删除失败！');
						exit ();
					}
				} else {
					echo ('订单删除失败！');
					exit ();
				}
				break;
			case 4 :
				// 取消订单
				$orderno = $arr [0];
				$where = array ();
				$where ['orderno'] = $orderno;
				$db = M ( "order" )->where ( $where )->setField ( 'status', 4 );
				if ($db !== false) {
					$db = M ( "order_detail" )->where ( $where )->setField ( 'status', 4 );
					if ($db !== false) {
					} else {
						echo ('子订单取消失败！');
						exit ();
					}
				} else {
					echo ('订单取消失败！');
					exit ();
				}
				break;
		}
	}
	public function updateOrder($orderno = null) {
		if (isN ( $orderno )) {
			return false;
		} else {
			
			// 更新实际金额
			// amount=amountall+shipfee-discount-creditamount
			$where = array ();
			$where ['orderno'] = $orderno;
			$num = M ( 'order_detail' )->where ( $where )->sum ( 'num' );
			$amount = M ( 'order_detail' )->where ( $where )->sum ( 'price*num' );
			$data ['num'] = $num;
			$data ['amountall'] = $amount;
			$db = M ( 'order' )->where ( $where )->save ( $data );
			if ($db !== false) {
				// M('order')->where($where)->setField('amount','amountall+shipfee-discount-creditamount');
				
				M ()->execute ( "update " . C ( 'DB_PREFIX' ) . "order set amount=amountall+shipfee-discount-creditamount where orderno='" . $orderno . "'" );
				return true;
			} else {
				return false;
			}
		}
	}
	/**
	 * 根据订单号列出订单产品
	 *
	 * @param string $orderno        	
	 */
	public function getOrderProducts($orderno = '') {
		$where = array ();
		$where ['orderno'] = $orderno;
		$db = M ( 'order_detail' )->field ( 'productid,productname,indexpic' )->where ( $where )->select ();
		$html = '';
		if ($db != null) {
			foreach ( $db as $pro ) {
				$html .= "<img class='thumbpic' src='{$pro['indexpic']}' alt='{$pro['productname']}' title='{$pro['productname']}' />";
			}
		}
		echo ($html);
	}
}
?>