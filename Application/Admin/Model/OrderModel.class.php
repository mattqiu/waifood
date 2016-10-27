<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Common\Model\AddressModel;
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
        //获取用户地址不为空的
        $con['_string'] = "address is not null and address !=''";
        $user =  M('member')->where($con)->select();
        $hasDefault = 0;
        $ids = array(574,714,1813,431,1478);
        foreach($user as $key=>$val){
            if(in_array($val['id'],$ids)){
            //用户有默认地址的,将用户地址更新到默认地址中
            if($daddr = AddressModel::getUserDefaultAddress($val['id'])){
                $con['id'] = $daddr['id'];
                $con['userid'] = $val['id'];
                $data['address'] = $val['address'];
//                echo '----de----<br>';
//                dump($data);
//                dump($con);
//                echo '----de----<br>';continue;
                M('address')->where($con)->save($data);
                M('address')->where('id !='.$daddr['id'] .' and userid='. $val['id'])->delete($data);//删除其他的
            }else if( $addr = AddressModel::getUserAddress($val['id'])) {//获取用户地址
                if (count($addr) > 1) {//有多条数据
                    $con1['id'] =$addr[0]['id'];
                    $con1['userid'] = $val['id'];
                    $data['address'] = $val['address'];
                    $data['isdefault'] = 1;
//                    echo '----11----<br>';
//                    dump($addr);
//                    dump($con1);
//                    echo '----11----<br>';continue;
                    M('address')->where($con1)->save($data);//去出用户最近添加的一条地址,修改地址并设为默认
                    M('address')->where('id !='.$addr[0]['id'] .' and userid='. $con1['userid'])->delete($data);//删除其他的
                }else{
                    $con1['userid'] = $val['id'];
                    $data['address'] = $val['address'];
                    $data['isdefault'] = 1;
//                    echo '----111----<br>';
//                    dump($addr);
//                    dump($con1);
//                    echo '----11----<br>';continue;
                    M('address')->where($con1)->save($data);
                }
            }
        }
        }

    }
}
