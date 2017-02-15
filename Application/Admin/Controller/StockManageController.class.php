<?php
namespace Admin\Controller;
use Admin\Model\ChannelModel;
use Admin\Model\MaterialModel;
use Admin\Model\StockManageModel;
use Admin\Model\UserModel;
use Common\Model\CodeModel;
use Common\Model\GoodsAttrModel;
use Common\Model\SupplyModel;

/**
 * 库存管理
 * Class StoreManageController
 * @package Admin\Controller
 */
class StockManageController extends BaseController {

    //成品采购单
   public function goodsPurchaseOrder(){
       // 输出供应商列表
       $where=array();
       $where['status']=1;
       $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
       $this->assign ( "supplylist", $list );
       $this->display('finished_product_purchase_order');
   }

    //原料编辑
   public function materialContent(){
       $id = $_REQUEST['id'];
       if(regex($id,'number')){
           $this->assign ( "db", MaterialModel::getMaterialById($id));
       }
       // 输出供应商列表
       $where=array();
       $where['status']=1;
       $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
       $this->assign ( "supplylist", $list );
       $origin = GoodsAttrModel::getGoodAttr(GoodsAttrModel::ORIGIN);
       $storage = GoodsAttrModel::getGoodAttr(GoodsAttrModel::STORAGE);
       $this->assign ( "origin", $origin );
       $this->assign ( "storage", $storage );
       $this->display('material_content');
   }

    //添加或修改原料库
    public function modifyMaterial(){
        $data = I('post.');
        if(!empty($data)){
            $dbdata = M('material')->create($data);
            if(!empty($dbdata)){
                if($dbdata['id']){
                    if(MaterialModel::modifyData($dbdata['id'],$dbdata)){
                        apiReturn(CodeModel::CORRECT,'修改成功');
                    }else{
                        apiReturn(CodeModel::ERROR,'修改失败');
                    }
                }else{
                   if(MaterialModel::createData($dbdata)){
                       apiReturn(CodeModel::CORRECT,'添加成功');
                   }else{
                       apiReturn(CodeModel::ERROR,'添加失败');
                   }
                }
            }else{
                apiReturn(CodeModel::ERROR,M()->getError());
            }
        }else{
            apiReturn(CodeModel::ERROR,'数据不能为空');
        }
    }

    /**
     * 原料列表
     */
    public function ylContent(){
        $row = 9;//C ( 'VAR_PAGESIZE' );
        $count = M('material')->count();
        $page = new  \Think\Page ( $count, $row );
        $list = M('material')->limit($page->firstRow.",".$page->listRows)->order('addtime desc')->select();
        $this->assign("list",$list);
        $this->assign("page",$page->show());
        $this->display('yl_content');
    }

    //原料采购单
    public function materialPurchase(){
        // 输出供应商列表
        $where=array();
        $where['status']=1;
        $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
        $this->assign ( "supplylist", $list );
        $this->display('material_product_purchase_order');
    }

    //原料明细
    public function ylContentLog(){
        $materialid = $_REQUEST['materialid'];
        if(regex($materialid,'number')){
            $row = C ( 'VAR_PAGESIZE' );
            $con['status'] = MaterialModel::COMPLETE;//只查询入库完成的、、状态：0草稿，1待审核，2完成，3取消
            $con['materialid'] = $materialid;
            $count = M('material_log')->where($con)->count();
            $page = new  \Think\Page ( $count, $row );
            $list = M('material_log')->where($con)->limit($page->firstRow.",".$page->listRows)->order('stocktime desc')->select();
            $material = MaterialModel::getMaterialById($materialid);
            $this->assign("title", $material['title']);
            $this->assign("list",$list);
            $this->assign("page",$page->show());
        }
        $this->display('yl_content_log');
    }

