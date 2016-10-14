<?php
namespace Shop\Controller; 

class CleaningController extends BaseController
{
	public function index()
	{
		$this->assign('title', 'House Cleaning Service');
		$this->assign('timesPrice', C('config.CLEANING_TIMES_HOUR_PRICE'));
		$this->assign('monthPrice', C('config.CLEANING_MONTH_HOUR_PRICE'));
		$this->assign('user',UserModel::getUserById(get_userid()));
		$this->display();
	}
	
	/**
	 * 提交清洁
	 */
	public function submit()
	{
		$memberId = get_userid();
		if ($memberId == "0") {
			$this->ajaxReturn(array('errcode'=>1, 'errmsg'=>'Please login'));
		}
		
		$payment = I('payment', 3, 'int');
		if( $payment != 2 && $payment != 3 ){
			$this->ajaxReturn(array('errcode'=>1, 'errmsg'=>'Operation failed'));
		}
		
		$data = array(
			'type'		=> I('post.type', 1, 'int'),
			'hours'		=> I('post.hours', 1, 'int'),
			'start_time'=> date('Y-m-d H:i', strtotime(I('post.datetime', '', 'string').' '.I('post.time', '', 'string'))),
			'uname'		=> I('post.uname', '', 'string'),
			'phone'		=> I('post.phone', '', 'string'),
			'email'		=> I('post.email', '', 'string'),
			'address'	=> I('post.address', '', 'string'),
			'remark'	=> I('post.remark', '', 'string'),
		);
		
		if( $data['start_time'] <= 0 || $data['hours'] <=0 || strlen($data['phone'])<8 || $data['address']=='' ){
			$this->ajaxReturn(array('errcode'=>1, 'errmsg'=>'Please fill in the correct information'));
		}
		
		if( $data['type'] == 2){
			$data['month_times']= I('post.times', 0, 'int');	//每月次数
			$data['month_num']	= I('post.month', 0, 'int');	//订购月数
			if( $data['month_times'] <= 0 || $data['month_num'] <=0 ){
				$this->ajaxReturn(array('errcode'=>1, 'errmsg'=>'Please fill in the correct information'));
			}
			$data['hour_price'] = C('config.CLEANING_MONTH_HOUR_PRICE');
			$data['total_hour']	= $data['month_times'] * $data['month_num'] * $data['hours'];
			$amount				= $data['hour_price'] * $data['total_hour'];
		}else{
			$data['type']		= 1;
			$data['hour_price'] = C('config.CLEANING_TIMES_HOUR_PRICE');
			$data['total_hour'] = $data['hours'];
			$amount				= $data['hour_price'] * $data['hours'];
		}
		
		//插入数据库
		$errmsg = null;
		$orderno = D('Ordernew')->createOrderForCleaning($memberId, $payment, $amount, $data, $errmsg);
		if( !$orderno ){
			$this->ajaxReturn(array('errcode'=>1, 'errmsg'=> $errmsg ? $errmsg : 'Operation failed1'));
		}
		
		//管理员邮件
		$to = C('config.WEB_SITE_COPYRIGHT');
		if( $to ){
			D('Ordernew')->sendMailToMasterForCleaning($orderno);
		}
		
		$this->ajaxReturn(array('errcode'=>0, 'data'=>$orderno));
	}
	
	/**
	 * 成功
	 */
	public function submitSuccess($orderno)
	{
		$order = M('Ordernew')->field(array('orderno'=>$orderno))->find();
		if( !$order ){
			E('not found');
		}
		
		$this->assign('title', 'Success');
		$this->assign('orderno', $orderno);
		$this->display();
	}
}
?>