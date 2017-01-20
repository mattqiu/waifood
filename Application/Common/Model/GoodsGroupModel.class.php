<?php
namespace Common\Model;
use Think\Model;
class GoodsGroupModel extends Model {
//    const NORMAL = 1;
    const GENERAL_GOODS = 0;//普通商品
    const COMBINATION_OF_GOODS = 2;//组合商品

    /**
     * 根据组合商品id获取子商品信息
     * @param $parentid
     * @return bool|mixed
     */
    public static function getGoodsChild($parentid){
        if(regex($parentid,'number')){
            $con['parentid'] =$parentid;
            return M('goods_group')->alias('gg')->join('my_content as c on gg.productid=c.id')->field('gg.*,c.title,c.stock,c.price,c.title')->where($con)->select();
        }else{
            return false;
        }
    }

    /**
     *订单中商品的实际数量
     * @param $data
     */
    public static function checkAndGetGroupGoodsNum($data){
        if(empty($data)){
            return false;
        }
        foreach ( $data as $key => $val) {
            if($val['id']){
                $product=M('content')->find($val['id']);
                if($product['good_type'] == \Admin\Model\ContentModel ::COMBINATION_OF_GOODS){ //是组合类型的商品
                    $goods = self::getGoodsChild($product['id']); //当前商品是组合类型的商品（取出该商品的子商品）
                    foreach($goods as $v){
                        if(!empty($data[$v['productid']])){ //既买了组合商品包含的子商品又单独买了该子商品
                            $data[$v['productid']]['true_num'] = ($val['num']*$v['num'])+$data[$v['productid']]['num']; //总数=组合份数*子商品组合数+单独购买数
                            $data[$v['productid']]['group_sold'] = $val['num']*$v['num'];
                        }else{//组合商品包含的子商品没有被单独购买
                            $data[$v['productid']]['true_num'] = ($val['num']*$v['num']); //总数=组合份数*子商品组合数+单独购买数
                            $data[$v['productid']]['group_sold'] = $val['num']*$v['num'];
                        }
                    }
                }else{
                    $data[$val['id']]['group_sold'] = 0;
                }
            }
        }

        return $data;
    }

    /**
     * 判断是否是被组合的子商品
     * @param $productid
     * @return bool
     */
    public static function isGroupChildGoods($productid){
        if(regex($productid,'number')){
            $con['productid'] = $productid;
            $data =  M('goods_group')->where($con)->select();
            if(!empty($data)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 根据商品id获取组合该商品的组合商品
     * @param $productid
     * @return array|bool
     */
    public static function getGroupGoodsByChildid($productid){
        if(regex($productid,'number')){
            $sql = 'SELECT DISTINCT parentid from '. C ( 'DB_PREFIX' ) .'goods_group where `productid` = '.$productid;
            $data = M ()->query($sql);
            $list = array();
            if(!empty($data)){
                $field = 'id,title,stock,namecn';
                foreach($data as $key=>$val){
                    $list[$key] =  \Admin\Model\ContentModel::getContentById($val['parentid'],$field);
                }
                return $list;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 判断是否是被组合的子商品
     * @param $productid
     * @return bool
     */
    public static function isGroupGoods($productid){
        if(regex($productid,'number')){
            $con['parentid'] = $productid;
            $data =  M('goods_group')->where($con)->select();
            if(!empty($data)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 根据商品id 获取组合商品并重新设置组合商品的库存
     * @param $productid
     * @return bool
     */
    public static function resetGroupGoodsStock($productid){
        if(regex($productid,'number')){
            $con['productid'] = $productid;
            $data =  M('goods_group')->where($con)->select();
            if(!empty($data)){
                foreach($data as $key=>$val){
                    if($val['parentid']){
                        $stock = self::getGroupStockByParentId($val['parentid']);
                       if(is_number($stock)){
                           $savedata['stock'] = $stock;
                           if($stock>0){
                               $savedata['status'] = 1;
                           }else{
                               $savedata['status'] = 0;
                           }
                           \Admin\Model\ContentModel::modifyContent($val['parentid'],$savedata); //重新设置组合商品的库存
                       }
                    }
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 根据组合商品的id获取组合商品的库存
     * @param $groupid
     * @return int
     */
    public static function getGroupStock($groupid,$id){
        if(!empty($groupid) && strpos($groupid,',')>0){
            $idsArr =array_filter(explode('|',$groupid));
            $data = array();
            foreach($idsArr as $key=>$val){
                $idarr =array_filter(explode(',',$val));
                if(regex($idarr[0],'number')){
                    $goods =  \Admin\Model\ContentModel::getContentById($idarr[0]);
                    $child['parentid'] = $id;
                    $child['productid'] = $idarr[0];
                    $child['num'] = $idarr[1];
                    self::addGoodsChild($child);//添加商品关系
                    $data[$key]['stock']=intval($goods['stock']/$idarr[1]);
                }
            }
            //按照库存从小到大排序（组合商品的库存由子商品库存数最小的决定）
            $datanew = myArraySort($data,'stock',SORT_ASC);
            return $datanew[0]['stock'];
        }else{
            return 0;
        }
    }

    /**
     * 根据组合商品的id获取组合商品的库存
     * @param $groupid
     * @return int
     */
    public static function getGroupStockByParentId($parentid){
        if(regex($parentid,'number')){
            $data = GoodsGroupModel::getGoodsChild($parentid); //（取出该商品的子商品）
            if(!empty($data)){
                foreach($data as $key=>$val){
                    if($val['productid']){
                        $goods =  \Admin\Model\ContentModel::getContentById($val['productid']);
                        if(!$goods['stock']){
                            $data[$key]['stock'] = 0;
                        }else{
                            $data[$key]['stock']=intval($goods['stock']/$val['num']);
                        }
                    }
                }
                //按照库存从小到大排序（组合商品的库存由子商品库存数最小的决定）
                $datanew = myArraySort($data,'stock',SORT_ASC);
                return $datanew[0]['stock'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public static function delGoodsChild($id){
        if(regex($id,'number')){
            $con['id'] =$id;
            return  M('goods_group')->where($con)->delete();
        }else{
            return false;
        }
    }

    /**
     * 添加组合商品的子商品
     * @param $data
     * @return bool|mixed
     */
    public static function addGoodsChild($data){
        if(empty($data)){
            return false;
        }
        $con['parentid'] = $data['parentid'];
        $con['productid'] = $data['productid'];
        $goods = M('goods_group')->where($con)->find();
        if(!empty($goods)){ // 指定组合商品存在改子商品（使用修改操作）
            return  M('goods_group')->where($con)->save($data);
        }else{
            return  M('goods_group')->add($data);
        }
    }

}

?>