    //原料商品列表
    public function ylContentDetail(){
        $row = C ( 'VAR_PAGESIZE' );
        $where = array();
        $searchtype = I ( 'searchtype' );
        $keyword = I ( 'keyword' );
        if($searchtype && $keyword){
            switch ($searchtype) {
                case '1' : $where ['title'] = array ( 'like','%' . $keyword . '%'); break;
                case '2' : if (is_numeric ( $keyword )) { $where ['id'] = $keyword; } break;
            }
        }
        if (!empty($_REQUEST['stime']) && empty($_REQUEST['etime'])) { //如果只有开始时间
            $where['update_time'] = array("egt",$_REQUEST['stime']." 00:00:00");
        }
        if (empty($_REQUEST['stime']) && !empty($_REQUEST['etime'])) { //如果只有结束时间
            $where['update_time'] = array("elt",$_REQUEST['etime']." 23:59:59");
        }
        if(!empty($_REQUEST['stime']) && !empty($_REQUEST['etime'])){  //如果有开始和结束时间
            $where['update_time'] = array(array("egt",$_REQUEST['stime']." 00:00:00"),array("elt",$_REQUEST['etime']." 23:59:59"));
        }

        if ((!isset($_REQUEST['ranktype']) || empty($_REQUEST['ranktype'])) || !empty($_REQUEST['ranktype']) && $_REQUEST['ranktype']==1) {
            $order = 'update_time ';
        }else{
            $order = 'id ';
        }
        if ((!isset($_REQUEST['rank']) || empty($_REQUEST['rank'])) ||!empty($_REQUEST['rank']) && $_REQUEST['rank'] =='desc') {
            $order.='desc';
        }else{
            $order.='asc';
        }

        if ($_REQUEST['supplyid']>0) {
            $where ['supplyid'] = $_REQUEST['supplyid'];
        }
        $count = M('material')->where($where)->count();
        $page = new  \Think\Page ( $count, $row );
        $list = M('material')->limit($page->firstRow.",".$page->listRows)->where($where)->order($order)->select();
        foreach($list as &$val){
            $supply= SupplyModel::getSupplyByid($val['supplyid']);
            $supply2= SupplyModel::getSupplyByid($val['supplyid2']);
            $origin= GoodsAttrModel::getGoodAttrById($val['origin_id']);
            $storage= GoodsAttrModel::getGoodAttrById($val['storage_id']);
            $val['supply'] = $supply['name'];
            $val['supply2'] = $supply2['name'];
            $val['origin'] = $origin['namecn'];
            $val['storage'] = $storage['namecn'];
        }
        $this->assign("list",$list);
        $this->assign("page",$page->show());

        // 输出供应商列表
        $where=array();
        $where['status']=1;
        $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
        $this->assign ( "supplylist", $list );
        $this->display('yl_content_detail');
    }

    public function delCGGoods(){
        $id = I('post.id');
        $orderno = I('post.orderno');
        if(regex($id,'number') && $orderno){
           if(StockManageModel::delCGGoods($id,$orderno)){
               apiReturn(CodeModel::CORRECT);
           }else{
               apiReturn(CodeModel::ERROR,'删除失败');
           }
        }else{
            apiReturn(CodeModel::ERROR,'请刷新重试');
        }
    }

    /**
     *生产管理单
     */
    public function generateOrder(){
        // 输出供应商列表
        $where=array();
        $where['status']=1;
        $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
        $this->assign ( "supplylist", $list );
        $this->display('generate_order');
    }

