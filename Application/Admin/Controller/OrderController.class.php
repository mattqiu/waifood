<?php

namespace Admin\Controller;

use Admin\Model\MemberMemoModel;
use Admin\Model\OrderModel;
use Common\Model\CodeModel;
use Common\Model\DiscountModel;
use Common\Model\UserModel;

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
		$status = I ( 'status' );
		$pay = I ( 'pay' );
		$orderfrom = I ( 'orderfrom' );
        $searchtype = I ( 'searchtype' );
        $keyword = I ( 'keyword' );
        if($searchtype && $keyword){
            switch ($searchtype) {
                case '1' : $where['orderno'] = array ( 'like','%' . $keyword . '%');break;
                case '2' : $where['id'] = array ( 'eq',$keyword );break;
                case '3' : $where['username'] = array ( 'like','%' . $keyword . '%');break;
                case '4' : $where['telephone'] = array ( 'like','%' . $keyword . '%');break;
                case '5' : $where['address'] = array ( 'like','%' . $keyword . '%');break;
                case '6' : $where['info'] = array ( 'like','%' . $keyword . '%');break;
            }
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
		
		$statuslist=C('config.CONFIG_STATUS_LIST');
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
                           apiReturn(CodeModel::ERROR,"未完事项修改失败！" );
                       }
                   }else{

                   }
                }else{
                    if(!$re= MemberMemoModel::addMemo($data['userid'],$data['memo_content'],$id)){
                        apiReturn(CodeModel::ERROR,"未完事项添加失败！" );
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
                $where ['orderno'] = $orderno;
				$order = M ( 'order' )->where ($where )->find ();
				$status=$order['status'];
// 				if ($status == '3' || $status == '4') {
// 					$this->error ( '该订单状态不可修改！' );
                //配送费有修改
               if(isset($data['shipfee']) && $order['shipfee'] != $data['shipfee']){
                   $amount = M ( 'order_detail' )->where ( $where )->sum ( 'price*num' );
                   $data ['amountall'] =float_fee($amount+floatval($data ['shipfee']));//订单总金额 = 商品总价+配送费
               }

				if ($status == '3' || $data ['status']  == '4' ) {
				    withdrawOrder($orderno);
				}
				if ($db->where($where)->save ( $data ) !== false) {
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
                                        apiReturn(CodeModel::CORRECT,"编辑订单成功！" );
									} else {
                                        apiReturn(CodeModel::ERROR,"编辑订单失败！" );
									}
								}	
								}
							} else {
								$this->updateOrderDetail($orderno,$data['status']);
                                apiReturn(CodeModel::CORRECT,"编辑订单成功！" );
							}
						}
						
						// TODO:取消订单需要的操作：返回余额？ 减积分？
						if ($data ['status'] == '4') {
                            \Common\Model\OrderModel::modifyStockAndSoldForOrder($orderno,\Common\Model\OrderModel::RET_STOCK);//退库存减销量
						}
					} else {
						$this->updateOrderDetail($orderno,$data['status']);
						//$this->updateOrder ( $data ['orderno'] );
					}
                    apiReturn(CodeModel::CORRECT,"编辑订单成功！" );
				} else {
                    apiReturn(CodeModel::ERROR,"编辑订单失败！" );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$db = M ( "order" )->find ( $id );
            $user = UserModel::getUserById($db['userid']) ;
            //获取用户最近一次下单
            $order = OrderModel::getOrderByUserId($id);
            $this->assign ( "order_time", $order['addtime'] );
			$this->assign ( "db", $db );
			$this->assign ( "user", $user );
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
						if ($this->updateOrder ($arr[0])) {
                            \Common\Model\OrderModel::modifyStockAndSoldForOrderdetailid($arr[0],$arr[1],$arr[2],\Common\Model\OrderModel::CAT_STOCK);//添加直接减库存，销量
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
                $detail = M ( 'order_detail' )->find($arr [1]);
                $num =  intval($detail['num']) - intval($arr[2]); //获取变动数量
                if($num>0){ //如果变动数量小于原始数量则返回变动差
                    $modifytype = \Common\Model\OrderModel::CAT_STOCK;
                }else{
                    $modifytype = \Common\Model\OrderModel::RET_STOCK;
                }
                $db = M ( 'order_detail' )->where ( 'id=' . $arr [1] )->setField ( 'num', $arr [2] );
				if ($db !== false) {
					// 3.更新主订单
					if ($this->updateOrder ( $arr [0] )) {
                        \Common\Model\OrderModel::modifyStockAndSoldForOrderdetailid($arr[0],$detail['productid'],$num,$modifytype);//库存，销量操作
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
                $detail = M ( 'order_detail' )->find($arr [1]);
                $num =  intval($detail['num']);
                //删除则直接返回该商品的库存，销量(需要提前执行)
                \Common\Model\OrderModel::modifyStockAndSoldForOrderdetailid($arr[0],$detail['productid'],$num,\Common\Model\OrderModel::RET_STOCK);
                $db = M ( 'order_detail' )->where ( 'id=' . $arr [1] )->delete ();
				if ($db !== false) {
					// 3.更新主订单
					if ($this->updateOrder ( $arr[0] )) {

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
                        \Common\Model\OrderModel::modifyStockAndSoldForOrder($orderno,\Common\Model\OrderModel::RET_STOCK);//退库存减销量
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
                        \Common\Model\OrderModel::modifyStockAndSoldForOrder($orderno,\Common\Model\OrderModel::RET_STOCK);//退库存减销量
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
	public function updateOrder($orderno) {
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
            //需要重新计算的有：订单总金额，配送费，应付金额，折扣优惠
            $discount = DiscountModel::getDiscountMoney($amount,$db['userid']);
            $data ['amountall'] =$amount+getShipfee($amount);//订单总金额 = 商品总价+配送费
            $data ['shipfee'] = getShipfee($amount); //配送费
            $data ['amount'] =float_fee(($amount-$discount['money'])+getShipfee($amount));//实际支付总金额=商品总价-折扣+配送费
            $data ['discount'] =float_fee($discount['money']);

			if(isN($time)||$time=='0000-00-00 00:00:00'){
				$data ['time'.$status] = time_format();
			} 
			$data ['info'.$status] = $info;
			$db = M ( 'order' )->where ( $where )->save ( $data );
			if ($db !== false) {
			    $data=array();
			    $data['status']=$status;
			    M ( 'order_detail' )->where ( $where )->save ( $data );
              //  \Common\Model\OrderModel::modifyStockAndSoldForOrder($orderno);//减去库存
//				M ()->execute ( "update " . C ( 'DB_PREFIX' ) . "order set amount=amountall+shipfee-discount-creditamount-couponamount where orderno='" . $orderno . "'" );
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