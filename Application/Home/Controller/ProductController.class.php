<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

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
		$where = array ();
		$where ['status'] = 1;
		if(isset($id)){
    		$where['sortpath'][]= array('like','%,'.$id.',%');
    	}else{ 
    		$where['sortpath'][]= array('like','%,2,%');
    	}
		$orderstr = 'sort desc';
		switch ($order) {
			case 3 :
				$orderstr = 'sold '.$sort;
				break;
			case 2 :
				$orderstr = 'price '.$sort;
				break; 
		}
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "content" )->field ( 'id,title,indexpic,price,price1,description,unit,storage,origin,brand' )->where ( $where )->order ( $orderstr )->page ( $p, $row );
		$list = $rs->select ();
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
			$page->setConfig ( 'prev', 'prev' );
			$page->setConfig ( 'next', 'next' );
			$this->assign ( 'page', $page->showm () );
		}
		
		$subchannel = M ( 'channel' )->field ( 'id,pid,name' )->where ( 'pid=' . $id )->select ();
		if (count ( $subchannel ) == 0) {
			$pid = M ( 'channel' )->where ( 'id=' . $id )->getField ( 'pid' );
			$subchannel = M ( 'channel' )->field ( 'id,pid,name' )->where ( 'pid=' . $pid )->select ();
		}
		$this->assign ( 'subchannel', $subchannel );
		$this->assign ( 'order', $order );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'id', $id );
		$this->assign ( 'title', get_data( $id,'channel' ) );
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
			
			if($db['images']){
			    $gallery=get_imgs ($db ['images'] );
			}else{
			    $gallery=array($db['indexpic']);
			}
			$credit = $db ['price'] * C ( 'config.CREDIT_RATE' );
			$this->assign ( "db", $db );
			$this->assign ( "gallery", $gallery );
			$this->assign ( 'title', $db ['title'] );
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
}
?>