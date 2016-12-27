<?php
namespace Common\Model;
use Think\Model;
class GoodsAttrModel extends Model {
    const NORMAL  = 1;//正常；
    const DISABLE = 1;//禁用；
    const DELETE  = -1;//删除；
    const ORIGIN  = 1;//产地
    const STORAGE  = 2;//产地

    /**获取商品属性
     * @param $type
     * @return bool|mixed
     */
    public static function getGoodAttr($type){
        if($type){
            $con['status'] = array('neq',self::DELETE);
            $con['type'] = $type;
            return M('goods_attr')->where($con)->select();
        }
        return false;
    }

    public static function getGoodAttrById($id){
        if(regex($id,'number')){
            return M('goods_attr')->find($id);
        }
        return false;
    }

    /**
     * 添加产地
     * @param $data
     * @return mixed
     */
    public static function addGoodsAttr($data){
        if(isset($data['type']) && $data['type']){
            return M('goods_attr')->add($data);
        }
        return false;
    }

    /**
     * 编辑产地
     * @param $id
     * @param $data
     * @return bool
     */
    public static function modifyGoodsAttr($id,$data){
        if(regex($id,'number') && isset($data['type']) && $data['type']){
            $con['id'] = $id;
            return M('goods_attr')->where($con)->save($data);
        }
        return false;
    }
}

?>