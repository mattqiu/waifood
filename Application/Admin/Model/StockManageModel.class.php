<?php 
namespace Admin\Model;
use Common\Model\CodeModel;
use Think\Model;
 
class StockManageModel extends Model{
    const NEW_PRODUCT = 2;
    const CP = 'CP'; //成品
    const YL = 'YL';//原料
    const SC = 'SC';//生产
    const NEWGOOD = '2';//新品
    const DRAFT = 0;//0草稿
    const AD_REFERENDUM = 1;//1待审核
    const COMPLETE = 2;//2完成，
    const  CANCEL = 3;//3取消


    // 自动验证设置
    protected $_validate = array(
        array('title','require',"商品名不能为空",Model::MUST_VALIDATE),
        array('price','require',"商品单价不能为空",Model::MUST_VALIDATE),
        array('num','number',"商品数量必须为数字",Model::MUST_VALIDATE),
    );

    /**
     * 添加数据
     * @param $data 数据
     * @return bool|mixed
     */
    public static function createOrder($data,$orderno){
        if(!$orderno){
            return false;
        }
        if(!empty($data)){
            $data['orderno'] = $orderno ;
            $data['addtime'] = date('Y-m-d H:i:s');
            return M('store_manage')->add($data);
        }else{
            return false;
        }
    }

    /**
     * 获取状态
     * @param $orderno
     * @return bool
     */
    public static function getStatusByOrderno($orderno,$productid){
        if(regex($orderno,'require')){
            $con['orderno'] = $orderno;
            $con['productid'] = $productid;
            $data =M('store_manage')->where($con)->find();
            return $data['status'];
        }else{
            return false;
        }
    }

