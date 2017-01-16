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
        $time = date('Y-m-d',strtotime("-11 day"))." 00:00:00";
       ProductSoldModel::getProductDaySold($time);
       // ProductSoldModel::getProductAVGSoldByDay(7);
    }



}

?>
