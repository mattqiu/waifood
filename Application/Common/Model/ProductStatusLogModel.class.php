<?php
namespace Common\Model;
use Think\Model;

class ProductStatusLogModel extends Model
{
    const UPTYPE_ADMIN = 1;//人工下架
    const UPTYPE_AUTO = 2;//自动下架
	public static function addProductStatusLog($data){
        if(!empty($data)){
            return M('product_status_log')->add($data);
        }else{
            return false;
        }
    }
}