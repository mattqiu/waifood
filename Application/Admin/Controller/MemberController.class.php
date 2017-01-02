<?php

namespace Admin\Controller;

use Admin\Model\OrderModel;
use Common\Model\AddressModel;
use Common\Model\CodeModel;
use Common\Model\DiscountModel;
use Common\Model\UserModel;

class MemberController extends BaseController {
	public function index() {
	}
	
	/**
	 * 收支管理
	 */
	public function balance($type = null, $status = null) {
		$username = null;
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		
		switch ($searchtype) {
			case '0' :
				$username = $keyword;
				break;
		}
		
		if (is_numeric ( $status )) {
			$where ['status'] = $status;
		}
		
		if (is_numeric ( $type )) {
			$where ['balancetype'] = $type;
		}
		
		if (! isN ( $username )) {
			$where ['username'] = array (
					'eq',
					$username 
			);
		}
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "balance" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
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
		$this->assign ( "type", $type );
		$this->assign ( "searchtype", $searchtype );
		
		$this->display ();
	}
	
	// 添加收支
	public function addBalance($pid = 0) {
		if (IS_POST) {
			$db = D ( "balance" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			
			if ($data) {
				// 1. 取收支类型，取userid,取之前的余额
				$uid = get_useridbyname ( $data ['username'] );
				if (is_numeric ( $uid ) && $uid != 0) {
					$amount = $data ['amount']; // 金额
					if ($amount != 0) {
						$db = $this->insertBalance ( $uid, $data ['balancetypeid'], $amount, $data ['remark'] );
						if ($db) {
							$this->success ( "添加收支成功！" );
						} else {
							$this->error ( '添加收支失败！' );
						}
					} else {
						$this->error ( '添加收支失败！' );
					}
				} else {
					$this->error ( '用户名不存在！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "balance" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			
			// 输出当前balance列表
			$list = M ( "balancetype" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
			
			$this->display ('addBalance');
		}
	}
	
	// 编辑收支
	public function editBalance($id = 0) {
		if (IS_POST) {
			$data = I ( 'remark' );
			$db = M ( 'balance' )->where ( 'id=' . $id )->setField ( 'remark', $data );
			if ($db !== false) {
				$this->success ( "编辑收支成功！" );
			} else {
				$this->error ( '编辑收支失败' );
			}
		} else {
			
			$db = M ( "balance" )->find ( $id );
			$this->assign ( "db", $db );
			
			// 输出当前balance列表
			$list = M ( "balancetype" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
			
			$this->display ('editBalance');
		}
	}
	
	// 删除收支
	public function deleteBalance($id) {
		$db = M ( "balance" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	// 收支操作
	public function insertBalance($uid, $balancetypeid, $amount, $remark) {
		$type = M ( 'balancetype' )->getFieldByid ( $balancetypeid, 'type' ); // 之前余额
		$prebalance = M ( 'member' )->getFieldByid ( $uid, 'balance' ); // 之前余额
		if (isN ( $prebalance )) {
			$prebalance = 0;
		}
		
		$data ['userid'] = $uid;
		$data ['username'] = get_username ( $uid );
		$data ['amount'] = $amount;
		$data ['prebalance'] = $prebalance;
		$data ['balancetypeid'] = $balancetypeid;
		if ($type == 1) {
			$data ['balance'] = $prebalance + $amount;
			$data ['balancetype'] = '1';
		} else {
			$data ['balance'] = $prebalance - $amount;
			$data ['balancetype'] = '0';
		}
		$data ['addip'] = get_client_ip ();
		$data ['remark'] = $remark;
		
		$db = M ( "balance" );
		if ($db->add ( $data )) {
			
			// 2. 更新当前余额
			M ( 'member' )->where ( 'id=' . $uid )->setField ( 'balance', $data ['balance'] );
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 收支类型管理
	 */
	public function balancetype() {
		$list = M ( "balancetype" )->order ( 'sortpath asc' )->select ();
		$this->assign ( "list", $list );
		$this->display ();
	}
	
	// 添加收支类型
	public function addBalancetype($pid = 0) {
		if (IS_POST) {
			$db = D ( "balancetype" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			// depth,sortpath
			$info = M ( 'balancetype' )->getById ( $pid );
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($data) {
				if ($db->add ( $data )) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'balancetype' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ',' 
					) );
					$this->success ( "添加收支类型成功！" );
				} else {
					$this->error ( '添加收支类型失败！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "balancetype" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->display ('addBalancetype');
		}
	}
	
	// 编辑收支类型
	public function editBalancetype($id = 0) {
		if (IS_POST) {
			$db = D ( "balancetype" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			
			// 上级收支类型不能是自己
			if ($data ['pid'] == $id) {
				$this->error ( '上级不能是自己！' );
			}
			
			// depth,sortpath
			$info = M ( 'balancetype' )->getById ( $data ['pid'] );
			// 有下级收支类型不能改变自己的上级
			if ($data ['pid'] !== $info ['pid']) {
				if ($data ['pid'] != '0') {
					$find = $db->where ( 'pid=' . $id )->count ();
					if ($find > 0) {
						$this->error ( '有下级收支类型时不能是改变自己的上级！' );
					}
				}
			}
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($data) {
				if ($db->save ( $data ) !== false) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'balancetype' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ',' 
					) );
					$this->success ( "编辑收支类型成功！" );
				} else {
					$this->error ( '编辑收支类型失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			
			$db = M ( "balancetype" )->find ( $id );
			$this->assign ( "db", $db );
			
			$this->display ('editBalancetype');
		}
	}
	
	// 删除收支类型
	public function deleteBalancetype($id) {
		$db = M ( "balancetype" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * 积分管理
	 */
	public function credit($type = null, $status = null) {
		$username = null;
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		
		switch ($searchtype) {
			case '0' :
				$username = $keyword;
				break;
		}
		
		if (is_numeric ( $status )) {
			$where ['status'] = $status;
		}
		
		if (is_numeric ( $type )) {
			$where ['credittype'] = $type;
		}
		
		if (! isN ( $username )) {
			$where ['username'] = array (
					'eq',
					$username 
			);
		}
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "credit" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
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
		$this->assign ( "type", $type );
		$this->assign ( "searchtype", $searchtype );
		
		$this->display ();
	}
	
	// 添加积分
	public function addCredit($pid = 0) {
		if (IS_POST) {
			$db = D ( "credit" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			
			if ($data) {
				// 1. 取积分类型，取userid,取之前的余额
				$uid = get_useridbyname ( $data ['username'] );
				if (is_numeric ( $uid ) && $uid != 0) {
					$amount = $data ['amount']; // 金额
					if ($amount != 0) { 
						$db = $this->insertCredit ( $uid, $data ['credittypeid'], $amount, $data ['remark'] );
						if ($db) {
							$this->success ( "添加积分成功！" );
						} else {
							$this->error ( '添加积分失败！' );
						}
					} else {
						$this->error ( '添加积分失败！' );
					}
				} else {
					$this->error ( '用户名不存在！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "credit" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			
			// 输出当前credit列表
			$list = M ( "credittype" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
			
			$this->display ('addCredit');
		}
	}
	
	// 编辑积分
	public function editCredit($id = 0) {
		if (IS_POST) {
			$data = I ( 'remark' );
			$db = M ( 'credit' )->where ( 'id=' . $id )->setField ( 'remark', $data );
			if ($db !== false) {
				$this->success ( "编辑积分成功！" );
			} else {
				$this->error ( '编辑积分失败' );
			}
		} else {
			
			$db = M ( "credit" )->find ( $id );
			$this->assign ( "db", $db );
			
			// 输出当前credit列表
			$list = M ( "credittype" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
			
			$this->display ('editCredit');
		}
	}
	
	// 删除积分
	public function deleteCredit($id) {
		$db = M ( "credit" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	// 收支操作
	public function insertCredit($uid, $balancetypeid, $amount, $remark) {
		$type = M ( 'credittype' )->getFieldByid ( $balancetypeid, 'type' ); // 收支类型
		$precredit = M ( 'member' )->getFieldByid ( $uid, 'credit' ); // 之前余额
		if (isN ( $precredit )) {
			$precredit = 0;
		} 
		$data ['userid'] = $uid;
		$data ['credittypeid'] = $balancetypeid;
		$data ['username'] = get_username ( $uid );
		$data ['precredit'] = $precredit;
		$data ['amount'] = $amount; 
		if ($type == 1) {
			$data ['credit'] = $precredit + $amount;
			$data ['credittype'] = '1';
		} else {
			$data ['credit'] = $precredit - $amount;
			$data ['credittype'] = '0';
		}
		$data ['addip'] = get_client_ip (); 
		$data ['remark'] = $remark;
		
		$db = M ( "credit" );
		if ($db->add ( $data )) {
			
			// 2. 更新当前积分
			M ( 'member' )->where ( 'id=' . $uid )->setField ( 'credit', $data ['credit'] );
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 积分类型管理
	 */
	public function credittype() {
		$list = M ( "credittype" )->order ( 'sortpath asc' )->select ();
		$this->assign ( "list", $list );
		$this->display ();
	}
	
	// 添加积分类型
	public function addCredittype($pid = 0) {
		if (IS_POST) {
			$db = D ( "credittype" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			// depth,sortpath
			$info = M ( 'credittype' )->getById ( $pid );
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($data) {
				if ($db->add ( $data )) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'credittype' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ',' 
					) );
					$this->success ( "添加积分类型成功！" );
				} else {
					$this->error ( '添加积分类型失败！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "credittype" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->display ('addCredittype');
		}
	}
	
	// 编辑积分类型
	public function editCredittype($id = 0) {
		if (IS_POST) {
			$db = D ( "credittype" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			
			// 上级积分类型不能是自己
			if ($data ['pid'] == $id) {
				$this->error ( '上级不能是自己！' );
			}
			
			// depth,sortpath
			$info = M ( 'credittype' )->getById ( $data ['pid'] );
			// 有下级积分类型不能改变自己的上级
			if ($data ['pid'] !== $info ['pid']) {
				if ($data ['pid'] != '0') {
					$find = $db->where ( 'pid=' . $id )->count ();
					if ($find > 0) {
						$this->error ( '有下级积分类型时不能是改变自己的上级！' );
					}
				}
			}
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($data) {
				if ($db->save ( $data ) !== false) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'credittype' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ',' 
					) );
					$this->success ( "编辑积分类型成功！" );
				} else {
					$this->error ( '编辑积分类型失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			
			$db = M ( "credittype" )->find ( $id );
			$this->assign ( "db", $db );
			
			$this->display ('editCredittype');
		}
	}
	
	// 删除积分类型
	public function deleteCredittype($id) {
		$db = M ( "credittype" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * 会员列表，分页
	 */
	public function member() {
		$usereal = null;
		$username = null;
		$telephone = null;
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
		$status = I ( 'status' );
		
		switch ($searchtype) {
			case '1' :
				$usereal = $keyword;
				break;
			case '0' :
				$username = $keyword;
				break;
			case '2' :
				$telephone = $keyword;
				break;
			case '3' :
				$email = $keyword;
				break;
			case '4' :
				$usertype = $keyword;
				$this->assign('level',$usertype);
				break;
			case '5' :
				$weixin = $keyword; 
				break;
		}
		
		if (is_numeric ( $status )) {
			$where ['status'] = $status;
		}
		
		if (! isN ( $usereal )) {
			$where ['userreal'] = array (
					'like',
					'%' . $usereal . '%' 
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
		if (! isN ( $email )) {
			$where ['email'] = array (
					'like',
					'%' . $email . '%' 
			);
		}
		if (! isN ( $usertype )) {
			$where ['usertype'] = $usertype;
		}
		if (! isN ( $weixin )) {
			$where ['weixin'] = array (
					'like',
					'%' . $weixin . '%' 
			);
		}
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		 
		$rs = M ( "member" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
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

		// 输出当前Member等级列表
		$levels = M ( "level" )->where ( 'status=1' )->order ( 'id desc' )->select ();
		$this->assign ( "levels", $levels );
			
		$this->display ();
	}
	
	// 添加会员
	public function addMember($pid = 0) {
		if (IS_POST) {
			$db = D ( "member" );
			$data = empty ( $data ) ? $_POST : $data;
			if ($data ['usertype'] == '0') {
				$this->error ( '必须选择会员等级！' );
			}
			$data ['addip'] = get_client_ip ();
			$data ['userpwd'] = md5 ( $data ['userpwd'] );
			
			$data = $db->create ( $data );
			
			if ($data) {
				if ($db->add ( $data ) !== false) {
					$this->success ( "添加会员成功！" );
				} else {
					$this->error ( '添加会员失败！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "member" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			
			// 输出当前Member等级列表
			$list = M ( "level" )->where ( 'status=1' )->order ( 'id desc' )->select ();
			$this->assign ( "list", $list );
			
			$this->display ('addMember');
		}
	}

    /**
     * 编辑用户
     */
    public function modifyUserData(){
        $data = $_POST;
        $userpwd = $data ['userpwd'];
        if ($userpwd && strlen ( $userpwd ) != 32) {
            $data ['userpwd'] = md5 ( $userpwd );
        }
        if(UserModel::checkEmailToUp($data['email'],$data['id'])){
            apiReturn(CodeModel::ERROR,'邮箱已存在');
        }
        if(UserModel::checkUsernameToUp($data['username'],$data['id'])){
            apiReturn(CodeModel::ERROR,'用户名已存在');
        }
        if ($data) {
            if (UserModel::modifyMember($data['id'],$data) !== false) {
                apiReturn(CodeModel::CORRECT, "编辑会员成功！" );
            } else {
                apiReturn(CodeModel::ERROR, "编辑会员失败！" );
            }
        }else{
            apiReturn(CodeModel::ERROR);
        }
    }

    //编辑用户地址
    public function modifyAddr(){
        $data = $_POST;
        if (!$data['username']) {
            apiReturn(CodeModel::CORRECT, "收件人姓名不能为空！" );
        }
        if (!$data['telephone']) {
            apiReturn(CodeModel::CORRECT, "收件人电话不能为空！" );
        }
        if (!$data['address']) {
            apiReturn(CodeModel::CORRECT, "收件地址不能为空！" );
        }
        if ($data) {
            if (AddressModel::modifyAddr($data['id'],$data) !== false) {
                apiReturn(CodeModel::CORRECT, "编辑用户的地址成功！" );
            } else {
                apiReturn(CodeModel::CORRECT, "编辑用户的地址失败！" );
            }
        }
    }

	// 编辑会员
	public function editMember() {
		$id = I ( 'id' );
		if (IS_POST) {
			$db = D ( "member" );
			$data = empty ( $data ) ? $_POST : $data;
			if ($data ['usertype'] == '0') {
				$this->error ( '必须选择会员等级！' );
			}
			$userpwd = $data ['userpwd'];
			if (strlen ( $userpwd ) != 32) {
				$data ['userpwd'] = md5 ( $userpwd );
			}
			if ($data) {
				if (UserModel::modifyMember($data['id'],$data) !== false) {
					$this->success ( "编辑会员成功！" );
				} else {
					$this->error ( '编辑会员失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			// 输出当前Member等级列表
			$list = M ( "level" )->where ( 'status=1' )->order ( 'id desc' )->select ();
			$this->assign ( "level", $list );
			$addresslist = AddressModel::getUserAddress($id);
			$this->assign ( "addresslist", $addresslist );
            $user = UserModel::getUserById($id);
            $order = OrderModel::getOrderByUserId($id);
            $discount = DiscountModel::getDiscountByType(DiscountModel::USER_GROUPS);
            $this->assign ( "discount", $discount);
            $this->assign ( "order_time", $order['addtime'] );
            $this->assign ( "info", $user );
            $this->assign ( "login_key",  C('USER_LOGIN_KEY') );
            $this->display ('editMember');
		}
	}

    public function loginToUser(){
        $userid =  I('post.userid');
        $toadmin =  I('post.toadmin');
        if(trim($toadmin) !== C('USER_LOGIN_KEY')){
            apiReturn(CodeModel::ERROR,'权限不足,请联系超级管理员');
        }
        if(regex($userid,'number')){
            if(UserModel::loginByAdmin($userid)){
                apiReturn(CodeModel::CORRECT,'','/shop/member/index.html');
            }else{
                apiReturn(CodeModel::ERROR,'登录失败');
            }
        }
    }


	// 删除会员
	public function deleteMember($id) {
		$db = M ( "member" )->delete ( $id );
		if ($db !== false) { 
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * 等级管理
	 */
	public function level() {
		$list = M ( "level" )->order ( 'sortpath asc' )->select ();
		$this->assign ( "list", $list );
		$this->display ();
	}
	
	// 添加等级
	public function addLevel($pid = 0) {
		if (IS_POST) {
			$db = D ( "level" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			// depth,sortpath
			$info = M ( 'level' )->getById ( $pid );
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($data) {
				if ($db->add ( $data )) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'level' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ',' 
					) );
					$this->success ( "添加等级成功！" );
				} else {
					$this->error ( '添加等级失败！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "level" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid );
			
			// 输出当前Level列表
			$list = M ( "level" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
			
			// 取父级绑定的模型
			$model_id = M ( 'level' )->getFieldById ( $pid, 'model_id' );
			$this->display ('addLevel');
		}
	}
	
	// 编辑等级
	public function editLevel($id = 0) {
		if (IS_POST) {
			$db = D ( "level" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			
			// 上级等级不能是自己
			if ($data ['pid'] == $id) {
				$this->error ( '上级不能是自己！' );
			}
			
			// depth,sortpath
			$info = M ( 'level' )->getById ( $data ['pid'] );
			// 有下级等级不能改变自己的上级
			if ($data ['pid'] !== $info ['pid']) {
				if ($data ['pid'] != '0') {
					$find = $db->where ( 'pid=' . $id )->count ();
					if ($find > 0) {
						$this->error ( '有下级等级时不能是改变自己的上级！' );
					}
				}
			}
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($data) {
				if ($db->save ( $data ) !== false) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'level' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ',' 
					) );
					$this->success ( "编辑等级成功！" );
				} else {
					$this->error ( '编辑等级失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			
			$db = M ( "level" )->find ( $id );
			$this->assign ( "db", $db );
			
			// 输出当前Level列表
			$list = M ( "level" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
			
			$this->display ('editLevel');
		}
	}
	
	// 删除等级
	public function deleteLevel($id) {
		$db = M ( "level" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
}
?>