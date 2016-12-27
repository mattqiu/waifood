<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

use Common\Model\ActiviModel;
use Common\Model\CodeModel;
use Common\Model\ContentModel;
use Common\Model\GoodsAttrModel;

class ProductController extends BaseController {

    public function channel($id = null) {
		$where = array ();
		$where ['status'] = 1;
		$where ['pid'] = $id;
		$order = 'sort asc';
		$db = M ( 'channel' )->order ( $order )->where ( $where )->select ();

		$this->display ();
	}
	/**
	 * 产品列表
	 */
	public function lists($id = null, $order = 1,$sort='desc') {
        $orderstr = 'sort desc';
        switch ($order) {
            case 3 :
                $orderstr = 'sold '.$sort;
                break;
            case 2 :
                $orderstr = 'price '.$sort;
                break;
        }
        if($_REQUEST['group'] == ContentModel::NEW_ARRIVAL){
            $this->assign ( 'title', 'New Arrvial' );
            $list  =  ContentModel::getGroupContent(ContentModel::NEW_ARRIVAL,$orderstr);
        }else if($_REQUEST['group'] == ContentModel::RECOMMEND){
            $this->assign ( 'title', 'Recommend');
            $list  =  ContentModel::getGroupContent(ContentModel::RECOMMEND,$orderstr);
        }else if($_REQUEST['group'] == ContentModel::PROMOTION){
            $this->assign ( 'title', 'Promotion');
            $list  =  ContentModel::getGroupContent(ContentModel::PROMOTION,$orderstr);
        }else{
            $keyword=parse_param($_REQUEST['keyword'],true);
            $where = array ();
            $where ['status'] = 1;
            if($id > 0){
                $where['sortpath'][]= array('like','%,'.$id.',%');
            }elseif($keyword){
                $this->assign ( 'title', $keyword );
                $where['_string'] = ' (title like "%'.$keyword.'%")  OR ( keywords like "%'.$keyword.'") ';
                //   $this->addKeyword($keyword);
            }else{
                $where['sortpath'][]= array('like','%,2,%');
            }
            $field =  'id,title,indexpic,price,price1,description,unit,storage,origin,origin_id,brand,stock';
            $list = M ( "content" )->field ($field)->where ( $where )->order ( $orderstr )->select ();
        }
        foreach($list as &$val){
            if(isset($val['origin_id']) && $val['origin_id']){
                $orogin = GoodsAttrModel::getGoodAttrById($val['origin_id']);//获取产地信息
                $val['origin'] = $orogin['name'];
            }
        }
        $this->assign ( "list", $list );
        $subchannel = M ( 'channel' )->field ( 'id,pid,name' )->where ( 'pid=' . $id )->select ();
		if (count ( $subchannel ) == 0) {
			$pid = M ( 'channel' )->where ( 'id=' . $id )->getField ( 'pid' );
			$subchannel = M ( 'channel' )->field ( 'id,pid,name' )->where ( 'pid=' . $pid )->select ();
		}


		$this->assign ( 'subchannel', $subchannel );
		$this->assign ( 'order', $order );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'id', $id );
		$this->display ();
	}
	
	/**
	 * 产品详细
	 * 
	 * @param number $id        	
	 */
	public function view($id = 0) {
		$where = array ();
		$where ['status'] = 1;
		$db = M ( "content" )->where ( $where )->find ( $id );

		if ($db) {
			$arr = str2arr ( cookie ( 'view_history' ) );
			$arr = arr2clr ( $arr );
			if (! in_array ( $id, $arr )) {
				$arr [] = $id;
				cookie ( 'view_history', arr2str ( $arr ) );
			}
            if(isset($db['origin_id']) && $db['origin_id']){
                $orogin = GoodsAttrModel::getGoodAttrById($db['origin_id']);//获取产地信息
                $db['origin'] = $orogin['name'];
            }
            if(isset($db['storage_id']) && $db['storage_id']){
                $storage = GoodsAttrModel::getGoodAttrById($db['storage_id']);//获取保存方法信息
                $db['storage'] = $storage['name'];
            }

			if(strpos($db['images'],'.')){
			    $gallery=get_imgs ($db ['images'] );
			}else{
			    $gallery=array($db['indexpic']);
			}
			$credit = $db ['price'] * C ( 'config.CREDIT_RATE' );
			$this->assign ( "db", $db );
			$this->assign ( "gallery", $gallery );
			$this->assign ( 'credit', $credit );
			$subchannel = M ( 'channel' )->field ( 'id,pid,name' )->where ( 'pid=' . $db ['pid'] )->select ();
			if (count ( $subchannel ) == 0) {
				$pid = M ( 'channel' )->where ( 'id=' . $db ['pid'] )->getField ( 'pid' );
				$subchannel = M ( 'channel' )->field ( 'id,pid,name' )->where ( 'pid=' . $pid )->select ();
			}
			$backrate=0;
			if(get_data(get_userid(),'member','usertype')==2){
				$backrate =$db['price']*$db['backrate'];
			} 
			$this->assign('backrate',to_price($backrate));
			$this->assign ( 'subchannel', $subchannel );
		} else {
			$this->error ( 'Sorry, this product never exists.' );
		}
		$this->display ();
	}
	
	public function comment($id=0){
		$where = null;
	  	$where ['status'] = 1;  
	  	$where ['productid'] = $id; 
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = 100;//C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "comment" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
		$list = $rs->select ();
 
		$this->assign ( "list", $list?$list:null );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( 'page', $page->showm () );
		} 
		$this->display ();
		
	}

    /**
     * 获取活动折扣
     */
    public function getActiviDiscount(){
        $amount = I('post.amount');
        if($amount){
            $discount = ActiviModel::getActiviDiscount($amount);
            apiReturn(CodeModel::CORRECT,'',$discount);
        }else{
            apiReturn(CodeModel::CORRECT,'',0);
        }
    }
}
?>