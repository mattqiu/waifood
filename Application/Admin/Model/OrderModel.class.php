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

class OrderModel extends Model {
    const  DRAFT = 0;//
    const   CONFIRMED  = 1;//
    const    DELIVERING  = 2;//
    const    COMPLETED = 3;//
    const    CANCELLED = 4;//


    public static function getMember(){
        $con['_string'] = "address is null or  address =''";
        $re =  M('member')->where($con)->select();
        $ids='';
        foreach($re as $key=>$val){
            $ids .= ','.$val['id'];
        }
        $con1['_string'] = "userid not in (".substr($ids,1).")";//删除用户有地址 的地址表数据
        $rs = M('address')->where($con1)->delete();
    }


    public static function addrmember(){
        $con['_string'] = "address is not null and address !=''";
        $re =  M('member')->where($con)->select();
        foreach($re as $key=>$val){
           // $con1['']
            $data['userid'] = $val['id'];
            $data['username'] = $val['username'];
            $data['sex'] = $val['sex'];
            $data['telephone'] = $val['telephone'];
            $data['address'] = $val['address'];

            M('address')->add($data);

        }

    }




}
