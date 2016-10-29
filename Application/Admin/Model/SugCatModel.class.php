<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Admin\Model;

use Think\Model;

class SugCatModel extends Model {
    const DEL =-2;//删除
    const DISABLE =-1;//禁用
    const NORMAL =0;//正常

    /**
     * 获取非删除的货源
     * @return mixed
     */
    public static function getCat(){
        $con['status'] = array('neq',self::DEL);
        $order = 'rank desc,createtime asc';
        return  M('sugcat')->where($con)->order($order)->select();
    }

    /**添加货源类型
     * @param $name
     * @return bool
     */
    public static function addCat($name){
        if(!$name){
            return false;
        }
        $data['cartname'] = $name;
        if( M('sugcat')->add($data)){
            return true;
        }else{
            return false;
        }
    }

}
