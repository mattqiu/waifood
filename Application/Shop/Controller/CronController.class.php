<?php

/**
 *  类描述: [定时任务类]
 */
namespace Shop\Controller;
use Shop\Model\ProductSoldModel;
use Think\Controller;

class CronController extends Controller
{

    public function getProductSold360(){
        ignore_user_abort(true); // 忽略客户端断开
        set_time_limit(0);    // 设置执行不超时
        GLog('定时','star');
        for($i=1;$i<360;$i++){
            $time = date('Y-m-d',strtotime("-$i day"));
            //获取前一天上商品销售量
            ProductSoldModel::getProductDaySold($time);
            GLog('定时','day'.$i);
        }
        GLog('定时','stop');
    }

    public function getProductAVGSoldByDay(){
        ProductSoldModel::getProductAVGSoldByDay(7);
        ProductSoldModel::getProductAVGSoldByDay(30);
    }

    public function loadDaySold(){
        $con['_string'] = 'days_by_month <= 0 or days_by_week <= 0';
        $data = M('content')->where($con)->field('id,stock')->select();
        if(!empty($data)){
            foreach($data as $val){
                $con1['id'] = $val['id'];
                $savedata['days_by_month'] = $val['stock'];
                $savedata['days_by_week'] = $val['stock'];
                M('content')->where($con1)->save($savedata);
            }
        }
    }

    public function getProductSold(){
        $time = date('Y-m-d',strtotime("-1 day"));
        //获取前一天上商品销售量
        if(false!==ProductSoldModel::getProductDaySold($time)){
            ProductSoldModel::getProductAVGSoldByDay(7);
            ProductSoldModel::getProductAVGSoldByDay(30);
        }
    }



}

?>
