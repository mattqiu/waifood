<?php

/**
 *  类描述: [定时任务类]
 */
namespace Shop\Controller;
use Think\Controller;

class CronController extends Controller
{

    public function getProductSold(){

        $date = getStimeAndETime(30);
        $dataArr['week'] = 7;
        $dataArr['month'] = 30;
        foreach($dataArr as $key=>$val){
            if($val){

            }
        }
    }



}

?>
