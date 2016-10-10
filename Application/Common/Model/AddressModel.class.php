<?php
namespace Common\Model;
use Think\Model;

class AddressModel extends Model
{
    protected $_validate = array(
        array('username', 'require', '用户名不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('username', '', '用户名已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH)
    );

    /**
     * 添加或修改收货人地址
     * @param $data
     * @param string $userId
     * @return bool
     */
    public static function addShoppingAddress($data,$userId=''){
        if(empty($data) || !regex($userId,'number')){
            return false;
        }
        if(isset($data['id']) &&  $data['id']>0){//修改用户收货地址
            $con['userid'] = $userId;
            $con['id'] = $data['id'];
            $rs = D ( "address" )->where($con)->save($data);
            if($rs!==false){
                return true;
            }else{
                return false;
            }
        }else{//添加用户收货地址
            $data['userid'] = $userId;
            $rs = D ( "address" )->add($data);
            if($rs){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * 获取用户收货地址
     * @param $userid
     */
    public static function getUserAddress($userid){
        if(regex($userid,'number')){
            $con['userid'] =  $userid;
            $order = 'isdefault desc,id desc';
            return D( 'address' )->where($con)->order ( $order )->select ();
        }
       return false;
    }

    /**根据地址id获取用户收货地址
     * @param $id 地址id
     * @param $userid
     * @return bool
     */
    public static function getUserAddressById($id,$userid){
        if(regex($id,'number')){
            $con['id'] =  $id;
            $con['userid'] =  $userid;
            return D( 'address' )->where($con)->find ();
        }
       return false;
    }

}