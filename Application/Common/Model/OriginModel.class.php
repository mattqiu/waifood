<?php
namespace Common\Model;
use Think\Model;
class OriginModel extends Model {
    const NORMAL  = 1;//正常；
    const DISABLE = 1;//禁用；
    const DELETE  = -1;//删除；


    public static function getAllOrigin(){
        $con['status'] = array('neq',self::DELETE);
        return M('origin')->where($con)->select();
    }

    /**
     * 添加产地
     * @param $data
     * @return mixed
     */
    public static function addOrigin($data){
        if(!empty($data)){
            return M('origin')->add($data);
        }
        return false;
    }

    /**
     * 编辑产地
     * @param $id
     * @param $data
     * @return bool
     */
    public static function modifyOrigin($id,$data){
        if(regex($id,'number') && !empty($data)){
            $con['id'] = $id;
            return M('origin')->where($con)->save($data);
        }
        return false;
    }
}

?>