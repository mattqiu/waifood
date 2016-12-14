<?php
namespace Common\Model;
use Think\Model;

class OrdernewModel extends Model
{
	/**
	 * 生成订单号
	 */
	public function makeOrderNo()
	{
		return (intval(date('Y'))-2014) . date('m') . date('d') . substr(NOW_TIME, -5) . substr(microtime(), 2, 4) . sprintf('%02d', rand(0, 99));
	}
	
	/**
	 * 生成交易号
	 */
	public function makeTradeNo()
	{
		return time().sprintf('%02d', rand(0, 99));
	}
	
	/**
	 * 创建订单
	 * @param int $memberId
	 * @param int $payment			支付方式
	 * @param float $amount			金额
	 * @param string $errmsg		错误消息
	 * @return boolean|string
	 */
	public function createOrderForCleaning($memberId, $payment, $amount, $cleaningData, &$errmsg=null)
	{
		$tranDb = new \Think\Model();
		$tranDb->startTrans();
	
		//订单编号
		$orderNo = $this->makeOrderNo();
	
		//生成订单
		$data = array(
				'orderno'		=> $orderNo,
				'member_id'		=> $memberId,
				'type'			=> 3,	
				'payment'		=> $payment,
				'amount'		=> $amount,
		);
		$orderId = $tranDb->table('__ORDERNEW__')->add($data);
		if( !$orderId ){
			$tranDb->rollback();
			return false;
		}
	
		//把信息加入清洁表
		$cleaningData['order_id'] = $orderId;
		$cleaningData['orderno'] = $orderNo;
		$re = $tranDb->table('__ORDER_CLEANING__')->add($cleaningData);
		if( !$re ){
			$tranDb->rollback();
			//$errmsg = M()->getLastSql();
			return false;
		}
	
		$tranDb->commit();
		return $orderNo;
	}
	
	
	public function createOrderForCarrental($memberId, $amount, $selectList, $contactData, &$errmsg=null)
	{
		$tranDb = new \Think\Model();
		$tranDb->startTrans();
		
		//订单编号
		$orderNo = $this->makeOrderNo();
		
		//生成订单
		$data = array(
				'orderno'		=> $orderNo,
				'member_id'		=> $memberId,
				'type'			=> 4,
				'payment'		=> 3,
				'amount'		=> $amount,
		);
		$orderId = $tranDb->table('__ORDERNEW__')->add($data);
		if( !$orderId ){
			$tranDb->rollback();
			$errmsg = '111';
			return false;
		}
		
		//加入租车表(联系人信息)
		$contactData['order_id'] = $orderId;
		$contactData['orderno'] = $orderNo;
		$carrentalId = $tranDb->table('__ORDER_CARRENTAL__')->add($contactData);
		if( !$carrentalId ){
			$tranDb->rollback();
			$errmsg = '222';
			return false;
		}
		
		//加入租车详细表
		foreach ($selectList as $row){
			foreach ( $row['cars'] as $item ){
				$data = array(
					'order_id'		=> $orderId,
					'orderno'		=> $orderNo,
					'carrental_id'	=> $carrentalId,
					'name'			=> $row['name'],
					'desc'			=> $row['desc'],
					'pic'			=> $row['pic'],
					'day_price'		=> $row['day_price'],
					'seat'			=> $row['seat'],
					'use_date'		=> $item['use_date'],
					'use_days'		=> $item['use_days'],
					'pilot'			=> $item['pilot'],
					'delivery_vehicles'	=> $item['delivery_vehicles'],
					'pickup_car'	=> $item['pickup_car'],
					'amount'		=> $item['amount']
				);
				$re = $tranDb->table('__ORDER_CARRENTAL_DETAIL__')->add($data);
				if( !$re ){
					$tranDb->rollback();
					$errmsg = $tranDb->getLastSql();
					return false;
				}
			}
		}
		
		$tranDb->commit();
		return $orderNo;
	}
	
	//向管理员发送邮箱
	public function sendMailToMasterForCleaning($orderno)
	{
		$order = M('Ordernew')->where(array('orderno'=>$orderno))->find();
		$cleaning = M('OrderCleaning')->where(array('orderno'=>$orderno))->find();
		
		$body = '<div>订单号：'.$orderno.'(金额：<span style="color:red">'.$order['amount'].'元</span>) <br />时间：'.$order['create_time'].'</div>';
		$body .= '<div>类型：'.($cleaning['type']==2?'包月':'单次').'</div>';
		if( $cleaning['type'] == 2 ){
			$body .= '<div>每月次数：'.$cleaning['month_times'].'</div>';
			$body .= '<div>每次小时：'.$cleaning['hours'].'</div>';
			$body .= '<div>预订月数：'.$cleaning['month_num'].'</div>';
		}else{
			$body .= '<div>预订小时数：'.$cleaning['hours'].'</div>';
		}
		$body .= '<div>开始时间：'.$cleaning['start_time'].'</div>';
		$body .= '<div>联系人：'.$cleaning['uname'].'</div>';
		$body .= '<div>电话：'.$cleaning['phone'].'</div>';
		$body .= '<div>邮箱：'.$cleaning['email'].'</div>';
		$body .= '<div>地址：'.$cleaning['address'].'</div>';
		$body .= '<div>备注：'.$cleaning['remark'].'</div>';
		
		$to =ConfigModel::getConfig('WEB_SITE_COPYRIGHT');//获取管理员邮箱 C('config.WEB_SITE_COPYRIGHT');
		$subject = '清洁订单:' . get_username(get_userid());
		send_mail($to, $subject, $body);
	}
}