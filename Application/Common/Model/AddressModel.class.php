<?php
namespace Common\Model;
use Think\Model;

class AddressModel extends Model
{
    const  DEFAULT_ADDRESS = 1;//默认地址
    const  OTHER_ADDRESS = 0;


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
    public static function modifyShoppingAddress($data,$userId=''){
        if(empty($data) || !regex($userId,'number')){
            return false;
        }
        //如果提交设置了默认地址,先清除以前默认地址
        if($data['isdefault'] == self::DEFAULT_ADDRESS){
            $savedata['isdefault'] = self::OTHER_ADDRESS;
            self::modifyDefaultAddress($userId,$savedata);
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
     * @return bool
     */
    public static function getUserAddress($userid){
        if(regex($userid,'number')){
            $con['userid'] =  $userid;
            $order = 'isdefault desc,addtime desc';
            return D( 'address' )->where($con)->order ( $order )->select ();
        }
       return false;
    }

    public static function getUserDefaultAddress($userid){
        if(regex($userid,'number')){
            $con['userid'] =  $userid;
            $con['isdefault'] =  self::DEFAULT_ADDRESS;
            return D( 'address' )->where($con)->find();
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

    /**根据地址id获取用户收货地址
     * @param $id 地址id
     * @param $userid
     * @return bool
     */
    public static function modifyDefaultAddress($userid,$data){
        if(regex($userid,'number') && !empty($data)){
            $con['userid'] =  $userid;
            $con['isdefault'] = self::DEFAULT_ADDRESS;
            return D( 'address' )->where($con)->save ($data);
        }
       return false;
    }

    /**修改用户地址
     * @param $addrid
     * @param $data
     * @return bool
     */
    public static function modifyAddr($addrid,$data){
        if(regex($addrid,'number') && !empty($data)){
            $con['id'] = $addrid;
            return M('address')->where($con)->save($data);
        }else{
            return false;
        }
    }

    /**
     * 根据条件更改订单
     * @param $con
     * @param $data
     * @return bool
     */
    public static function modifyAddrByCon($con,$data){
        if($con && !empty($data)){
            return M ( 'address' )->where($con)->save($data );
        }
        return false;
    }
}