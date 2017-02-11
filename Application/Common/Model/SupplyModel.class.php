<?php
namespace Common\Model;
use Common\Model\ContentModel;
use Home\Model\WeixinModel;
use Think\Log;
use Think\Model;

class SupplyModel extends Model{
    const  NORMAL = 1;

    /**
     * 根据供应商ID获取供应商信息
     * @param $id
     * @param string $filed
     * @return bool|mixed
     */
    public static function getSupplyByid($id,$filed=''){
        if(regex($id,'number')){
            $where['status']= self::NORMAL;
            $where['id']= $id;
            if(empty($filed)){
                return M('supply')->where($where)->find();
            }else{
                return M('supply')->where($where)->field($filed)->find();
            }
        }else{
            return false;
        }
    }

    public static function  getAllSupply(){
        $where['status']=1;
        return M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
    }

}