<?php

namespace Admin\Controller;
use Admin\Model\MemberMemoModel;
use Admin\Model\OrderManageModel;
use Common\Model\CodeModel;

class OrderManageController extends BaseController {
    /**
     * 待售清单
     */
	public function getPendingOrders(){
        $list = OrderManageModel::getPendingOrders();
        $this->assign ( "list", $list[1]);
        $this->assign ( "page", $list[0] );
        $this->display('pending_orders');
    }

    /**
     * 获取采购清单
     */
	public function getShoppingList(){
        $date = I('datetime');
        if(!$date){
            $date = date('Y-m-d');
        }
        $list = OrderManageModel::getShoppingList($date);
        $this->assign ( "list", $list[1]);
        $this->assign ( "page", $list[0] );
        $this->display('shopping_list');
    }

    /**
     * 导出数据
     */
	public function getPendingOrderTable(){
        $list = OrderManageModel::getPendingOrders();
        $data= array();
        foreach($list as $key=>$val){
            $data[$key]['id'] = $val['id'];
            $data[$key]['username'] = $val['username'];
            $data[$key]['telephone'] = $val['telephone'];
            $data[$key]['address'] = $val['address'];
            $data[$key]['cityname'] = $val['cityname'];
            $data[$key]['cityname'] = $val['cityname'];
            $data[$key]['delivertime'] = $val['delivertime'];
            if($val['invoice']==1){
                $data[$key]['delivertime'] = '是';
            }else{
                $data[$key]['delivertime'] = '';
            }
            if($val['paymethod']==1){
                $data[$key]['paymethod'] = '是';
            }else{
                $data[$key]['paymethod'] = '';
            }
            if(strpos($val['supplyid'],'2,')){
                $data[$key]['kc'] = '是';
            }else{
                $data[$key]['kc'] = '';
            }
            if(strpos($val['supplyid'],'10,')){
                $data[$key]['zy'] = '是';
            }else{
                $data[$key]['zy'] = '';
            }
            if(strpos($val['supplyid'],'3,')){
                $data[$key]['ln'] = '是';
            }else{
                $data[$key]['ln'] = '';
            }
            if(strpos($val['supplyid'],'11,')){
                $data[$key]['ql'] = '是';
            }else{
                $data[$key]['ql'] = '';
            }
            if(strpos($val['supplyid'],'5,')){
                $data[$key]['sny'] = '是';
            }else{
                $data[$key]['sny'] = '';
            }
            $data[$key]['info'] = $val['info'];
            $data[$key]['info0'] = $val['info0'];
        }
        exportexcel($data,array('id',
            '用户名','电话', '地址','城市',
            '送货时间','发票','在线', 'KC肉',
            '自营肉','莲娜','强力','酸奶油','客户留言','内部留言'),'pendingOrder');

    }
}
?>