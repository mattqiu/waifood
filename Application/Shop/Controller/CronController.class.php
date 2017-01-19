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
        for($i=1;$i<40;$i++){
            $time = date('Y-m-d',strtotime("-$i day"));
            //获取前一天上商品销售量
            if(false!==ProductSoldModel::getProductDaySold($time)){
                ProductSoldModel::getProductAVGSoldByDay(7);
                ProductSoldModel::getProductAVGSoldByDay(30);
            }
          //  ProductSoldModel::getProductDaySold($time);//获取前一天上商品销售量
        }
        // ProductSoldModel::getProductAVGSoldByDay(7);
        // $time = date('Y-m-d',strtotime("-1 day"));
    }



}

?>
