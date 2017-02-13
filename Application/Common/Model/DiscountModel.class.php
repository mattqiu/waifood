<?php
namespace Common\Model;
use Think\Model;
class DiscountModel extends Model {
    const NORMAL  = 1;//正常；
    const DISABLE = 0;//禁用；
    const DELETE  = -1;//删除；

    const USER_GROUPS  = 1;//用户组
    const ORDER_ALL  = 2;//订单全局

    const DISCOUNT  = 1;//百分比
    const FULL_REDUCTION  = 2;//满减

    const EXCLUSIVE_DISCOUNT = 1;//排他折扣

    /* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '必须填写英文名', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('discount', 'require', '请填写优惠值', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    public static function getDiscountByType($type,$time=false){
        if($type){
            $con['type'] = $type;
            $con['status'] = self::NORMAL;
            if($time){
                $con['_string'] = " star_time < now() and end_time >= now()";
            }
            return M('discount')->where($con)->select();
        }
        return false;
    }
    /**
     * 获取所有未删除的优惠信息
     * @return mixed
     */
    public static function getAllDiscount(){
        $con['status'] = array('neq',self::DELETE);
        return M('discount')->where($con)->select();
    }

    public static function getDiscountById($id){
        if(!regex($id,'number')){
            return false;
        }
        $con['id'] = $id;
        $con['status'] = self::NORMAL;
        $con['_string'] = " star_time < now() and end_time >= now()";
        return M('discount')->where($con)->find();
    }

    /**
     * 获取用户组折扣
     * @param $money
     * @param $userid
     * @return array|bool
     */
    public static function getUserGroupsDiscount($money,$userid){
        if($money>0 && $userid>0){
            $disMoney = 0;$method = 0;
            $user = UserModel::getUserById($userid);
            //新用户折扣（---------临时优惠-----）
            if($user['addtime'] >='2017-02-12 17:47:35' && $user['addtime'] <= '2017-02-19 23:59:59'){
                $user['discount_id'] = 5;//临时优惠活动
            }
            if(isset($user['discount_id']) && $user['discount_id']>0){
                $userdiscount = self::getDiscountById($user['discount_id']);
                if($userdiscount['status'] != self::NORMAL){ //活动失效
                    return false;
                }
                $method =$userdiscount['method'];
                $overlay =$userdiscount['overlay'];
                $name =$userdiscount['name'];
                $namecn =$userdiscount['namecn'];
                if($userdiscount){
                    //满减
                    if($userdiscount['method'] == self::FULL_REDUCTION){
                        list($offset,$dis)=explode('|',trim($userdiscount['discount']));
                        if($money>=$offset){
                            $disMoney =  $dis;
                        }
                    }else{ //打折
                        $disMoney = $money - ($money*$userdiscount['discount']); //优惠 = 总金额-(总金额*折扣)
                    }
                }
            }else{
                return false;
            }
            return array('money'=>$disMoney,'method'=>$method,'overlay'=>$overlay,'name'=>$name,'namecn'=>$namecn,'from'=>'user');
        }
        return false;
    }

    public static function getOrderAllDiscount($money){
        $discount = self::getDiscountByType(self::ORDER_ALL,true);
        if(!empty($discount)){
            $discArr = array();
            //获取所有优惠
            foreach($discount as $key=>$val){
                if($val['method'] == self::DISCOUNT){ //打折
                    $discArr[$key]['money'] = $money - ($money*$val['discount']); //优惠 = 总金额-(总金额*折扣)
                }else{
                    //满减
                    list($offset,$dis)=explode('|',trim($val['discount']));
                    if( $money>=$offset){
                        $discArr[$key]['money'] = $dis;
                    }else{
                        $discArr[$key]['money'] = 0;
                    }
                }
                $discArr[$key]['method'] = $val['method'];
                $discArr[$key]['overlay'] = $val['overlay'];
                $discArr[$key]['from'] = 'orderall';
                $discArr[$key]['name'] = $val['name'];
                $discArr[$key]['namecn'] = $val['namecn'];
            }
            $discArr = myArraySort($discArr,'money',SORT_DESC);//按金额高低排序
            return $discArr[0]; //取优惠最高的
        }
        return false;
    }