    public function stockManageOrder(){
        $row = C ( 'VAR_PAGESIZE' );
        $where = '';
        if (!empty($_REQUEST['stime']) && empty($_REQUEST['etime'])) { //如果只有开始时间
            $where.='runtime >= '.$_REQUEST['stime']." 00:00:00 and ";
           // $where['runtime'] = array("egt",$_REQUEST['stime']." 00:00:00");
        }
        if (empty($_REQUEST['stime']) && !empty($_REQUEST['etime'])) { //如果只有结束时间
            $where.='runtime <= '.$_REQUEST['etime']." 23:59:59 and ";
           // $where['runtime'] = array("elt",$_REQUEST['etime']." 23:59:59");
        }
        if(!empty($_REQUEST['stime']) && !empty($_REQUEST['etime'])){  //如果有开始和结束时间
            $where.='runtime >= '.$_REQUEST['stime']." 00:00:00".' and runtime <= '.$_REQUEST['etime']." 23:59:59 and ";
            //$where['runtime'] = array(array("egt",$_REQUEST['stime']." 00:00:00"),array("elt",$_REQUEST['etime']." 23:59:59"));
        }
        if ($_REQUEST['supplyid']>0) {
            $where.='supplyid = '.$_REQUEST['supplyid'].' and ';
          //  $where= ['supplyid'] = $_REQUEST['supplyid'];
        }
        if($_REQUEST['ordertype']){
            $where.='ordertype in ('.implode(',',$_REQUEST['ordertype']).') and ';
//            $where.='ordertype = '.$_REQUEST['ordertype'].' and ';
         //   $where ['ordertype'] = $_REQUEST['ordertype'];
        }else{
            $where.='ordertype in (1,2,3) and ';
            //$where ['ordertype'] = 1;
        }
        if(isset($_REQUEST['status']) && !empty($_REQUEST['status'])){
            $where.='status in ('.implode(',',$_REQUEST['status']).')';
           // $where ['status'] = $_REQUEST['ordertype'];
        }else{
            $where.='status in (0,1) ';
           // $where ['status'] = 0;
        }
        $sql = 'SELECT  count(DISTINCT(orderno)) as tp_count FROM `my_store_manage` WHERE '.$where;
        $data = M ()->query($sql);
        $count = $data[0]['tp_count'];
     //   $count = M('store_manage')->where($where)->group('orderno')->count();
        $page = new  \Think\Page ( $count, $row );
        //$list = M('store_manage')->limit($page->firstRow.",".$page->listRows)->where($where)->order('id desc')->select();
        $filed = '`id`,`runtime`,`orderno`,`ordertype`,`total_fee`,`supplyid`,`supplyid2`,`operator`,`note`,`status`';
        $sql = 'SELECT DISTINCT(orderno),'.$filed.' FROM `my_store_manage` WHERE '.$where .' order by id desc limit '.$page->firstRow.','.$page->listRows;
        $list = M ()->query($sql);
        $newlist  = array();
        foreach($list as $val){
            $supply= SupplyModel::getSupplyByid($val['supplyid']);
            $supply2= SupplyModel::getSupplyByid($val['supplyid2']);
            $val['supply'] = $supply['name'];
            $val['supply2'] = $supply2['name'];
            if(!isset($newlist[$val['orderno']])){
                $newlist[$val['orderno']] = $val;
            }
        }
        $this->assign("list",$newlist);
        $this->assign("page",$page->show());
        // 输出供应商列表
        $user = UserModel::getUsers();
        $list = SupplyModel::getAllSupply();
        $this->assign ( "user", $user );
        $this->assign ( "supplylist", $list );
        $this->display('stock_manage_order');
    }

    //提交成品采购单
    public function subCGOrder(){

        $data = I('post.');
        if(!empty($data)){
            $newdata = StockManageModel::seperateOrder($data);
            $type = strtoupper($data['type']);
            $status = strtoupper($data['status']);
            if($status == 2){//入库
                if(trim(session('adminname')) != 'admin' && trim(session('adminname')) != 'administrator'){
                    apiReturn(CodeModel::ERROR,'权限不足，请联系管理员！'.session('adminname'));
                }
            }
            if(!in_array($type,array(StockManageModel::CP,StockManageModel::YL))){
               // apiReturn(CodeModel::ERROR,'采购商品类型不正确，请刷新重试！');
            }
            if(!$data['orderno']) {
                if (!$count = S('count')) {
                    S('count', 1, 86400);
                    $count = S('count');
                } else {
                    $count++;
                    S('count', $count, 86400);
                }
                $orderno = $type.date('Ymd') .( $count<10?'0'.$count:$count);
            }

            //使用事务保证添加订单，明细成功
            M()->startTrans();
            foreach($newdata as $val){
                $dbdata = M('store_manage')->create($val);
                if($data['orderno']){
                    $orderno = $data['orderno'];
                    if(StockManageModel::COMPLETE == $orderStatus = StockManageModel::getStatusByOrderno($orderno,$dbdata['productid'])){
                        apiReturn(CodeModel::ERROR,'已完成的订单不支持重新修改');
                    }
                    if(false===StockManageModel::modifyOrder($orderno,$dbdata)){
                        M()->rollback();
                        apiReturn(CodeModel::ERROR,'修改失败');
                    }else{
                        //原料修改时入库
                        if($type == StockManageModel::YL  && $status == StockManageModel::COMPLETE) {
                            StockManageModel::addLYToStock($dbdata);
                            MaterialModel::modifyMaterialLogById($orderno,$dbdata['productid'],$status,$dbdata);
                        }
                    }
                }else{
                    if(!StockManageModel::createOrder($dbdata,$orderno)){
                        M()->rollback();
                        apiReturn(CodeModel::ERROR,'添加失败');
                    }
                    if($type == StockManageModel::YL){
                        //原料入库记录
                        if($status == StockManageModel::COMPLETE){
                            //添加记录是直接入库
                            MaterialModel::achatMaterialLog($dbdata, $orderno, $status);
                            StockManageModel::addLYToStock($dbdata);
                        }else{
                            //添加记录非直接入库
                            MaterialModel::achatMaterialLog($dbdata, $orderno, $status);
                        }
                    }
                }
                //成品入库操作
                if($type == StockManageModel::CP && $status == StockManageModel::COMPLETE){
                    StockManageModel::addCpToStock($dbdata);
                }
            }
            //保证没有错误以后提交
            M()->commit();
            apiReturn(CodeModel::CORRECT,'操作成功');
        }else{
            apiReturn(CodeModel::ERROR,'数据为空');
        }
    }

