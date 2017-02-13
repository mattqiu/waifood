<?php
namespace Home\Controller;
use Common\Model\CodeModel;
use Common\Model\DiscountModel;
use Common\Model\UserModel;
use Think\Controller;
class CommonController extends Controller {

    //后期折扣
    public function getDiscount(){
        $amount = I('post.amount');
        if($amount>0){
            $user = UserModel::getUser();
            if(empty($user)){
                apiReturn(CodeModel::ERROR);
            }
            $discount = DiscountModel::getDiscountMoney($amount,$user['id']);
            if(!empty($discount)){
                apiReturn(CodeModel::CORRECT,'',$discount);
            }else{
                apiReturn(CodeModel::ERROR);
            }
        }
    }

    public function delFile(){
        $file = $_REQUEST['file'];
        if($file){
            delfile($file);
        }
        apiReturn(CodeModel::CORRECT);
    }

}
?>