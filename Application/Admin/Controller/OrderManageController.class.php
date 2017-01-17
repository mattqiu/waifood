<?php

namespace Admin\Controller;
use Admin\Model\MemberMemoModel;
use Admin\Model\OrderManageModel;
use Admin\Model\OrderModel;
use Common\Model\GoodsGroupModel;

class OrderManageController extends BaseController {
    /**
     * 待售清单
     */
	public function getPendingOrders(){
        $list = OrderManageModel::getPendingOrders();
        $statuslist=C('config.CONFIG_STATUS_LIST');
        $this->assign("statuslist",$statuslist);
        $this->assign ( "list", $list);
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
        $this->assign ( "list", $list);
        $this->assign ( "today", date('Y-m-d') );
        $this->assign ( "tomorrow", date('Y-m-d',strtotime('+1 day')) );
        $this->assign ( "afterTomorrow", date('Y-m-d',strtotime('+2 day')) );
        $this->display('shopping_list');
    }

    /**
     * 获取日销售
     */
	public function getDailySalesList(){
        $date = I('datetime');
        if(!$date){
            $date = date('Y-m-d');
        }
        $list = OrderManageModel::getDailySalesList($date);
        $this->assign ( "list", $list);
        $this->assign ( "today", date('Y-m-d') );
        $this->assign ( "tomorrow", date('Y-m-d',strtotime('+1 day')) );
        $this->assign ( "afterTomorrow", date('Y-m-d',strtotime('+2 day')) );
        $this->display('daily_sales');
    }

    /**
     *
     * 商品销售情况
     */
	public function commoditySales(){
        $contentid = I('contentid');
        $date = I('datetime');
        if(!$date){
            $date = date('Y-m-d');
        }
        $list = OrderManageModel::getCommoditySales($contentid,$date);
        $this->assign ( "list", $list);
        $this->assign ( "today", date('Y-m-d') );
        $this->assign ( "tomorrow", date('Y-m-d',strtotime('+1 day')) );
        $this->assign ( "afterTomorrow", date('Y-m-d',strtotime('+2 day')) );
        $this->display('commodity_sales');
    }

    /**
     * 查找订单详情（符合导入管家婆模板，任何符号都不能变动、excel的列宽必须是25）
     * @return mixed|void
     */
    public function getOrderForGJP(){
        $id = I('id');
        $list = OrderManageModel::getOrderForGJP($id);
        $data= array();
        $status = '';
        foreach($list as $key=>&$val){
            if($status == OrderModel::DRAFT){
                $status = 'draft';
            }elseif($status == OrderModel::CONFIRMED){
                $status = 'confirmed';
            }elseif($status == OrderModel::DELIVERING){
                $status = 'delivering';
            }elseif($status == OrderModel::COMPLETED){
                $status = 'completed';
            }else{
                $status = 'cancelled';
            }
            if($goods = GoodsGroupModel::isGroupGoods($val['productid'])){
                $goods = GoodsGroupModel::getGoodsChild($val['productid']);
                foreach($goods as $k=>$v){
                    if(isset($data[$v['productid']])){
                        $data[$v['productid']]['num'] += $val['num']*$v['num'];
                    }else{
                        $data[$val['productid']]['num'] =  $val['num']*$v['num'];
                        $data[$val['productid']]['productid'] = $v['productid'];
                        $data[$val['productid']]['name'] = $val['name'];
                        $data[$val['productid']]['price'] = $v['price'];
                        $data[$val['productid']]['no'] = $val['no'];
                        $data[$val['productid']]['zk'] = $val['zk'];
                        $data[$val['productid']]['note'] = $val['note'];
                    }
                }
            }else{
                if(isset($data[$val['productid']])){   dump($val['num']*$v['num']);
                    $data[$val['productid']]['num'] += $val['num'];
                }else{
                    $data[$val['productid']]['num'] = $val['num'];
                    $data[$val['productid']]['productid'] = $val['productid'];
                    $data[$val['productid']]['name'] = $val['name'];
                    $data[$val['productid']]['price'] = $val['price'];
                    $data[$val['productid']]['no'] = $val['no'];
                    $data[$val['productid']]['zk'] = $val['zk'];
                    $data[$val['productid']]['note'] = $val['note'];
                }
            }
        }
        $filename = $id.'-'.$status;
        $title = array('商品条码',
            '商品编号','商品名称', '数量','单价',
            '折扣(0.9为9折)','备注(不能超过200个字符)');
        downloadExcel($data,$filename,$title,25);
    }

    public function unfinishedMatters(){
        $userid = I('userid');
        $list = MemberMemoModel::getAllMemo($userid);
        $this->assign ( "list", $list);
        $this->display('unfinished_matters_list');
    }

}
?>