<?php
namespace Shop\Model;
use Admin\Model\ContentModel;
use Common\Model\OrderModel;
use Think\Model;

class ProductSoldModel extends Model {

    //获取指定时间内的商品销售量
    public static function getProductDaySold($time){
        $where['od.addtime'] = array(array("egt",$time." 00:00:00"),array("elt",$time." 23:59:59"));
        $where['o.status'] = array('lt',OrderModel::CANCELLED); //获取
        $field = 'od.productid,od.addtime,SUM(od.num) as sold';
        $data = M('order_detail')->alias('od')->join("my_order as o on od.orderno = o.orderno")
            ->where($where)->field($field)->group('od.productid')->select();
        if(!empty($data)){
            foreach($data as $val){
                $val['sold'] = intval($val['sold']);
                self::addProductSales($val);//插入销售表
            }
            return true;
        }else{
            return false;
        }
    }

    public static function getProductSalesByCon($con){
        if(!empty($con)){
            return M('product_sales')->where($con)->select();
        }else{
            return false;
        }
    }

    public static function addProductSales($data){
        if(!empty($data)){
            $con['productid'] = $data['productid'];
            $con['addtime'] = $data['addtime'];
            //同一个商品在同一时段只能添加一次
            if(!self::getProductSalesByCon($con)){
                return M('product_sales')->add($data);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 按周、月给商品计算月、周售，月、周可售多少天
     * @param $day
     * @return bool
     */
    public static function getProductAVGSoldByDay($day){
        if(regex($day,'number')){
            $date = getStimeAndETime($day);
            $field = 'p.productid,SUM(p.sold) as totalsold,AVG(p.sold) as sold,c.stock';
            $where['p.addtime'] = array(array("egt",$date['stime']." 00:00:00"),array("elt",$date['etime']." 23:59:59"));
            $data = M('product_sales')->alias('p')->join('my_content as c on p.productid = c.id')->where($where)->field($field)->group('p.productid')->select();
            foreach($data as $val){
                if($day ==7){ //周
                    $savedata['week_sale'] = $val['totalsold'];
                    $savedata['days_by_week'] =ceil($val['stock']/$val['sold']) ; // 周可售（天）= 库存/周平均售
                }else{ //月
                    $savedata['month_sale'] = $val['totalsold'];
                    $savedata['days_by_month'] = intval($val['stock']/$val['sold']) ;// 月可售（天）= 库存/月平均售
                }

                $con['id'] = $val['productid'];
                $savedata['update_time'] = date('Y-m-d H:i:s');
                 M('content')->where($con)->save($savedata);
            }
        }else{
            return false;
        }
    }

}

?>