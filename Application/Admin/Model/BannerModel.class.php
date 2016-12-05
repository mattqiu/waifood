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

class BannerModel extends Model {
    const PC_BANNER = 1;//网站banner
    const WX_BANNER = 2;//微信banner
    const NORMAL = 1;//状态启用

    /**
     * 根据类型获取banner
     * @param $type
     * @return bool
     */
    public static function getBannerByType($type,$state=self::NORMAL){
        if(regex($type,'number')){
            $con['type'] = $type;
            if($state == self::NORMAL){
                $con['status'] = $state;
            }
            return D('banner')->where($con)->order('id desc')->select();
        }else{
            return false;
        }
    }

    /**添加banner
     * @param $data
     * @return bool|mixed
     */
    public static function addBanner($data){
        if(!empty($data)){
            return D('banner')->add($data);
        }else{
            return false;
        }
    }

    /**修改banner
     * @param $id
     * @param $data
     * @return bool
     */
    public static function modifyBanner($id,$data){
        if(regex($id,'number') && !empty($data)){
            $con['id'] = $id;
            return D('banner')->where($con)->save($data);
        }else{
            return false;
        }
    }

}
