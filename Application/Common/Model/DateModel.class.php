<?php
namespace Common\Model;
use Think\Model;
use Org\Util\String;

class DateModel extends Model
{
    const FUTURE_DAY = 5;//未来5天的日期
    const ADD_DELIVERTIME = 1;//配送准备时间计算
    /**
     * 获取本月不配送日
     * @return bool|\multitype
     */
    public static function getHoliday(){
        $strdates = lbl('disabled_dates');
        if ($strdates) {
            $strdates = str_replace("\r\n", ",", $strdates);
            $strdates = str_replace("，", ",", $strdates);
            $arr = arr2clr(str2arr($strdates));
            return $arr;
        }
        return false;
    }

    /**
     * 获取未来5天的日期
     * @param int $day
     * @return array
     */
    public static function getFutureDay($day = self::FUTURE_DAY){
        $dataarr = array();
        $holiday = self::getHoliday();
        $date = intval($day);
        $disabledweek = lbl('disabled_week');
        $week = array(0,1,2,3,4,5,6);
        for($i=0;$i<=$date;$i++){
            $dataarr[$i]['time']=date('Y-m-d',strtotime("+$i day"));
            $dataarr[$i]['date']= getDateFormat(strtotime("+$i day"));
            foreach($holiday as $key=>$val){
                if($val == date('Y-m-d',strtotime("+$i day"))){//节假日
                    $dataarr[$i]['isholiday']=1;
                }else{
                    if(in_array($disabledweek,$week)){//判断是否有周末不配送设置
                        if($w =date("w",strtotime("+$i day")) == $disabledweek){
                            $dataarr[$i]['isholiday']=1;
                        }
                    }
                }
            }
        }
        return $dataarr;
    }

    public static function checkTimeForOrder($time){
        $holiday = self::getHoliday();
        if(in_array($time[0],$holiday)){//不配送
            if($time[0] == date('Y-m-d')){
                return "sorry, no delivery today";
            }else{
                return $time[0].",sorry, we don't deliver";
            }
        }
        if($time[0] == date('Y-m-d')){ //选择今天,并且选有配送时段
            $times = str2arr(lbl('delivertime'),"\r\n");//配送时段
            $h = intval(date('H'))+self::ADD_DELIVERTIME;
            foreach($times as $k=>$v){
                list($star, $end) = explode('-',$v);
                if($star>$h){
                    return true;
                }
            }
            return false;//2:01分超过配送时段2:00-4:00 不给配送
        }else{
            return true;
        }
    }


}