<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
use Think\Page;

class OrderManageModel extends Model {
    /*
     * $list = D("MemberLog")->alias("l")
    ->join("t_member_card as c on l.m_id = c.m_id")
    ->join("t_food_order as o on l.order_id = o.order_id")
    ->where($where)->limit($page->firstRow,$page->listRows)->field($field)->order($order)
    ->select();
    */
    /**
     * 采购清单
     * @param $date
     */
    public static function getShoppingList($date){
        $con['o.status'] = array('lt',OrderModel::DELIVERING);
        $con['d.supplyid'] = array(array('eq',2),array('eq',3),
            array('eq',5),array('eq',10),array('eq',11), 'or');
        $con['_string'] = "o.delivertime like '$date%'";
        $field = 'o.delivertime,d.productid,d.productname,d.unit,sum(d.num) as num,
        d.supplyname,group_concat(d.num) as info';
        $list =  M('order')->alias('o')->join("my_order_detail as d on o.orderno = d.orderno")->where($con)
            ->field($field)->group('d.productid')->order('d.supplyid desc')->select();
        return $list;
    }

    /**#待售清单
     * @param $orderId
     */
    public static function getPendingOrders(){
        $con['status'] = array('lt',OrderModel::COMPLETED);
        $field = 'id,username,userid,telephone,address,cityname,delivertime,
        amount,status,invoice,paymethod,pay,orderno,info,info0';
        $order = M('order')->where($con)->field($field)->order('delivertime')->select();
        foreach($order as &$val){
            $where['orderno'] = $val['orderno'];
            $field = 'distinct(supplyid)';
            $val['date'] = substr($val['delivertime'],0,10);
            $val['time'] = substr($val['delivertime'],11);
           // list($val['date'],$val['time']) = explode(' ',$val['delivertime']);
            $datd = M('order_detail')->where($where)->field($field)->select();
            foreach($datd as $k=>$v){
                $val['supplyid'] .= $v['supplyid'].',';
            }
        }
        return $order;
    }

    /**获取日销售额
     * @param $orderId
     */
    public static function getDailySalesList($date){
        $con['o.status'] = array('lt',OrderModel::DELIVERING);
        $con['_string'] = "o.delivertime like '$date%'";
        $field = 'o.delivertime,d.productid,d.productname,d.unit,sum(d.num) as num,
        d.supplyname,group_concat(d.num) as info';
        $list =  M('order')->alias('o')->join("my_order_detail as d on o.orderno = d.orderno")->where($con)
            ->field($field)->group('d.productid')->order('d.supplyid desc')->select();
        return $list;
    }

    /**
     * @param $orderId
     */
    public static function getCommoditySales($contentid,$date){
        if(regex($contentid,'number')){
            $con['c.id'] = $contentid;
        }
        if($date){
            $con['_string'] = "o.delivertime like '$date%'";
        }
        $field = 'od.productid,od.productname,od.unit,od.num,od.supplyname,od.orderno,od.price,o.id,o.delivertime,o.userid,o.username,o.address';
        $list =  M('content')->alias('c')
            ->join("my_order_detail as od on c.id = od.productid")
            ->join("my_order as o on o.orderno = od.orderno")
            ->field($field)->where($con)->order('c.id desc')->select();
        return $list;
    }

    public static function getOrderForGJP($id){
        $con['o.id'] = $id;
        $filed = 'o.status,d.ext as no,d.productid,d.ext as name ,d.num,d.price,d.ext as zk,d.ext as note';
       return M('order')->alias('o')->join('my_order_detail as d on o.orderno = d.orderno')
            ->field($filed)->where($con)->select();
    }
}
