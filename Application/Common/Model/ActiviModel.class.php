<?php
namespace Common\Model;
use Think\Model;

/**
 * 临时活动（）
 * Class ActiviModel
 * @package Common\Model
 */
class ActiviModel extends Model
{
    const START_TIME='2016-12-21 00:00:00'; //开始时间
    const END_TIME='2016-12-24 23:59:59'; //结束时间
    const PLAN='400|40'; //普通优惠
    const PLAN_SUPER='400|50'; //特定优惠
    const NAME='specific';
    public static function getActiviDiscount($amount){
        $nowtime = date('Y-m-d H:i:s');
        if($nowtime<self::START_TIME || $nowtime>self::END_TIME){
            return 0;
        }
        $plan = self::PLAN;
        if(session('hd') == self::NAME || cookie('hd') == self::NAME){
            $plan = self::PLAN_SUPER;
        }
        list($offset,$discount) = explode('|',$plan);
        if($amount >=$offset ){
            return $discount;
        }
        return 0;
    }
}