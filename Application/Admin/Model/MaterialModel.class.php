<?php 
namespace Admin\Model;
use Common\Model\CodeModel;
use Think\Model;
 
class MaterialModel extends Model{
    const RECEIVE = 1; //领用
    const ACHAT = 2; //采购
    const DRAFT = 0;//0草稿
    const AD_REFERENDUM = 1;//1待审核
    const COMPLETE = 2;//2完成，
    const CANCEL = 3;//3取消
    // 自动验证设置
    protected $_validate = array(
        array('title','require',"商品名不能为空",Model::MUST_VALIDATE),
    );

    public static function getMaterial(){
        return M('material')->select();
    }

    public static function getMaterialById($id){
        if(!regex($id,'number')){
            return false;
        }
        return M('material')->find($id);
    }

    /**
     * 添加数据
     * @param $data
     * @return bool|mixed
     */
    public static function createData($data){
        if(!empty($data)){
           return M('material')->add($data);
        }else{
            return false;
        }
    }

    /**
     * 修改数据
     * @param $goodsId
     * @param $data
     * @return bool|\Model
     */
    public static function modifyData($goodsId,$data){
        if(regex($goodsId,'number') && !empty($data)){
            $con['id'] = $goodsId;
            $data['update_time'] = date('Y-m-d H:i:s');
            return  M('material')->where($con)->save($data);
        }else{
            return false;
        }
    }

    /**
     * 获取一个订单的原料记录
     * @param $orderno
     * @return bool|mixed
     */
    public static function getMaterialLogByOrderno($orderno){
        if(regex($orderno,'require')){
            $con['l.orderno'] = $orderno;
            return M('material_log')->alias('l')->join('my_material as m on l.materialid = m.id')->field('l.*,m.title,m.unit,m.stock,m.stock_fee,m.price')->where($con)->select();
        }else{
            return false;
        }
    }

    /**
     * 领用原料记录
     * @param $data
     * @param $orderno
     * @param $status
     */
    public static function receiveMaterialLog($data,$orderno,$status){
        if(!empty($data) && regex($orderno,'require')){
            $orderTmp = explode("|", $data['order']);
            $newdata = array();
            foreach($orderTmp as $key=>$val) {
                if (stripos($val, ',')) {
                    $ids = explode(",", $val);
                    $newdata['materialid'] = $ids[0];
                    $newdata['num'] = $ids[1];
                    $newdata['amount'] = $ids[2];
                    $newdata['orderno'] = $orderno;
                    $newdata['operator'] = $_SESSION['adminname'];
                    $newdata['type'] = self::RECEIVE;
                    $newdata['status'] = $status;
                   if(!M('material_log')->add($newdata)){
                       GLog('add material log','添加商品：ID'. $ids[0].'失败');
                   }elseif($status == self::COMPLETE){ //完成状态
                       $con1['id'] = $newdata['materialid'];
                       M('material')->where($con1)->setDec('stock', $newdata['num'] );//减库存
                       M('material')->where($con1)->setDec('stock_fee', $newdata['amount'] );//减库存金额
                   }elseif($status == self::CANCEL){//作废状态
                       $con2['id'] = $newdata['materialid'];
                       M('material')->where($con2)->setInc('stock', $newdata['num'] );//返回库存
                       M('material')->where($con2)->setInc('stock_fee', $newdata['amount'] );//返回库存金额
                   }
                }
            }
        }
    }

    /**
     * 修改原料记录
     * @param $data
     * @param $orderno
     * @param $status
     */
    public static function modifyMaterialLog($data,$orderno,$status){
        if(!empty($data) && regex($orderno,'require')){
            $orderTmp = explode("|", $data['order']);
            $savedata = array();
            foreach($orderTmp as $key=>$val) {
                if (stripos($val, ',')) {
                    $ids = explode(",", $val);
                    $con['materialid'] =  $ids[0];
                    $con['orderno'] =  $orderno;
                    $savedata['num'] = $ids[1];
                    $savedata['amount'] = $ids[2];
                    $savedata['status'] = $status;
                    if($status == self::COMPLETE){ //入库时修改入库时间
                        $savedata['stocktime'] = date('Y-m-d H:i:s');
                    }
                    if(M('material_log')->where($con)->find()){
                        if(!M('material_log')->where($con)->save($savedata)){
                            GLog('add material log','添加商品：ID'. $ids[0].'失败');
                        }elseif($status == self::COMPLETE){ //完成状态
                            $con1['id'] =  $ids[0];
                            M('material')->where($con1)->setDec('stock', $savedata['num'] );//减库存
                            M('material')->where($con1)->setDec('stock_fee', $savedata['amount'] );//减库存金额
                        }
                    }else{ //如果提交的修改中有新增原料
                        MaterialModel::receiveMaterialLog($data,$orderno,$status);
                    }
                }
            }
        }
    }

    /**
     * 修改原料记录
     * @param $data
     * @param $orderno
     * @param $status
     */
    public static function modifyMaterialLogById($orderno,$materialid,$status,$data=array()){
        if(regex($orderno,'require') && regex($materialid,'require')){
            $con['materialid'] = $materialid;
            $con['orderno'] =  $orderno;
            $materiallog = M('material_log')->where($con)->find();
            if($materiallog){
                $savedata['status'] = $status;
                if($status == self::COMPLETE){ //入库时修改入库时间
                    $savedata['stocktime'] = date('Y-m-d H:i:s');
                }
                if(false === M('material_log')->where($con)->save($savedata)){
                    GLog('add material log','添加商品：ID'. $materialid.'失败');
                }else{
                    $con1['id'] = $materialid;
                    M('material')->where($con1)->setDec('stock', $savedata['num'] );//减库存
                    M('material')->where($con1)->setDec('stock_fee', $savedata['amount'] );//减库存金额
                }
            }else{
                MaterialModel::achatMaterialLog($data, $orderno, $status);
            }
        }else{
            return false;
        }
    }

    /**
     * 采购入库原料记录
     * @param $data
     * @param $orderno
     * @param $status
     */
    public static function achatMaterialLog($data,$orderno,$status){
        if(!empty($data) && regex($orderno,'require')){
            $newdata = array();
            $newdata['materialid'] =$data['productid'];
            $newdata['num'] = $data['num'];
            $newdata['amount'] =$data['amount'];
            $newdata['orderno'] = $orderno;
            $newdata['operator'] = $_SESSION['adminname'];
            $newdata['type'] = self::ACHAT;
            $newdata['status'] = $status;
            if(!M('material_log')->add($newdata)){
                GLog('add material log','添加商品：ID'.  $newdata['materialid'].'失败');
            }elseif($status == self::COMPLETE){ //完成状态
                $con1['id'] = $newdata['materialid'];
                M('material')->where($con1)->setInc('stock', $newdata['num'] );//加库存
            }/*elseif($status == self::CANCEL){//作废状态
                $con2['id'] = $newdata['materialid'];
                M('material')->where($con2)->setDec('stock', $newdata['num'] );//返回减库存
            }*/
        }
    }
}
