<?php

namespace Admin\Controller;

use Admin\Model\MemberMemoModel;
use Common\Model\CodeModel;

class OrderController extends BaseController {
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
		$pay = I ( 'pay' );
		$orderfrom = I ( 'orderfrom' );
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
		if (is_numeric ( $orderfrom )) {
			$where ['orderfrom'] = $orderfrom;
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
		
		$rs = M ( "order" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
		$list = $rs->select ();
        foreach($list as &$val){
            if(regex($val['userid'],'number')){
                $memo = MemberMemoModel::getMemo($val['userid']);
                if($memo){
                    $val['memo'] ='未完事项:'. $memo['content'];
                }
            }
        }
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( 'page', $page->show () );
		}
		
		$statuslist=parse_field_attr(C('config.CONFIG_STATUS_LIST'));
		$this->assign("statuslist",$statuslist);
		$this->assign ( "keyword", $keyword );
		$this->assign ( "status", $status );
		$this->assign ( "pay", $pay );
		$this->assign ( "orderfrom", $orderfrom );
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
			$sort = M ( "order" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid );
			
			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$list = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $list );
			
			// 输出当前Order列表
			$list = M ( "order" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
			
			$this->display ('addOrder');
		}
	}
	
	// 编辑订单
	public function editOrder() {
		$id = I ( 'id' );
		$act = I ( 'act' );
		$act1 = I ( 'act1' );
		if (IS_POST) {
            $data = empty ( $data ) ? $_POST : $data;
            if(isset($data['memo_content']) && !empty($data['memo_content'])){
                $memo_mid = $data['memo_mid'];
                if(regex($memo_mid,'number')){ //修改备注
                    $key = 'MEMO:USER:ID:'.$data['userid'];
                   if(md5(trim($data['memo_content'])) != S($key)){//备忘录被修改
                       if(!$re= MemberMemoModel::modifyMemo($memo_mid,$data['memo_content'])){
                           $this->error ( '未完事项修改失败' );
                       }
                   }else{

                   }
                }else{
                    if(!$re= MemberMemoModel::addMemo($data['userid'],$data['memo_content'])){
                        $this->error ( '未完事项添加失败' );
                    }
                }
            }
            unset($data['memo_mid']);
            unset($data['memo_content']);
            $db = D ( "order" );
			$data = $db->create ( $data );
			if ($data) {
				$orderno = $data ['orderno'];
				// 如果当前订单是已完成或取消，则不允许修改
				$order = M ( 'order' )->where ( 'orderno=' . $orderno )->find ();
				$status=$order['status'];
// 				if ($status == '3' || $status == '4') {
// 					$this->error ( '该订单状态不可修改！' );
// 				}

				if ($status == '3' || $data ['status']  == '4' ) {
				    withdrawOrder($orderno);
				} 
				
				if ($db->save ( $data ) !== false) {
					if ($data ['status'] == '3' ) {
					//if ($data ['status'] == '3' || $data ['status'] == '4') {
						// status:3 已完成，需要加积分
						// status:4 已取消，不做更新操作
						
						if ($data ['status'] == '3') {
						    finishOrder($orderno);
							// 增加积分
							$creditrate = (C ( 'config.CREDIT_RATE' ) ? C ( 'config.CREDIT_RATE' ) : 0);
							$creditrate = (is_numeric ( $creditrate ) ? $creditrate : 0);
							
							if ($creditrate != 0) {
								$where=array();
								$where['credittypeid']=1;
								$where['remark']=$orderno;
								$where['userid']=$order ['userid'];
								$creditAdded=M('credit')->where($where)->find();
								if(!$creditAdded){
								
								$where = array ();
								$where ['orderno'] = $orderno;
								$db = M ( 'order' )->field ( 'userid,amount' )->where ( $where )->find ();
								$uid = $db ['userid'];
								$amount = $db ['amount']*$creditrate;
								if (is_numeric ( $uid ) && $uid != 0) {
									$ctrl = A ( 'Member' );
									// 参数1：订单消费
									$ret = $ctrl->insertCredit ( $uid, 1, $amount, $orderno );
									if ($ret) {
										$this->updateOrderDetail($orderno,$data['status']);
										$this->success ( "编辑订单成功！" );
										exit ();
									} else {
										$this->error ( '编辑订单失败' );
										exit ();
									}
								}	
								}
							} else {
								$this->updateOrderDetail($orderno,$data['status']);
								$this->success ( "编辑订单成功！" );
								exit ();
							}
						}
						
						// TODO:取消订单需要的操作：返回余额？ 减积分？
						if ($data ['status'] == '4') {
						}
					} else {
						$this->updateOrderDetail($orderno,$data['status']);
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
			$db = M ( "order" )->find ( $id );
			$this->assign ( "db", $db );
            //dump($db);exit;
			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$shoplist = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $shoplist );
			
			// 输出当前Order列表
			$where = array ();
			$where ['orderno'] = $db ['orderno'];
			$list = M ( "order_detail" )->where ( $where )->order ( 'id asc' )->select ();
			$this->assign ( "list", $list );
			$isShop=false;
			$isService=false;
			foreach ($list as $k=>$v){
			    if(strpos($v['sortpath'],',2,')){
			        $isShop=true;
			    }
			    if(strpos($v['sortpath'],',3,')){
			        $isService=true;
			    }
			}
			$this->assign ( 'isService', $isService );
			$this->assign ( 'isShop', $isShop );
			$this->assign ( 'memo',  MemberMemoModel::getMemo($db['userid']) );
			if($act=='print'){
				if($act1=="1"){
					$this->display('Cms/printOrder1');
				}else{
					$this->display('Cms/printOrder');
				}
			}else{
			$this->display ('editOrder');
			}
		}
	}
	
	// 删除订单
	public function deleteOrder() {
		$id = I ( 'id' );
		$orderno = M ( "order" )->getFieldbyid ( $id, 'orderno' );
		
		if (! isN ( $orderno )) {
			$db = M ( "order" )->where ( 'status<>3' )->delete ( $id );
			if ($db !== false) {
				$where = array ();
				$where ['orderno'] = $orderno;
				$db = M ( "order_detail" )->where ( $where )->delete ();
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
				$db = M ( 'content' )->field ( 'id,title,price,indexpic,sortpath,unit,namecn,supplyid,supplyname' )->find ( $arr [1] );
				// 2. 加入详单
				if ($db) {
					$data ['orderno'] = $arr [0];
					$data ['productid'] = $db ['id'];
					$data ['productname'] = $db ['title'];
					$data ['indexpic'] = $db ['indexpic'];
					$data ['price'] = $db ['price'];
					$data ['sortpath'] = $db ['sortpath'];
					$data ['unit'] = $db ['unit'];
					$data ['namecn'] = $db ['namecn'];
					$data ['supplyid'] = $db ['supplyid'];
					$data ['supplyname'] = $db ['supplyname'];
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
			$db= M ( 'order' )->where ( $where )->find();
			if($db){
				$status=$db['status']; 
				$time=$db['time'.$status]; 
				$info=$db['info'.$status]; 
			}
			
			$num = M ( 'order_detail' )->where ( $where )->sum ( 'num' );
			$amount = M ( 'order_detail' )->where ( $where )->sum ( 'price*num' );
			$data ['num'] = $num;
			$data ['amountall'] = $amount;
			if(isN($time)||$time=='0000-00-00 00:00:00'){
				$data ['time'.$status] = time_format();
			} 
			$data ['info'.$status] = $info;
			
			$db = M ( 'order' )->where ( $where )->save ( $data );
			if ($db !== false) {
			    $data=array();
			    $data['status']=$status;
			    $db = M ( 'order_detail' )->where ( $where )->save ( $data );
				 
				M ()->execute ( "update " . C ( 'DB_PREFIX' ) . "order set amount=amountall+shipfee-discount-creditamount-couponamount where orderno='" . $orderno . "'" );
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * 更新详单状态
	 * @param string $orderno
	 * @param number $status
	 */
	private function updateOrderDetail($orderno=null,$status=0){
		M ( 'order_detail' )->where ( 'orderno=' . $orderno )->setField('status',$status); 
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

    public function finishMemo(){
        $mid = I('post.m_id');
        if(regex($mid,'number')){
            if(MemberMemoModel::finishMemo($mid)){
                apiReturn(CodeModel::CORRECT,'修改成功');
            }else{
                apiReturn(CodeModel::ERROR,'修改失败');
            }
        }
    }
	
}
?>