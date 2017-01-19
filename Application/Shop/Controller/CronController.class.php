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
        GLog('定时','star');
        for($i=1;$i<360;$i++){
            $time = date('Y-m-d',strtotime("-$i day"));
            //获取前一天上商品销售量
            ProductSoldModel::getProductDaySold($time);
            GLog('定时','day'.$i);
          //  ProductSoldModel::getProductDaySold($time);//获取前一天上商品销售量
        }
        GLog('定时','stop');
        // ProductSoldModel::getProductAVGSoldByDay(7);
        // $time = date('Y-m-d',strtotime("-1 day"));
    }

    public function getProductAVGSoldByDay(){
        ProductSoldModel::getProductAVGSoldByDay(7);
        ProductSoldModel::getProductAVGSoldByDay(30);
    }
/*
    public function getProductSold(){
        $time = date('Y-m-d',strtotime("-1 day"));
        //获取前一天上商品销售量
        if(false!==ProductSoldModel::getProductDaySold($time)){
            ProductSoldModel::getProductAVGSoldByDay(7);
            ProductSoldModel::getProductAVGSoldByDay(30);
        }
    }*/



}

?>