    /**
     * 拆分商品信息并验证
     * @param $dataval
     * @return array
     */
    private function seperateCpOrder($dataval){
        $orderTmp = explode("|", $dataval['order']);
        $data = array();
        unset($dataval['order']);
        foreach($orderTmp as $key=>$val) {
            if (stripos($val, ',')) {
                $ids = explode(",", $val);
                $data[$ids[0]]['productid'] = $ids[0];
                $data[$ids[0]]['title'] = $ids[1];
                $data[$ids[0]]['unit'] = $ids[2];
                $data[$ids[0]]['num'] = $ids[3];
                $data[$ids[0]]['price'] = $ids[4];
                $goods_amount = float_fee(intval($data[$ids[0]]['num']) * floatval($data[$ids[0]]['price']));//商品金额=数量*单价
                if ($goods_amount != floatval($ids[5])) { //验证单个商品总金额
                    apiReturn(CodeModel::ERROR, '商品' . $data[$ids[0]]['title'] . '金额不正确');
                }
                $data[$ids[0]]['note'] = $ids[6];
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
                $data[$ids[0]]['ordertype'] = $dataval['ordertype'];
            }
        }
        $ylamount =float_fee(floatval($dataval['total_amount'])+floatval($dataval['other_fee'])+floatval($dataval['delivery_fee']));
        if($ylamount != floatval($dataval['total_fee'])){
            apiReturn(CodeModel::ERROR,'单据金额=原料金额+运费+杂费');
        }
        return $data;
    }

    /**
     * 生产管理单（添加成品记录，添加原料领用记录）
     */
    public function subGenerateOrder(){
        $data = I('post.');
        if(!empty($data)){
            $newdata = $this->seperateCpOrder($data);
            $type = strtoupper($data['type']);
            $status = strtoupper($data['status']);
            if($status == 2){ //入库
                if(trim(session('adminname')) != 'admin' && trim(session('adminname')) != 'administrator'){
                    apiReturn(CodeModel::ERROR,'权限不足，请联系管理员！！');
                }
            }
            if(!in_array($type,array(StockManageModel::CP,StockManageModel::YL))){
                apiReturn(CodeModel::ERROR,'商品类型不正确，请刷新重试！');
            }
            if(!$data['orderno']) {
                if (!$count = S('count')) {
                    S('count', 1, 86400);
                    $count = S('count');
                } else {
                    $count++;
                    S('count', $count, 86400);
                }
                $orderno = $type.date('Ymd') .( $count<10?'0'.$count:$count);
            }
            //使用事务保证添加订单，明细成功
            M()->startTrans();
            foreach($newdata as $val){
                $dbdata = M('store_manage')->create($val);
                if($data['orderno']){
                    $orderno = $data['orderno'];
                    if(StockManageModel::COMPLETE == $orderStatus = StockManageModel::getStatusByOrderno($orderno,$dbdata['productid'])){
                        M()->rollback();
                        apiReturn(CodeModel::ERROR,'已完成的订单不支持重新修改');
                    }
                    if(false === StockManageModel::modifyOrder($data['orderno'],$dbdata)){
                        M()->rollback();
                        apiReturn(CodeModel::ERROR,'修改失败');
                    }else{
                        //修改原料领用记录
                        if(isset($data['yldata']) && !empty($data['yldata'])){
                            MaterialModel::modifyMaterialLog($data['yldata'],$orderno,$status);
                        }
                    }
                }else{
                    if(!StockManageModel::createOrder($dbdata,$orderno)){
                        M()->rollback();
                        apiReturn(CodeModel::ERROR,'添加失败');
                    }else{
                        //原料领用记录
                        if(isset($data['yldata']) && !empty($data['yldata'])){
                            MaterialModel::receiveMaterialLog($data['yldata'],$orderno,$status);
                        }
                    }
                }
                if($status == StockManageModel::COMPLETE ){ //入库操作
                    StockManageModel::addCpToStock($dbdata);
                }
            }
            //保证没有错误以后提交
            M()->commit();
            apiReturn(CodeModel::CORRECT,'操作成功');
        }else{
            apiReturn(CodeModel::ERROR,'数据为空');
        }
    }