    /**
     * 获取
     * @param $orderno
     * @return bool
     */
    public static function getOrderByOrderno($orderno){
        if(regex($orderno,'require')){
            $con['orderno'] = $orderno;
            $order = M('store_manage')->where($con)->select();
            if(!empty($order)){
                foreach($order as &$val){
                    if(!is_date($val['createtime'])){
                        $val['createtime'] = '';
                    }
                    if(!is_date($val['dietime'])){
                        $val['dietime'] = '';
                    }
                }
                return $order;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 修改
     * @param $orderno
     * @param $data
     * @return bool
     */
    public static function modifyOrder($orderno,$data){
        if(!empty($data) && regex($orderno,'require')){
            $con['orderno'] = $orderno;
            $con['productid'] = $data['productid'];
            if(M('store_manage')->where($con)->find()){
                return M('store_manage')->where($con)->save($data);
            }else{
                //如果提交的修改中有新增成品
                StockManageModel::createOrder($data,$orderno);
            }
        }else{
            return false;
        }
    }

    /**删除指定商品
     * @param $id
     * @return mixed
     */
    public static function delCGGoods($id,$orderno){
        $con['productid'] = $id;
        $con['orderno'] = $orderno;
        return M('store_manage')->where($con)->delete();
    }

    /**
     * 拆分商品信息并验证
     * @param $dataval
     * @return array
     */
    public static function seperateOrder($dataval){
        $orderTmp = explode("|", $dataval['order']);
        $data = array();
        unset($dataval['order']);
        $total_amount = 0;
        $goods_amount = 0;
        foreach($orderTmp as $key=>$val) {
            if (stripos($val, ',')) {
                $ids = explode(",", $val);
                if($ids[2] != self::NEW_PRODUCT){ //如果商品是新品，商品没有商品id
                    $data[$ids[0]]['productid'] = $ids[0];
                }elseif(regex($ids[0],'number')){
                    apiReturn(CodeModel::ERROR,  '商品' . $data[$ids[0]]['title'] . '不可以更改为当前类型');
                }
                $data[$ids[0]]['title'] = $ids[1];
                $data[$ids[0]]['goodtype'] = $ids[2];
                $data[$ids[0]]['unit'] = $ids[3];
                $data[$ids[0]]['num'] = $ids[4];
                $data[$ids[0]]['price'] = $ids[5];
                $goods_amount = float_fee(intval($data[$ids[0]]['num']) * floatval($data[$ids[0]]['price']));//商品金额=数量*单价
                if ($goods_amount != float_fee($ids[6])) { //验证单个商品总金额
                    apiReturn(CodeModel::ERROR,'商品' . $data[$ids[0]]['title'] . '金额不正确');
                }
                $data[$ids[0]]['amount'] = $ids[6];
                $data[$ids[0]]['true_num'] = $ids[7];
                $data[$ids[0]]['true_price'] = $ids[8];
                $data[$ids[0]]['true_amount'] = $ids[9];
                $data[$ids[0]]['createtime'] = $ids[10];
                $data[$ids[0]]['leveltime'] = $ids[11];
                $data[$ids[0]]['dietime'] = $ids[12];
                $data[$ids[0]]['barcode'] = $ids[13];

                $data[$ids[0]]['orderno'] = $dataval['orderno'];
                $data[$ids[0]]['operator'] = $dataval['operator'];
                $data[$ids[0]]['runtime'] = $dataval['runtime'];
                $data[$ids[0]]['supplyid'] = $dataval['supplyid'];
                $data[$ids[0]]['supplyid2'] = $dataval['supplyid2'];
                $data[$ids[0]]['total_amount'] = $dataval['total_amount'];
                $data[$ids[0]]['total_fee'] = $dataval['total_fee'];
                $data[$ids[0]]['delivery_fee'] = $dataval['delivery_fee'];
                $data[$ids[0]]['other_fee'] = $dataval['other_fee'];
                $data[$ids[0]]['status'] = $dataval['status'];
                $data[$ids[0]]['note'] = $dataval['note'];
                $data[$ids[0]]['ordertype'] = $dataval['ordertype'];
                $total_amount += $goods_amount;
            }
        }
        if($total_amount != floatval($dataval['total_amount'])){
            apiReturn(CodeModel::ERROR,'商品总金额不正确');
        }
        $total_fee = floatval($total_amount +$dataval['other_fee']+$dataval['delivery_fee']);
        if($total_fee != $dataval['total_fee']){
            apiReturn(CodeModel::ERROR,'单据总金额不正确');
        }
        return $data;
    }

    /**
     * 成品入库操作
     * @param $data
     * @return bool
     */
    public static function addCpToStock($data){
        if(!empty($data)){
            if($data['productid']){
                $good = \Admin\Model\ContentModel::getContentById($data['productid']);
                if(!empty($good)){
                    $saveData['cost_fee'] = $data['true_price']>0?$data['true_price']: $data['price'];
                    $saveData['stock'] = intval($good['stock']) + intval($data['true_num']>0?$data['true_num']:$data['num']);
                    $rs =  \Admin\Model\ContentModel::modifyContent($data['productid'],$saveData);
                    if($rs){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else if($data['title'] && $data['goodtype'] == self::NEWGOOD){
                //入库操作时，商品是新品时，执行添加商品操作
                $adddata['title'] = $data['title'];
                $adddata['cost_fee'] = $data['true_price']>0?$data['true_price']: $data['price'];
                $adddata['stock'] = intval($data['true_num']>0?$data['true_num']:$data['num']);
                $adddata['unit'] = $data['unit'];
                $adddata['expire_date'] = $data['expire_date'];
                $adddata['addfrom'] = 1;
                $adddata['status'] = 0;
                $rs =  \Admin\Model\ContentModel::addContent($adddata);
                if($rs){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 原料入库
     * @param $data
     * @return bool
     */
    public static function addLYToStock($data){
        if(!empty($data)){
            if($data['productid']){
                $good = MaterialModel::getMaterialById($data['productid']);
                if(!empty($good)){
                    $saveData['price'] =$data['true_price']>0?$data['true_price']: $data['price'];
                    $saveData['stock'] = floatval($good['stock']) + floatval($data['true_num']>0?$data['true_num']:$data['num']);
                    $saveData['warranty'] = $good['dietime'];
                    $rs = MaterialModel::modifyData($data['productid'],$saveData);
                    if($rs){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


}
