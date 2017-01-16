<?php
namespace Shop\Model;
use Common\Model\OrderModel;
use Think\Model;

class ProductSoldModel extends Model {

    //获取指定时间内的商品销售量
    public static function getProductDaySold($time){
        $where['od.addtime'] = array("egt",$time);
        $where['o.status'] = array('lt',OrderModel::CANCELLED); //获取
        $field = 'od.productid,od.addtime,SUM(od.num) as sold';
        $data = M('order_detail')->alias('od')->join("my_order as o on od.orderno = o.orderno")
            ->where($where)->field($field)->group('od.addtime')->select();
        dump(M()->_sql());
        dump($data);
exit;
        if(!empty($data)){
            foreach($data as $val){
                $val['sold'] = intval($val['sold']);
                self::addProductSales($val);//插入销售表
            }
        }
    }

    public static function addProductSales($data){
        if(!empty($data)){
            return M('product_sales')->add($data);
        }else{
            return false;
        }
    }

    public static function getProductAVGSoldByDay($day){
        if(regex($day,'number')){
            $date = getStimeAndETime($day);
            $field = 'productid,SUM(sold) as totalsold,AVG(sold) as sold';
            $where['addtime'] = array(array("egt",$date['stime']." 00:00:00"),array("elt",$date['etime']." 23:59:59"));
            $data = M('product_sales')->where($where)->field($field)->group('productid')->select();
            dump(M()->_sql());
            dump($data);

        }else{
            return false;
        }
    }

}

?>