    /**
     * @param $money
     * @param int $userid
     * @return mixed
     */
    public static function getDiscountMoney($money,$userid = 0){
        $discount = array();
        if($userid>0){
            if($userdisc = self::getUserGroupsDiscount($money,$userid)){
                $discount[] =$userdisc;
            }
        }
        if($money>0){
            if($orderAllDiscount = self::getOrderAllDiscount($money)){
                $discount[] =$orderAllDiscount;
            }
        }
        $discount = myArraySort($discount,'method',SORT_ASC);//按折扣方式从折扣后满减排序
        if(!$discount){
            return false;
        }
        if(empty($discount)){
            return false;
        }elseif(count($discount) ==1){
            $disArr[0] =  $discount[0];
            $disArr['money'] = $discount[0]['money'];
//            $disArr['name'] = $discount[0]['name'];
//            $disArr['namecn'] = $discount[0]['namecn'];
        }else if($discount[0]['money'] > $discount[1]['money']){  //有多种优惠
            if($discount[0]['overlay'] == self::EXCLUSIVE_DISCOUNT){ //不支持折上折
                $disArr[0] =  $discount[0];
                $disArr['money'] = $discount[0]['money'];
//                $disArr['name'] = $discount[0]['name'];
//                $disArr['namecn'] = $discount[0]['namecn'];
            }else{ //可折上折
                $money1 = $money - $discount[0]['money'];//折上折的总金额=总金额-最高优惠
                if(!empty($discount[1])){ //第二个有值
                    if($discount[1]['from'] == 'user'){
                        $discountmoney = self::getUserGroupsDiscount($money1,$userid);
                    }else{
                        $discountmoney = self::getOrderAllDiscount($money1);
                    }
                    $disArr[0] =  $discount[0];
                    $disArr[1] =  $discountmoney;
                    $disArr['money'] =($discount[0]['money']+$discountmoney['money']); //总优惠=总金额-（最高优惠+（总金额-最高优惠计算出下一个优惠））
//                    $disArr['name'] = $discount[0]['name'].'+'. $discount[0]['name'];
//                    $disArr['namecn'] = $discount[0]['namecn'].'+'.$discount[0]['namecn'];
                }else{
                    $disArr[0] =  $discount[0];
                    $disArr['money'] = $discount[0]['money']; //只有一个值
//                    $disArr['name'] = $discount[0]['name'];
//                    $disArr['namecn'] = $discount[0]['namecn'];
                }
            }
        }else{  //订单全局扣优惠高
            if($discount[1]['overlay'] == self::EXCLUSIVE_DISCOUNT){ //不支持折上折
                $disArr[1] =  $discount[1];
                $disArr['money'] = $discount[1]['money'];
//                $disArr['name'] = $discount[1]['name'];
//                $disArr['namecn'] = $discount[1]['namecn'];
            }else{ //可折上折
                $money1 = $money - $discount[1]['money']; ;//折上折的总金额=总金额-最高优惠
                if($discount[0]['from'] == 'user'){
                    $discountmoney = self::getUserGroupsDiscount($money1,$userid);
                }else{
                    $discountmoney = self::getOrderAllDiscount($money1);
                }
                $disArr['money'] =($discount[1]['money']+$discountmoney['money']); //总优惠=总金额-（最高优惠+（总金额-最高优惠计算出下一个优惠））
                $disArr[0] =  $discount[1];
                $disArr[1] =  $discountmoney;

                //$disArr['name'] = $discount[1]['name'].'+'. $discount[0]['name'];
                //$disArr['namecn'] = $discount[1]['namecn'].'+'. $discount[0]['namecn'];
            }
        }
        return  $disArr;
    }

    /**
     * 添加优惠方案
     * @param $data
     * @return bool|mixed
     */
    public static function addDiscount($data){
        if(!empty($data)){
            return M('discount')->add($data);
        }
        return false;
    }

    /**
     * 修改优惠
     * @param $id
     * @param $data
     * @return bool
     */
    public static function modifyDiscount($id,$data){
        if(regex($id,'number') && !empty($data)){
            $con['id'] = $id;
            return M('discount')->where($con)->save($data);
        }
        return false;
    }

}

?>