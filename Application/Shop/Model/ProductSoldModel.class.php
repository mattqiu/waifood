<?php
namespace Shop\Model;
use Think\Model;

class ProductSoldModel extends Model {

    public static function getProductSoldBy($day){
        if(regex($day,'number')){
            $date = getStimeAndETime($day);
            $order = "l.create_time desc";
          /*  $count =  D("MemberLog")->alias("l")
                ->join("t_member_card as c on l.m_id = c.m_id")
                ->join("t_food_order as o on l.order_id = o.order_id")
                ->where($where)->count();
            $field = 'c.card_number,c.name,c.tel,c.blance,l.*,o.discounts,o.is_online_pay';*/
            M('content')->alias('c')->join('my_order_detail as od on c.id=od.productid');
        }else{
            return false;
        }
    }

}

?>