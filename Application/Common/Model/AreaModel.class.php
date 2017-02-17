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

    /**
     * 编辑地区
     * @param $id
     * @param $data
     * @return bool
     */
    public static function modifyArea($id,$data){
        if(regex($id,'number') && !empty($data)){
            $con['id'] = $id;
            return M('area')->where($con)->save($data);
        }
        return false;
    }
}

?>