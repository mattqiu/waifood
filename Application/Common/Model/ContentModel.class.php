<?php
namespace Common\Model;
use Think\Model;
class ContentModel extends Model {
    const PROMOTION = 9;//Promotion
    const NEW_ARRIVAL = 8;//New Arrival
    const RECOMMEND = 11;//Recommend
    const NORMAL = 1;//Recommend
    const GENERAL_GOODS = 0;//普通商品
    const COMPOSITE_GOODS = 1;//复合商品
    const COMBINATION_OF_GOODS = 2;//组合商品

    const CAN_NEGATIVE_AOLD = 1;//支持可负销售
    const CANNOT_NEGATIVE_AOLD = 0;//不支持可负销售


    /**根据分组获取商品
     * @param $group
     * @return mixed
     */
    public static  function getGroupContent($group,$order=array()) {
        $db = M ( "magic" )->find ( $group );
        $where=array();
        $where['id']=array('in',$db['contentids']);
        $where ['status'] = 1;
        if(empty($order)){
            $order= 'addtime desc';
        }
        $field =  'id,title,indexpic,price,price1,description,unit,storage,origin,brand,stock';
        $list=M('content')->where($where)->field($field)->order($order)->select();
        return $list;
    }

    /**
     * 根据组合商品的id获取组合商品的库存
     * @param $groupid
     * @return int
     */
    public static function getGroupSold($groupid,$goodType){
        if(!empty($groupid) && strpos($groupid,',')>0){
            $idsArr =array_filter(explode('|',$groupid));
            $data = array();
            foreach($idsArr as $key=>$val){
                $idarr =array_filter(explode(',',$val));
                if(regex($idarr[0],'number')){
                    $goods=\Admin\Model\ContentModel::getContentById($idarr[0]);
                    $data[$key]['stock']=intval($goods['stock']/$idarr[1]);
                }
            }
            if($goodType == self::COMPOSITE_GOODS){
                return intval($data[0]['stock']);
            }elseif($goodType == self::COMBINATION_OF_GOODS){
                //按照库存从小到大排序（组合商品的库存由子商品库存数最小的决定）
                $datanew = myArraySort($data,'stock',SORT_ASC);
                return $datanew[0]['stock'];
            }
        }else{
            return 0;
        }
    }

    /**
     * 根据组合商品的id获取组合商品的库存
     * @param $groupid
     * @return int
     */
    public static function getGroupInfo($groupid){
        if(!empty($groupid) && strpos($groupid,',')>0){
            $idsArr =array_filter(explode('|',$groupid));
            $data = array();
            foreach($idsArr as $key=>$val){
                $idarr =array_filter(explode(',',$val));
                if(regex($idarr[0],'number')){
                    $goods=\Admin\Model\ContentModel::getContentById($idarr[0]);
                    $data[$key]['id']=$goods['id'];
                    $data[$key]['name']=$goods['title'];
                    $data[$key]['indexpic']=$goods['indexpic'];
                    $data[$key]['num']=$idarr[1];
                }
            }
            return $data;
        }else{
            return false;
        }
    }

    /**
     * 判断商品是否是组合或复合商品
     * @param $goodId
     * @return bool
     */
    public static function isGroupGoods($goodId){
        if(regex($goodId,'number')){
            $goods = \Admin\Model\ContentModel::getContentById($goodId);
            if(!empty($goods) &&  $goods['group_id'] != self::GENERAL_GOODS){
                if(strpos($goods['group_id'],',')>0){
                    return $goods['group_id'];
                }else{ //是组合或复合商品，但格式不正确的
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 库存销量操作
     * @param $orderno
     */
    public static function catStockForOrder($orderno){
        $order =  OrderModel::getOrderDetailByOrderno($orderno);
        if(!empty($order)){
            foreach($order as $key=>$val){
                if($val['productid'] && $val['num']){
                    //复合或组合商品减库存，加销量
                    if(false !== $groupid = ContentModel::isGroupGoods($val['productid'])){
                        if( strpos($groupid,',')>0){
                            $idsArr =array_filter(explode('|',$groupid));
                            foreach($idsArr as $k=>$v){
                                $idarr =array_filter(explode(',',$v));
                                if(regex($idarr[0],'number')){
                                    $number = intval($val['num'])*intval($idarr[1]); //销售总数量=销售份数*公式
                                    self::modifyGoodsStockAndSold($idarr[0],$number,true);
                                }
                            }
                        }else{
                            GLog('order cat stock','商品ID.'.$val['productid'].'进去错误判读');
                            return false;
                        }
                        //复合、组合商品也是一个单独的正常商品
                        self::modifyGoodsStockAndSold($val['productid'],intval($val['num']));
                    }else{
                        //正常商品的减库存，加销量
                        self::modifyGoodsStockAndSold($val['productid'],intval($val['num']));
                    }
                }
            }
        }
    }

    /**
     * 库存销量操作
     * @param $goodsId
     * @param $soldnum
     */
    public  static function modifyGoodsStockAndSold($goodsId,$soldnum,$resold = false,$status=0){
        if(regex($goodsId,'number') && is_number($soldnum)){
            $con['id'] = $goodsId;
            M('content')->where($con)->setDec('stock',$soldnum);//减库存
            M('content')->where($con)->setInc('sold',$soldnum);//加销量
            if($resold == true){
                M('content')->where($con)->setInc('re_sold',$soldnum);//加组合销量
            }
            if($status===0){
                //库存小于1的商品自动下架
                $con = array();
                $con['stock'] = array('lt',1);
            }else{
                //大于1的上架
                $con['stock'] = array('gt',0);
            }
            $data['status'] = $status;
            M('content')->where($con)->save($data);
        }else{
            GLog('order cat stock','商品ID.'.$goodsId.'.减库存、加销量时参数错误;num='.$soldnum);
        }
    }

}

?>