    //导出表格
    public function downOrder(){
        $orderno = $_REQUEST['orderno'];
        $list = StockManageModel::getOrderByOrderno($orderno);
        if(!empty($list)){
            if($list[0]['status'] == StockManageModel::DRAFT){
                $status = '草稿';
            }elseif($list[0]['status'] == StockManageModel::AD_REFERENDUM){
                $status = '待审核';
            }elseif($list[0]['status'] == StockManageModel::COMPLETE){
                $status = '完成';
            }else{
                $status = '取消';
            }
            if($list[0]['ordertype'] == 1){
                $ordertype = '成品采购';
            }elseif($list[0]['ordertype'] == 2){
                $ordertype = '原料采购';
            }elseif($list[0]['ordertype'] == 3){
                $ordertype = '生产管理';
            }
            $data= array();
            foreach($list as $key=>&$val) {
                $supply = SupplyModel::getSupplyByid($val['supplyid']);
                $supply2 = SupplyModel::getSupplyByid($val['supplyid2']);
                $data[$val['id']]['runtime'] = $val['runtime'];
                $data[$val['id']]['orderno'] = $orderno;
                $data[$val['id']]['ordertype'] = $ordertype;
                $data[$val['id']]['total_fee'] = $val['total_fee'];
                $data[$val['id']]['supply'] = $supply['name'];
                $data[$val['id']]['supply2'] = $supply2['name'];
                $data[$val['id']]['operator'] = $val['operator'];
                $data[$val['id']]['status'] = $status;
                $data[$val['id']]['note'] = $val['note'];
            }
        }
        $filename = '采购生产-'.$orderno;
        $title = array('业务时间','单据号','单据类型','单据金额', '供应商','供应商2','创建人','状态','备注');
        downloadExcel($data,$filename,$title,25);
    }

    public function printOrder(){
        $orderno = $_REQUEST['orderno'];
        if(regex($orderno,'require')){
            $list = StockManageModel::getOrderByOrderno($orderno);
            if(!empty($list)){
                if($list[0]['status'] == StockManageModel::DRAFT){
                    $status = '草稿';
                }elseif($list[0]['status'] == StockManageModel::AD_REFERENDUM){
                    $status = '待审核';
                }elseif($list[0]['status'] == StockManageModel::COMPLETE){
                    $status = '完成';
                }else{
                    $status = '取消';
                }
                if($list[0]['ordertype'] == 1){
                    $ordertype = '成品采购';
                }elseif($list[0]['ordertype'] == 2){
                    $ordertype = '原料采购';
                }elseif($list[0]['ordertype'] == 3){
                    $ordertype = '生产管理';
                }
                foreach($list as $key=>&$val){
                    $supply= SupplyModel::getSupplyByid($val['supplyid']);
                    $supply2= SupplyModel::getSupplyByid($val['supplyid2']);
                    $val['supply'] =$supply['name'];
                    $val['supply2'] =$supply2['name'];
                }
                $this->assign ( "ordertype", $ordertype );
                $this->assign ( "status", $status );
                $this->assign ( "order", $list );
            }

        }
        $this->display();
    }

    public function getOrderInfo(){
        if(isset($_REQUEST['orderno']) && $_REQUEST['orderno']){
            $list = StockManageModel::getOrderByOrderno($_REQUEST['orderno']);
            apiReturn(CodeModel::CORRECT,'',$list);
        }else{
            apiReturn(CodeModel::ERROR);
        }
    }

    public function getGenerateOrderInfo(){
        if(isset($_REQUEST['orderno']) && $_REQUEST['orderno']){
            $list = StockManageModel::getOrderByOrderno($_REQUEST['orderno']);
            $logs = MaterialModel::getMaterialLogByOrderno($_REQUEST['orderno']);
            apiReturn(CodeModel::CORRECT,'',array('list'=>$list,'logs'=>$logs));
        }else{
            apiReturn(CodeModel::ERROR);
        }
    }
}
?>