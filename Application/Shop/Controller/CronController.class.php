<?php

/**
 *  类描述: [定时任务类]
 */
namespace Shop\Controller;
use Shop\Model\ProductSoldModel;
use Think\Controller;

class CronController extends Controller
{
        
    public function getProductSold(){
//        ProductSoldModel::getProductAVGSoldByDay(7);
        ProductSoldModel::getProductAVGSoldByDay(30);
        exit;
        $time = date('Y-m-d',strtotime("-1 day"));
        //获取前一天上商品销售量
        if(false!==ProductSoldModel::getProductDaySold($time)){

        }
    }



}

?>
