<?php
namespace Admin\Controller;

class OrderCleaningController extends BaseController
{
	public function index()
	{
		$orderno = null;
		$username = null;
		$telephone = null;
		$where = null;
		$keyword = I ( 'keyword' );
		$status = I ( 'status', null, 'int' );
		$paid = I ( 'paid', null, 'int' );
		$payment = I ( 'payment', null, 'int' );
		$params = array();

		if( $keyword ){
			//if( is_numeric($keyword) && strpos($keyword, '13') )
			$where['no.orderno'] = array('like', '%'.$keyword.'%');
			$params['keyword'] = $keyword;
		}
		
		if (is_numeric ( $status )) {
			$params['status'] = $where ['no.status'] = $status;
		}
		if (is_numeric ( $payment )) {
			$params['payment'] = $where ['no.payment'] = $payment;
		}
		if (is_numeric ( $paid )) {
			$params['paid'] = $where ['no.paid'] = $paid;
		}
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p>0 ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( 'Ordernew' )->alias('no')->field('no.*, c.*')
			->join('__ORDER_CLEANING__ c ON c.orderno=no.orderno')->where ( $where )->order ( 'no.id desc' )->page ( $p, $row );
		$rs1 = clone $rs;
		$count = $rs1->count ();
		unset($rs1);
		$list = $rs->select ();
		
		$this->assign ( "list", $list );
		
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( 'page', $page->show () );
		}

		$this->assign ( 'p', $params );
		$this->display ('OrderCleaning/index');
	}
	
	// 编辑订单
	public function editOrder()
	{
		$orderno = I ( 'orderno' );		
		$db = M ('Ordernew')->alias('o')->field('o.*, oc.*, oc.id as cleaning_id')
			->join('__ORDER_CLEANING__ oc ON oc.orderno=o.orderno')->where ( array('o.orderno'=>$orderno) )->find();
		$this->assign ( "db", $db );
		
		//详细
		$cleaningRecord = M('CleaningRecord')->where(array('cleaning_id'=>$db['cleaning_id']))->select();
		$this->assign('cleaningRecord', $cleaningRecord);
		$this->display ('editOrder');
	}
	
	// 删除订单
	public function removeOrder()
	{
		$orderno = I('orderno');
		$order = M ('Ordernew')->field('id,status')->where(array('orderno'=>$orderno))->find();
		if( !$order ){
			$this->error ( "删除失败" );
		}
		if( $order['status'] > 0 ){
			$this->error ( '不允许删除' );
		}
		
		$re = M ('Ordernew')->delete ($order['id']);
		if( !$re ){
			$this->error ( "删除失败" );
		}
		$this->success ( "删除成功！" );
	}
	
	//更新订单基本信息
	public function submitOrder()
	{
		$orderno = I('orderno');
		$data = array(
			'payment'		=> I('payment'),
			'paid' 			=> I('paid'),
			'status'		=> I('status'),
			'modify_amount'	=> I('modify_amount', 0, 'floatval'),
		);
		$order = M('Ordernew')->field('amount')->where(array('orderno'=>$orderno))->find();
		if( !$order ){
			$this->error ( "操作失败" );
		}
		if( $order['amount'] + $data['modify_amount'] < 0 ){
			$this->error ( "操作失败" );
		}
		$re = M('Ordernew')->where(array('orderno'=>$orderno))->save($data);
		if( !$re ){
			$this->error ( "操作失败");
		}
		$this->success ( "操作成功！" );
	}
	
	//更新订购信息
	public function submitCleaning()
	{
		$orderno = I('orderno');
		$data = array(
				'type'		=> I('type') == '2' ? 2 : 1,
				'start_time'=> I('start_time'),
				'remark'	=> I('remark'),
				'hours'		=> max(I('hours', 1, 'int'), 1),
		);
		if( $data['type'] == 1 ){
			$data['total_hour'] = $data['hours'];
		}else{
			$data['month_num'] = max(I('month_num', 1, 'int'), 1);
			$data['month_times'] = max(I('month_times', 1, 'int'), 1);
			$data['total_hour'] = $data['month_num'] * $data['month_times'] * $data['hours'];
		}
		$re = M('OrderCleaning')->where(array('orderno'=>$orderno))->save($data);
		if( !$re ){
			$this->error ( "操作失败" );
		}
		$this->success ( "操作成功！" );
	}
	
	//更新联系信息
	public function submitContact()
	{
		$orderno = I('orderno');
		$data = array(
			'uname'		=> I('uname'),
			'phone'		=> I('phone'),
			'email' 	=> I('email'),
			'address'	=> I('address'),
		);
		
		$re = M('OrderCleaning')->where(array('orderno'=>$orderno))->save($data);
		if( !$re ){
			$this->error ( "操作1失败" );
		}
		$this->success ( "操作成功！" );
	}
	
	//添加记录
	public function submitAddRecord()
	{
		$tranDb = new \Think\Model();
		$tranDb->startTrans();
		
		$data = array(
				'cleaning_id'	=> I('cleaning_id'),
				'cleaner'		=> I('cleaner'),
				'start_time'	=> I('start_time'),
				'clean_hour'	=> max(I('clean_hour', 1, 'int'), 1),
		);
		$re = $tranDb->table('__CLEANING_RECORD__')->add($data);
		if( !$re ){
			$tranDb->rollback();
			$this->error ( "操作失败2".M()->getLastSql() );
		}
		
		$re = $tranDb->table('__ORDER_CLEANING__')->where(array('id'=>$data['cleaning_id']))->save(array('used_hour'=>array('exp', '`used_hour` + '.$data['clean_hour'])));
		if( !$re ){
			$tranDb->rollback();
			$this->error ( "操作失败3" );
		}
		
		$tranDb->commit();
		$this->success ( "操作成功！" );
	}
	
	//删除记录
	public function removeRecord()
	{
		$id = I('id');
		$record = M('CleaningRecord')->find($id);
		if( !$record ){
			echo 0;
			return;
		}
		
		$tranDb = new \Think\Model();
		$tranDb->startTrans();
		$re = $tranDb->table('__CLEANING_RECORD__')->delete($id);
		if( !$re ){
			$tranDb->rollback();
			$this->error ( "操作失败" );
		}
		
		$re = $tranDb->table('__ORDER_CLEANING__')->where(array('id'=>$record['cleaning_id']))->save(array('used_hour'=>array('exp', '`used_hour` - '.$record['clean_hour'])));
		if( !$re ){
			$tranDb->rollback();
			$this->error ( "操作失败" );
		}
		
		$tranDb->commit();
		$this->success ( "操作成功！" );
	}

}
?>