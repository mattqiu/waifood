<?php
namespace Common\Model;
use Think\Model;
class AreaModel extends Model {
    const NORMAL  = 1;//正常；
    const DISABLE = 0;//禁用；
    const DELETE  = -1;//删除；

    /**
     * 添加地区
     * @param $data
     * @return mixed
     */
    public static function addArea($data){
        if(!empty($data)){
            return M('area')->add($data);
        }
        return false;
    }

    public static function getAreaByid($id){
        if(regex($id,'number')){
            return M('area')->find($id);
        }else{
            return false;
        }
    }

    public static function getAreas(){
        $con['status'] = self::NORMAL;
        $order = 'rank desc,id desc';
        return M('area')->where($con)->order($order)->select();
    }

    /**
     * 编辑地区
     * @param $id
     * @param $data
     * @return bool
     */
    public static function modifyArea($id,$data){
        if(regex($id,'number') && !empty($data)){
            $con['id'] = $id;
            $data['updatetime'] = date('Y-m-d H:i:s');
            return M('area')->where($con)->save($data);
        }
        return false;
    }
}

?>