<?php

namespace Admin\Controller;

use Admin\Model\ContentModel;
use Admin\Model\SugCatModel;
use Common\Model\CodeModel;
use Common\Model\DiscountModel;
use Common\Model\GoodsGroupModel;
use Common\Model\GoodsAttrModel;
use Common\Model\OrderModel;
use Common\Model\ProductStatusLogModel;

class CmsController extends BaseController {
	public function index() {
	}
	/**
	 * 智能选餐类别管理
	 */
	public function magictype(){
		$ctrl = A('Admin/Magic');
		$ctrl->magictype();
	}
	public function addMagictype(){
		$ctrl = A('Admin/Magic');
		$ctrl->addMagictype();
	}
	public function editMagictype(){
		$ctrl = A('Admin/Magic');
		$ctrl->editMagictype();
	}
	
	public function deleteMagictype(){
		$ctrl = A('Admin/Magic');
		$ctrl->deleteMagictype();
	}
	
	/**
	 * 智能选餐管理
	 */
	public function magic(){
		$ctrl = A('Admin/Magic');
		$ctrl->magic();
	}
	public function addMagic(){
		$ctrl = A('Admin/Magic');
		$ctrl->addMagic();
	}
	public function editMagic(){
		$ctrl = A('Admin/Magic');
		$ctrl->editMagic();
	}
	
	public function deleteMagic(){
		$ctrl = A('Admin/Magic');
		$ctrl->deleteMagic();
	}
	
	/**
	 * 订单管理
	 */
	public function order(){
		$ctrl = A('Admin/Order');
		$ctrl->order();
	}
	public function addOrder(){
		$ctrl = A('Admin/Order');
		$ctrl->addOrder();
	}
	public function editOrder(){
		$ctrl = A('Admin/Order');
		$ctrl->editOrder();
	}

	public function deleteOrder(){
		$ctrl = A('Admin/Order');
		$ctrl->deleteOrder();
	}
	
	public function special()
	{
		$ctrl = A('Admin/Special');
		$ctrl->index();
	}
	
	public function orderCleaning()
	{
		$ctrl = A('Admin/OrderCleaning');
		$ctrl->index();
	}
	
	public function statistic(){
		$data = empty ( $data ) ? $_GET : $data;
		if(!isN($data['id'])){
			switch($data['id']){
				case 1:
					//销售额统计
					if($data['report']=='1'){
						$this->assign('data',$data); 
						//报表
						//$data['date1'],$data['date2'],$data['type'],$data['paymethod'],$data['pay'],$data['status']
						//select year(addtime) as 年,month(addtime) as 月,count(*) as 订单数,sum(amount) as 销售额  from `my_order` where addtime between  '2012-1-1' and   '2015-1-1'  group by year(addtime),month(addtime) order by year(addtime),month(addtime)
						
						$where=array();
						if(!isN($data['date1'])){
							$where['addtime'][]=array('egt',$data['date1']);
						}
						if(!isN($data['date2'])){ 
							$ctrl=new \Org\Util\Date($data['date2']);
							$date2=$ctrl->dateAdd(1)->format();
							$where['addtime'][]=array('elt',$date2);
						}
						
						if(isset($data['paymethod'])){
							$where['paymethod']=array('in',$data['paymethod']);
						}
						if(isset($data['pay'])){
							$where['pay']=array('in',$data['pay']);
						}
						if(isset($data['status'])){
							$where['status']=array('in',$data['status']);
						}
						if($data['type']==1){
						$field=array('year(addtime) as year,month(addtime) as month,count(*) as num,sum(amount) as amount');
						$group='year(addtime),month(addtime)';
						$order='year(addtime),month(addtime)';
						}else{
						$field=array('year(addtime) as year,month(addtime) as month,day(addtime) as day,count(*) as num,sum(amount) as amount');
						$group='year(addtime),month(addtime),day(addtime)';
						$order='year(addtime),month(addtime),day(addtime)';
							
						}
						$list=M('order')->field($field)->where($where)->group($group)->order($order)->select();

						if($data['export']=='1'){ 
							list_to_csv($list);
						}else{
							$this->assign('list',$list);
						}
						
					}else{
						//筛选
						$data['type']=1;
						$data['paymethod']=array(4,2);
						$data['pay']=array(0,1);
						$data['status']=array(0,1,2,3);
						$this->assign('data',$data); 
						
					}
					
					$this->assign('title','销售额报表');
					$this->display('statistic_'.$data['id']);
					exit();
					break;
				case 2:
						//商品销售统计
						if($data['report']=='1'){
							$this->assign('data',$data);
							//报表
							//$data['date1'],$data['date2'],$data['type'],$data['paymethod'],$data['pay'],$data['status']
							//select year(addtime) as 年,month(addtime) as 月,count(*) as 订单数,sum(amount) as 销售额  from `my_order` where addtime between  '2012-1-1' and   '2015-1-1'  group by year(addtime),month(addtime) order by year(addtime),month(addtime)
					
							$where=array();
							if(!isN($data['date1'])){
								$where['a.addtime'][]=array('egt',$data['date1']);
							}
							if(!isN($data['date2'])){
								$ctrl=new \Org\Util\Date($data['date2']);
								$date2=$ctrl->dateAdd(1)->format();
								$where['a.addtime'][]=array('elt',$date2);
							}
					
							if(isset($data['status'])){
								$where['a.status']=array('in',$data['status']);
							}
							if(isset($data['status1'])){
								$where['b.status']=array('in',$data['status1']);
							}
							 
							 
							$group='a.productid';
							$order='num';

							if(isset($data['title'])){
								$field=array(' a.productid,a.productname,a.namecn,sum(a.num) as num,a.price,sum(a.num*a.price) as amount,a.supplyname,b.sold,b.hits,b.status ');
							}
							if(!isN($data['order'])){
								switch($data['order']){
									case 1:
										$order=' num ';
										break;
									case 2:
										$order='b.hits ';
										break;
									case 3:
										$order='amount ';
										break; 
								};
							}
							if(!isN($data['type'])){
								switch($data['type']){
									case 1:
										$order.=' desc';
										break;
									case 2:
										$order.=' asc';
										break; 
								}
								
							}
							
							$M=M('order_detail')->alias('a')->join('my_content as b on a.productid=b.id');
							$list=$M->field($field)->where($where)->group($group)->order($order)->select();
							foreach($list as $k=>$v){
								$list[$k]['rate']= to_percent($v['num']/$v['hits']); 
							}

							if($data['export']=='1'){
								list_to_csv($list);
							}else{
								$this->assign('list',$list);
							}
							
						}else{
							//筛选
							$data['type']=1;
							$data['status1']=array(0,1);
							$data['title']=array(1);
							$data['order']=1;
							$data['status']=array(0,1,2,3);
							$this->assign('data',$data);
					
						}
							
						$this->assign('title','商品销售报表');
						$this->display('statistic_'.$data['id']);
						exit();
						break;
				case 3:
					//用户消费统计
					 
					if($data['report']=='1'){
						$this->assign('data',$data); 
						$month=0;
						$where=array();
						if(!isN($data['date1'])){
							$where['addtime'][]=array('egt',$data['date1']);
						}
						if(isN($data['date2'])){ 
							$data['date2']=time_format();
						}
						if(!isN($data['date2'])){ 
							$ctrl=new \Org\Util\Date($data['date2']);
							$date2=$ctrl->dateAdd(1)->format();
							$where['addtime'][]=array('elt',$date2);
							
							if($data['date1']==$data['date2']){
								$month=1;
							}else{
								$month=$ctrl->dateDiff($data['date1'],'M');
								$month=abs(floor($month));
							}
							
						} 
						
						if(isset($data['status'])){
							$where['status']=array('in',$data['status']);
						}
						
						$field=array('userid,username,sum(amount) as amount , count(id) as num');
						$group='userid';
						$order=' amount ';

						if(!isN($data['order'])){
							switch($data['order']){
								case 1:
									$order=' amount ';
									break;
								case 2:
									$order=' num ';
									break; 
							};
						}
						if(!isN($data['type'])){
							switch($data['type']){
								case 1:
									$order.=' desc';
									break;
								case 2:
									$order.=' asc';
									break; 
							}
							
						}
						
						//正常会员
						$where['usertype']=1;
						$list=M('order')->field($field)->where($where)->group($group)->order($order)->select();
						foreach($list as $k=>$v){
							$list[$k]['monthamount']= to_price($v['amount']/$month);
							$list[$k]['monthnum']= to_price($v['num']/$month);
						}
						

						//匿名会员
						$where['usertype']=2;
						$group='usertype';
						
						$list1=M('order')->field($field)->where($where)->group($group)->order($order)->select();
						foreach($list1 as $k=>$v){
							$list1[$k]['username']= '游客';
							$list1[$k]['userid']= '-';
							$list1[$k]['monthamount']= to_price($v['amount']/$month);
							$list1[$k]['monthnum']= to_price($v['num']/$month);
						}
						$list=array_merge($list,$list1);
						$this->assign('month',$month);
					
						if($data['export']=='1'){ 
							list_to_csv($list);
						}else{
							$this->assign('list',$list);
						}
						
					}else{
						//筛选
						$data['type']=1; 
						$data['order']=1;
						$data['status']=array(0,1,2,3);
						$this->assign('data',$data); 
						
					}
					
					$this->assign('title','用户消费报表');
					$this->display('statistic_'.$data['id']);
					exit();
					break;
					
					case 4:
						//供应商统计
					
						if($data['report']=='1'){
							$this->assign('data',$data);
							$month=0;
							$amountall=0;
					
							$where=array();
							if(!isN($data['date1'])){
								$where['addtime'][]=array('egt',$data['date1']);
							}
							if(isN($data['date2'])){
								$data['date2']=time_format();
							}
							if(!isN($data['date2'])){
								$ctrl=new \Org\Util\Date($data['date2']);
								$date2=$ctrl->dateAdd(1)->format();
								$where['addtime'][]=array('elt',$date2);
									
								if($data['date1']==$data['date2']){
									$month=1;
								}else{
									$month=$ctrl->dateDiff($data['date1'],'M');
									$month=abs(floor($month));
								}
									
							}
					
							if(isset($data['status'])){
								$where['status']=array('in',$data['status']);
							}
					
							$field=array('supplyid,supplyname,sum(price*num) as amount');
							$group='supplyid';
							$order=' amount ';

							if(!isN($data['order'])){
								switch($data['order']){
									case 1:
										$order=' amount ';
										break;
									case 2:
										$order=' supplyname ';
										break;
								};
							}
							
							if(!isN($data['type'])){
								switch($data['type']){
									case 1:
										$order.=' desc';
										break;
									case 2:
										$order.=' asc';
										break;
								}
									
							}
					
							$list=M('order_detail')->field($field)->where($where)->group($group)->order($order)->select();
							$db = M('order_detail')->field('sum(price*num) as amountall')->where($where)->select();
							$amountall = floatval($db[0]['amountall']); 
							
							foreach($list as $k=>$v){
								$list[$k]['percent']=to_percent($v['amount']/$amountall);
							}
							
							$this->assign('month',$month);
							$this->assign('amountall',$amountall);
							
							if($data['export']=='1'){
								list_to_csv($list);
							}else{
								$this->assign('list',$list);
							}
					
						}else{
							//筛选
							$data['type']=1;
							$data['order']=1;
							$data['status']=array(0,1,2,3);
							$this->assign('data',$data);
					
						}
							
						$this->assign('title','供应商报表');
						$this->display('statistic_'.$data['id']);
						exit();
						break;
						
			}
		}
		$this->assign('title','订单统计');
		$this->display();
	}
	/**
	 * 
	 * @param string $keyword
	 * @param string $searchtype:0-商品ID，1-分类ID，2-用户ID
	 * @param string $date1
	 * @param string $date2
	 */
	public function statistic_bak($keyword='',$searchtype='',$date1='',$date2='',$status=3){
		$where=array();
		if(is_numeric($keyword)){
			
			$where['status']=$status;
			if($date1!=''&&$date2!=''){
				$exec=new \Org\Util\Date ($date2);
				$date=$exec->dateAdd(1);
				$date=strVal($date);
				$where['addtime']=array('between',array($date1,$date));
			}else{ 
				if($date1!=''){
					$where['addtime']=array('egt',$date1);
				}else if($date2!=''){
					$exec=new \Org\Util\Date ($date2);
					$date=$exec->dateAdd(1);
					$date=strVal($date);
					$where['addtime']=array('elt',$date);
				}
			}
			 switch($searchtype){
			 	case '0':
			 		$where['productid']=$keyword;
			 		$fields='productname,price,sum(num) as num,sum(price*num) as amount';
			 		$data=M('order_detail')->field($fields)->where($where)->find();
			 		if($data['num']==null){ 
			 			$count=array('sum'=>0,'amount'=>0);
			 			$db=null;
			 		}else{
			 			$fields='sum(num) as sum ,sum(price*num) as amount';
			 			$count=M('order_detail')->field($fields)->where($where)->find();
			 			
			 			$data['productid']=$keyword;
			 			$db[]=$data;
			 		}
			 		break;
			 	case '1':
			 		$where['sortpath']=array('like','%,'.$keyword.',%');
			 		$fields='productname,productid ,num,price ,(price*num) as amount';
			 		$order='productid asc';
			 		$db=M('order_detail')->order($order)->field($fields)->where($where)->select();
			 		$fields='sum(num) as sum ,sum(price*num) as amount';
			 		$count=M('order_detail')->field($fields)->where($where)->find(); 
			 		if($count['sum']==null){
			 			$count=array('sum'=>0,'amount'=>0);
			 		}
			 		break;
			 	case '2':
			 		$where['userid']=$keyword;
			 		$fields='productname,productid,num,price ,(price*num) as amount';
			 		$order='productid asc';
			 		$db=M('order_detail')->order($order)->field($fields)->where($where)->select();
			 		
			 		$fields='sum(num) as sum ,sum(price*num) as amount';
			 		$count=M('order_detail')->field($fields)->where($where)->find();
			 		if($count['sum']==null){
			 			$count=array('sum'=>0,'amount'=>0);
			 		}
			 		break;
			 		
			 }
		}
			 	 
		$this->assign('list',$db);
		$this->assign('count',$count);
		$this->assign('keyword',$keyword);
		$this->assign('searchtype',$searchtype);
		$this->assign('date1',$date1);
		$this->assign('date2',$date2);
		$this->assign('status',$status);
		$this->display();
	}

	/**
	 * 门店管理
	 */
	public function shop() {
		$list = M ( "shop" )->order ( 'sortpath asc' )->select ();
		$this->assign ( "list", $list );
		$this->display ();
	}
	
	// 添加门店
	public function addShop($pid = 0) {
		if (IS_POST) {
			$db = D ( "shop" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			// depth,sortpath
			$info = M ( 'shop' )->getById ( $pid );
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($data) {
				if ($db->add ( $data )) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'shop' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ','
					) );
					$this->success ( "添加门店成功！" );
				} else {
					$this->error ( '添加门店失败！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "shop" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid );
				
			// 输出当前Shop列表
			$list = M ( "shop" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list );
			 
			$this->display ();
		}
	}
	
	// 编辑门店
	public function editShop($id = 0) {
		if (IS_POST) {
			$db = D ( "shop" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
				
			// 上级门店不能是自己
			if ($data ['pid'] == $id) {
				$this->error ( '上级不能是自己！' );
			}
				
			// depth,sortpath
			$info = M ( 'shop' )->getById ( $data ['pid'] );
			// 有下级门店不能改变自己的上级
			if ($data ['pid'] !== $info ['pid']) {
				if ($data ['pid'] != '0') {
					$find = $db->where ( 'pid=' . $id )->count ();
					if ($find > 0) {
						$this->error ( '有下级门店时不能是改变自己的上级！' );
					}
				}
			}
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			
			if ($data) {
				if ($db->save ( $data ) !== false) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'shop' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ','
					) );
					$this->success ( "编辑门店成功！" );
				} else {
					$this->error ( '编辑门店失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
				
			$db = M ( "shop" )->find ( $id );
			$this->assign ( "db", $db );
			
			// 输出当前Shop列表
			$list = M ( "shop" )->order ( 'sortpath asc' )->select ();
			$this->assign ( "list", $list ); 
			$this->display ('editShop');
		}
	}
	
	// 删除门店
	public function deleteShop($id) {
		$db = M ( "shop" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	
	
	
	
	/**
	 * ajax调用
	 * @param number $type
	 * @param number $mid
	 * @param number $cid
	 */
	
	public function ajax($type=0,$mid=0,$cid=0){
		switch($type){
			case 0:
				//模型数据
				if($mid!=0 ){
				$tablename='';
				$model=M('model')->field('name')->where('id='.$mid)->find();
				$tablename =  'content_'.strtolower($model['name']);

				
				//输出模型内容数据
				$db = M ($tablename)->where('id='.$cid)->find(); 
				
				//输出模型字段列表
				$fields=get_model_fields($mid);}
				if(null!==($fields)){
					$this->assign ( "fields", $fields );
					$this->assign ( "db", $db );
					$this->display ('Index:field');
				}else{
					echo('');
					exit();
				}
				
				break;
			case 1:
				//订单数据
				$ctrl=A('Admin/Order');//($mid."__".$cid);
				$ctrl->editOrderDetail($mid,$cid);
				break;
		}
	}
	/**
	 * 内容管理
	 */
	public function content($rootid=null) {
        $rootid=2;
		$title = null;
		$content = null;
		$where = null;
		$searchtype = I ( 'searchtype' );
		$keyword = I ( 'keyword' );
        if($searchtype && $keyword){
            switch ($searchtype) {
                case '1' : $where ['title'] = array ( 'like','%' . $keyword . '%'); break;
                case '2' : $where['channelname'][]= array('like','%'.$keyword.'%');break;
                case '3' : if (is_numeric ( $keyword )) { $where ['id'] = $keyword; } break;
                case '4' :
                    if (is_numeric ( $keyword ) && $keyword != 0) {
                        $where['sortpath'][]= array('like','%,'.$keyword.',%');
                    }
                    break;
                case '5' :
                    if (is_numeric ( $keyword )) { $where ['supplyid'] = $keyword;}break;
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
        if ( is_number($_REQUEST['status'])) {
			$where ['status'] = $_REQUEST['status'];
        }
        if (is_number($_REQUEST['good_type'])) {
			$where ['good_type'] = $_REQUEST['good_type'];
		}
        if ((!isset($_REQUEST['ranktype']) || empty($_REQUEST['ranktype'])) || !empty($_REQUEST['ranktype']) && $_REQUEST['ranktype']==1) {
            $order = 'update_time ';
        }elseif(!empty($_REQUEST['ranktype']) && $_REQUEST['ranktype']==2){
            $order = 'id ';
        }else{
            $order = 'sold ';
        }
        if ((!isset($_REQUEST['rank']) || empty($_REQUEST['rank'])) ||!empty($_REQUEST['rank']) && $_REQUEST['rank'] =='desc') {
            $order.='desc';
		}else{
            $order.='asc';
        }
		// 输出当前Content列表
		if(isset($rootid)){
			$where['sortpath'][]= array('like','%,'.$rootid.',%');
		}
		if ($_REQUEST['pid']>0) {
			$where['sortpath'][]= array('like','%,'.$_REQUEST['pid'].',%');
		}
        if ($_REQUEST['supplyid']>0) {
            $where ['supplyid'] = $_REQUEST['supplyid'];
        }
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		$rs = M ( "content" )->where ( $where )->order ( $order)->page ( $p, $row );
		$list = $rs->select ();
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( 'page', $page->show () );
		}
//
//		$this->assign ( "keyword", $keyword );
//		$this->assign ( "status", $status );
//		$this->assign ( "searchtype", $searchtype );
        $where=array();
        // 输出分类
        if(!empty($rootid)){
            $where['sortpath']= array('like','%,'.$rootid.',%');
        }
        $where['id']=get_role();
        $list = M ( "Channel" )->where($where)->order ( 'sort asc' )->select ();
        $list = list_to_tree ( $list );
        $this->assign ( "catlist", $list );
        // 输出供应商列表
        $where=array();
        $where['status']=1;
        $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
        $this->assign ( "supplylist", $list );
      //  $this->assign ( "rootid", $rootid );

		if($rootid==1){
		$this->display('content1');	
		}else{
		$this->display ();
		}
	}

    /**
     * ajax提交操作商品
     */
    public function subContent(){
        $pid = I('post.pid');
        $title = I('post.title');
        $price = I('post.price');
        if($pid<1){
            apiReturn(CodeModel::ERROR,'必须选择所属分类！');
        }
        if(!$title){
            apiReturn(CodeModel::ERROR,'必须填写商品名！');
        }
        if(!$price){
            apiReturn(CodeModel::ERROR,'必须填写商品价格！');
        }
        $data = I('post.');
        $id = I('post.id');
        //组合、复合商品重新计算库存
        if(isset($data['good_type']) && $data['good_type'] == ContentModel::COMBINATION_OF_GOODS && isset($data['group_id']) && $data['group_id']) {
            //判断是否支持可负销售
            if ($data['negative'] == ContentModel::CAN_NEGATIVE_AOLD) { //设置可负销售
                if (true !== $rs = ContentModel::checkNegativeSold($data['group_id'])) {
                    apiReturn(CodeModel::ERROR, '商品ID:' . $rs . '   不支持可负销售');
                };
            }
        }

        $group = $data['group_id'];
       // GoogsGroupModel::getGroupStock($group,$id);
        unset($data['group_id']);
        if(regex($id,'number')){ //修改商品
            if(ContentModel::modifyContent($id,$data)){
                if(!empty($group) && isset($data['good_type']) && $data['good_type'] == ContentModel::COMBINATION_OF_GOODS ){
                    $data = array();
                    if(   $data['stock'] =  GoodsGroupModel::getGroupStock($group,$id)){
                        ContentModel::modifyContent($id,$data);
                    }
                }else{ //修改普通商品完成后判断该商品是否被组合，（关联：重新计算被关联商品的库存）
                    if( GoodsGroupModel::isGroupChildGoods($id)){
                      GoodsGroupModel::resetGroupGoodsStock($id);
                    }
                }
                apiReturn(CodeModel::CORRECT,'编辑成功！');
            }
        }else{//新增商品
            if($id = ContentModel::addContent($data)){
                if(!empty($group) && isset($data['good_type']) && $data['good_type'] == ContentModel::COMBINATION_OF_GOODS ){
                    $data = array();
                    if($data['stock'] =  GoodsGroupModel::getGroupStock($group,$id)){
                        ContentModel::modifyContent($id,$data);
                    }
                }
                apiReturn(CodeModel::CORRECT,'添加成功！');
            }
        }
    }

    public function delGoodImg(){
        $id = I('post.id');
        $field = I('post.field');
        $img = I('post.img');
        if(regex($id,'number') && $img && $field){
            if($field == 'indexpic'){
                $data['indexpic'] = '';
            }else{
                $good = ContentModel::getContentById($id);
                $imgArr = array_filter(get_imgs($good['images']));
                $newimg = '';
                if(!empty($imgArr)){
                    foreach($imgArr as $val){
                        if($val != $img){
                            $newimg.=$val.'|';
                        }
                    }
                }
                $data['images'] = $newimg;
            }
           // delfile($img);0,2,517,520,2,
            if(ContentModel::modifyContent($id,$data)){
                apiReturn(CodeModel::CORRECT,'删除成功');
            }else{
                apiReturn(CodeModel::CORRECT,'删除失败');
            }
        }else{
            apiReturn(CodeModel::CORRECT,'参数错误');
        }
    }

	// 添加内容
	public function addContent($pid = 0,$rootid=null) {
		if (IS_POST) {
			$db = D ( "content" );
			$data = empty ( $data ) ? $_POST : $data;
			$ext =$data;
			$data = $db->create ( $data );
			// depth,sortpath
			session ( 'last_pid', $pid );
			$info = M ( 'Channel' )->getById ( $pid );
			$sortpath = $info ['sortpath'];
			$model_id = $info ['model_id'];
			if($pid==0){
				$this->error('必须选择所属分类！');
			}
            $data['sortpath']=$sortpath;
			//赋默认值：关键词，描述，作者，来源
			if(isN($data['keywords'])){
				$data['keywords']=$data['title'];
			}
			if(isN($data['description'])){
				$data['description']=$data['title'];
			}
			if(isN($data['source'])){
				$data['source']=C('config.WEB_SITE_TITLE');
			}
			if(isN($data['author'])){
				$data['author']='管理员';
			}
				$data['addip']=get_client_ip();
				$data['supplyname']=get_cate($data['supplyid'],'supply');
				$data['channelname']=get_cate($data['pid']);

			if ($data) {
				$lastid=$db->add ( $data );
				if ($lastid) {
					//$this->updateChannelNum($sortpath);
					//保存扩展模型内容
					if($model_id!=0){

						$table=('content_'.get_table_name($model_id));
						$db=M($table)->where('id='.$lastid)->find();
						if($db!==null){
							$db->save($ext);
						}else{
							$data['id']=$lastid;
							M($table)->add($ext);
						}

					}

					$this->success ( "添加内容成功！" );
				} else {
					$this->error ( '添加内容失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		}else {
            $origin = GoodsAttrModel::getGoodAttr(GoodsAttrModel::ORIGIN);
            $storage = GoodsAttrModel::getGoodAttr(GoodsAttrModel::STORAGE);
			$sort = M ( "content" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid );
			$where=array();
			// 输出当前Content列表
			if(isset($rootid)){
				$where['sortpath']= array('like','%,'.$rootid.',%');
			}
			$where['id']=get_role();
			$list = M ( "Channel" )->where($where)->order ( 'sort asc' )->select ();
            $list = list_to_tree ( $list );
            $this->assign ( "list", $list );
			// 输出门店列表
			$where=array();
			$where['id']=get_role('shop');
			$list = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
			$this->assign ( "shoplist", $list );
			$this->assign ( "shop_id", session('shop_id') );

			// 输出模型列表
			$modellist = M ( "model" )->where ( 'status=1' )->select ();
			$this->assign ( "modellist", $modellist );
			$this->assign ( "pid", session ( 'last_pid' ) );

			// 输出供应商列表
			$where=array();
			$where['status']=1;
			$list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
			$this->assign ( "supplylist", $list );
			$this->assign ( "origin", $origin );
			$this->assign ( "storage", $storage );
			$this->display ('addContent');
		}
	}

	// 编辑内容
	public function editContent($id = 0) {
		if (IS_POST) {
			$db = D ( "content" );
			$data = empty ( $data ) ? $_POST : $data;
			$ext =$data;
			$refurl=($data['refurl']);
			$data = $db->create ( $data );
			if(isN($refurl)){
				$refurl=I('SERVER.HTTP_REFERER');
			}
			$this->assign('refurl',$refurl);

			// depth,sortpath
			$info = M ( 'channel' )->getById ( $data ['pid'] ); 
			$sortpath = $info ['sortpath'];
            $data['sortpath']=$sortpath;

            $data['supplyname']=get_cate($data['supplyid'],'supply');
            $data['channelname']=get_cate($data['pid']);
			if ($data) {
				if ($db->save ( $data ) !== false) {
					//$this->updateChannelNum($sortpath);
			
					session('shop_id',$data['shop_id']);
					// 更新model_id 
					$model_id=$info['model_id'];
					$db = M ( 'content' )->where ( 'id=' . $id );
					$db->save ( array (
							'model_id' =>  $model_id
					) );
					
					//保存扩展模型内容
					if($model_id!=0){ 
						$table=('content_'.get_table_name($model_id));
						
						$db=M($table)->where('id='.$id)->find();
						 
						if($db!==null){
							M($table)->save($ext);
						}else{
							M($table)->add($ext);
						}
					}
					$this->success ( "编辑内容成功！" ,$refurl);
				} else {
					$this->error ( '编辑内容失败！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
            $origin = GoodsAttrModel::getGoodAttr(GoodsAttrModel::ORIGIN);
            $storage = GoodsAttrModel::getGoodAttr(GoodsAttrModel::STORAGE);
            $this->assign ( "origin", $origin );
            $this->assign ( "storage", $storage );
			$db = M ( "content" )->find ( $id );
            if($db['id']){
                $db['child'] =  GoodsGroupModel::getGoodsChild($db['id']);
            }
			$this->assign ( "db", $db );
			$where = array();
			if(!isN($db['sortpath'])){
				$rootid=str2arr($db['sortpath']); 
				if(isset($rootid)){
					$where['sortpath']= array('like','%,'.$rootid[1].',%');
				}	
			}
			// 输出门店列表
			$list = M ( "Shop" )->order ( 'sort asc' )->select ();
			$this->assign ( "shoplist", $list );

			// 输出当前channel列表
			$list = M ( "Channel" )->where($where)->order ( 'sort asc' )->select ();
			$list = list_to_tree ( $list );
			$this->assign ( "list", $list );
			$this->assign ( "pid", $db['pid'] ); 
			$refurl=I('SERVER.HTTP_REFERER'); 
			$this->assign('refurl',$refurl);
			
			
			// 输出供应商列表
			$where=array();
			$where['status']=1;
			$list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
			$this->assign ( "supplylist", $list );
            $this->display ('addContent');
		}
	}

    public function delgoodsgroup(){
        $id = I('post.pid');
        if(regex($id,'number')){
            if(GoodsGroupModel::delGoodsChild($id)){
                apiReturn(CodeModel::CORRECT);
            }else{
                apiReturn(CodeModel::ERROR,'删除失败');
            }
        }
        apiReturn(CodeModel::ERROR,'删除失败');
    }

	// 删除内容
	public function deleteContent($id) {
        $db= ContentModel::delContent($id);
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	//更新栏目内容数统计
	private function updateChannelNum($sortpath=''){
		$arr=str2arr($sortpath);
		$arr=arr2clr($arr);
		if(count($arr)>0){
			$count=0;
			foreach($arr as $key=>$value){
				$where=array();
				$where['status']=1;
    			$where['sortpath']=array('like','%,'.$value.',%');
				$count = M('content')->where($where)->count();
				M('channel')->where('id='.$value)->setField('num',$count);
			}
		}
	}
	
	/**
	 * 分类管理
	 */
	public function channel1() {
		$list = M ( "channel" )->order ( 'sortpath asc' )->select ();
		$this->assign ( "list", $list );
		$this->display ();
	}
	
	public function channel() {
		$fields = 'id,pid,name,sortpath,depth,sort,remark,status';
		$where = array (
				//'status' => 1 
		);
		$order = 'sort asc';
		$db = M ( 'channel' )->field ( $fields )->where ( $where )->order ( $order )->select ();
		
		$data = list_to_tree ( $db );
		$this->assign('tree',$data);
		$this->display();
	}
	
	public function treelist($tree = null){
        $this->assign('tree', $tree);
        $this->display('treelist');
    }
    
	public function treeselect($tree = null,$obj='option'){
        $this->assign('tree', $tree);
        $this->assign('obj', $obj);
        $this->display('treeselect');
    }
    
	// 添加分类
	public function addChannel($pid = 0) {
		if (IS_POST) {
			$db = D ( "channel" );
			$data = empty ( $data ) ? $_POST : $data;
			$validate = $db->create ( $data );
			// depth,sortpath
			$info = M ( 'channel' )->getById ( $pid );
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($validate) {
				if ($db->add ( $data )) {
					// 更新sortpath
					$last_id = $db->getLastInsID ();
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					}
					$db = M ( 'channel' )->where ( 'id=' . $last_id );
					$db->save ( array (
							'sortpath' => $sortpath . $last_id . ',' 
					) );
					$this->success ( "添加分类成功！" );
				} else {
					$this->error ( '添加分类失败！' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$sort = M ( "channel" )->max ( "id" );
			$this->assign ( "sort", $sort + 1 );
			$this->assign ( "pid", $pid );
			
			// 输出当前Channel列表
// 			$list = M ( "channel" )->order ( 'sortpath asc' )->select ();
// 			$this->assign ( "list", $list );
			$fields = 'id,pid,name,sortpath,depth,sort,remark,status';
			$where = array ('status' => 1);
			$order = 'sort asc';
			$db = M ( 'channel' )->field ( $fields )->where ( $where )->order ( $order )->select ();
				
			$data = list_to_tree ( $db );
			$this->assign('tree',$data);
			
			// 输出模型列表
			$modellist = M ( "model" )->where ( 'status=1' )->select ();
			$this->assign ( "modellist", $modellist );
			
			//取父级绑定的模型
			$model_id=M('channel')->getFieldById($pid,'model_id'); 
			$this->assign ( "model_id", $model_id );
			$this->display ('addChannel');
		}
	}
	
	// 编辑分类
	public function editChannel($id = 0) {
		if (IS_POST) {
			$db = D ( "channel" );
			$data = empty ( $data ) ? $_POST : $data;
			$validate = $db->create ( $data );

			// 上级分类不能是自己
			if ($data ['pid'] == $id) {
				$this->error ( '上级不能是自己！' );
			}
			clr_cache();
			
			// depth,sortpath
			$pid=M('channel')->where('id='.$id)->getField('pid'); 
			 
			// 有下级分类不能改变自己的上级
			if ($data ['pid'] !== $pid) { 
				$find = $db->where ( 'pid=' . $id )->count ();
				if ($find > 0) {
					$this->error ( '有下级分类时不能改变自己的上级！' );
				} 
			}
			$info=M('channel')->find($data ['pid']);
			$data ['depth'] = $info ['depth'] + 1;
			$sortpath = $info ['sortpath'];
			if ($validate) {
				if ($db->save ( $data ) !== false) {
					// 更新sortpath 
					if (empty ( $sortpath )) {
						$sortpath = '0,';
					} 
					$db = M ( 'channel' )->where ( 'id=' . $id );
					$db->save ( array (
							'sortpath' => $sortpath . $id . ',' 
					) );
					$this->success ( "编辑分类成功！" );
				} else {
					$this->error ( '编辑分类失败' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			
			$db = M ( "channel" )->find ( $id );
			$this->assign ( "db", $db );
			$this->assign('pid',$db['pid']);
			
			// 输出当前Channel列表
// 			$list = M ( "channel" )->order ( 'sortpath asc' )->select ();
// 			$this->assign ( "list", $list );
			$fields = 'id,pid,name,sortpath,depth,sort,remark,status';
			$where = array ('status' => 1);
			$order = 'sort asc';
			$db = M ( 'channel' )->field ( $fields )->where ( $where )->order ( $order )->select ();
				
			$data = list_to_tree ( $db );
			$this->assign('tree',$data);
			
			// 输出模型列表
			$modellist = M ( "model" )->where ( 'status=1' )->select ();
			$this->assign ( "modellist", $modellist );
			$this->display ('editChannel');
		}
	}
	
	// 删除分类
	public function deleteChannel($id) {
		$db = M ( "channel" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * 模型字段管理
	 */
	public function field($id = 0) {
		$where = array (
				"model_id" => $id 
		);
		$list = M ( "field" )->where ( $where )->select ();
		$this->assign ( "list", $list );
		
		$model_name = M ( "model" )->where ( 'id=' . $id )->getField ( "name" );
		$this->assign ( "model_name", $model_name );
		$this->assign ( "model_id", $id );
		$this->display ();
	}
	
	// 添加字段
	public function addField() {
		if (IS_POST) {
			$db = D ( "field" );
			if ($db->update ()) {
				$this->success ( "添加字段成功！" );
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			$model_id = I ( "id", 0 );
			$sort = M ( "field" )->max ( "id" );
			$this->assign ( "model_id", $model_id );
			$this->assign ( "sort", $sort + 1 );
			
			// 输出当前Field列表
			$list = M ( "field" )->select ();
			$this->assign ( "list", $list );
			$this->display ('addField');
		}
	}
	
	// 编辑字段
	public function editField($id = 0) {
		if (IS_POST) {
			$db = D ( "field" );
			if ($db->update ()) {
				$this->success ( "编辑字段成功！" );
			} else {
				$this->error ( $db->getError () );
			}
		} else {
			
			$db = M ( "field" )->find ( $id );
			$this->assign ( "db", $db );
			
			// 输出当前Field列表
			$list = M ( "field" )->select ();
			$this->assign ( "list", $list );
			$this->display ('editField');
		}
	}
	
	// 删除字段
	public function deleteField($id) {
		$db = D ( "field" );
		$info = $db->getById ( $id );
		$db->delete ( $id );
		if ($db->deleteField ( $info )) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	// 模型列表
	public function Model() {
		$list = M ( "model" )->select ();
		$this->assign ( "list", $list );
		$this->display ();
	}
	
	// 添加模型
	public function addModel() {
		if (IS_POST) {
			$db = M ( "model" )->add ( $_POST );
			if ($db !== false) {
				$this->success ( "添加模型成功！" );
			} else {
				$this->error ( "添加失败！" );
			}
		} else {
			$pid = I ( "pid", 0 );
			$sort = M ( "model" )->max ( "id" );
			$this->assign ( "pid", $pid );
			$this->assign ( "sort", $sort + 1 );
			
			// 输出当前Model列表
			$list = M ( "model" )->select ();
			$this->assign ( "list", $list );
			$this->display ('addModel');
		}
	}
	
	// 编辑模型
	public function editModel($id = 0) {
		if (IS_POST) {
			$db = M ( "model" )->save ( $_POST );
			if ($db !== false) {
				$this->success ( "编辑模型成功！" );
			} else {
				$this->error ( "编辑失败！" );
			}
		} else {
			
			$db = M ( "model" )->find ( $id );
			$this->assign ( "db", $db );
			
			// 输出当前Model列表
			$list = M ( "model" )->select ();
			$this->assign ( "list", $list );
			$this->display ('editModel');
		}
	}
	
	// 删除模型
	public function deleteModel($id) {
		$db = M ( "model" )->delete ( $id );
		if ($db !== false) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	

	/**
	 * 评价列表，分页
	 */
	public function comment() {
	    $where = null;
	    $searchtype = I ( 'searchtype' );
	    $keyword = I ( 'keyword' );
	    $status = I ( 'status' );
	
	    switch ($searchtype) {
	        case '0' :
	            $info = $keyword;
	            break;
	        case '1' :
	            $username = $keyword;
	            break;
	        case '2' :
	            $orderno = $keyword;
	            break;
	    }
	
	    if (is_numeric ( $status )) {
	        $where ['status'] = $status;
	    }
	
	    if (! isN ( $info )) {
	        $where ['info'] = array (
	            'like',
	            '%' . $info . '%'
	        );
	    }
	    if (! isN ( $username )) {
	        $where ['username'] = array (
	            'like',
	            '%' . $username . '%'
	        );
	    }
	    if (! isN ( $orderno )) {
	        $where ['orderno'] = array (
	            'like',
	            '%' . $orderno . '%'
	        );
	    }
	
	    // 分页
	    $p = intval ( I ( 'p' ) );
	    $p = $p ? $p : 1;
	    $row = C ( 'VAR_PAGESIZE' );
	
	    $rs = M ( "comment" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
	    $list = $rs->select ();
	
	    $this->assign ( "list", $list );
	    $count = $rs->where ( $where )->count ();
	
	    if ($count > $row) {
	        $page = new \Think\Page ( $count, $row );
	        $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
	        $this->assign ( 'page', $page->show () );
	    }
	
	    $this->assign ( "keyword", $keyword );
	    $this->assign ( "status", $status );
	    $this->assign ( "searchtype", $searchtype );
	
	    $this->display ();
	}
	
	// 添加评价
	public function addComment($pid = 0) {
	    if (IS_POST) {
	        $db = D ( "comment" );
	        $data = empty ( $data ) ? $_POST : $data;
	
	        $data ['addip'] = get_client_ip ();
	
	        // 计算评价菜品数量和金额
	        $arr = str2arr ( $data ['contentids'] );
	        $arr = array_filter ( $arr );
	        $arr = array_unique ( $arr );
	        $num = count ( $arr );
	        $where = array ();
	        $where ['id'] = array (
	            'in',
	            $arr
	        );
	        $amount = M ( 'content' )->where ( $where )->sum ( 'price' );
	
	        $data ['num'] = $num;
	        $data ['amount'] = $amount;
	
	        if (false !== $db->add ( $data )) {
	            $this->success ( "添加评价成功！" );
	        } else {
	            $this->error ( '添加评价失败！' );
	        }
	    } else {
	        $sort = M ( "comment" )->max ( "id" );
	        $this->assign ( "sort", $sort + 1 );
	        $this->assign ( "pid", $pid );
	
	        // 输出评价分类列表
	        $typelist = M ( "commenttype" )->order ( 'sortpath asc' )->select ();
	        $this->assign ( "typelist", $typelist );
	
	        // 输出门店列表
	        $where = array ();
	        $where ['id'] = get_role ( 'shop' );
	        $shoplist = M ( "Shop" )->where ( $where )->order ( 'sortpath asc' )->select ();
	        $this->assign ( "shoplist", $shoplist );
	
	        // 输出当前Comment列表
	        $list = M ( "comment" )->order ( 'sortpath asc' )->select ();
	        $this->assign ( "list", $list );
	
	        $this->display ('addComment');
	    }
	}
	
	// 编辑评价
	public function editComment() {
	    $id = I ( 'id' );
	    if (IS_POST) {
	        $db = D ( "comment" );
	        $data = empty ( $data ) ? $_POST : $data;
	        $data = $db->create ( $data );
	        if ($data) {
	
	            // 计算评价菜品数量和金额
	            $arr = str2arr ( $data ['contentids'] );
	            $arr = array_filter ( $arr );
	            $arr = array_unique ( $arr );
	            $num = count ( $arr );
	            $where = array ();
	            $where ['id'] = array (
	                'in',
	                $arr
	            );
	            $amount = M ( 'content' )->where ( $where )->sum ( 'price' );
	
	            $data ['num'] = $num;
	            $data ['amount'] = $amount;
	            unset($data ['num'],$data ['amount']);
	            if ($db->save ( $data ) !== false) {
	                $this->success ( "编辑评价成功！" );
	            } else {
	                $this->error ( '编辑评价失败' );
	            }
	        } else {
	            $this->error ( $db->getError () );
	        }
	    } else {
	
	        $db = M ( "comment" )->find ( $id );
	        $this->assign ( "db", $db );
	        // 输出评价分类列表
	        $typelist = M ( "commenttype" )->order ( 'sortpath asc' )->select ();
	        $this->assign ( "typelist", $typelist );
	
	        // 输出门店列表
	        $where = array ();
	        $where ['id'] = get_role ( 'shop' );
	        $shoplist = M ( "Shop" )->where ( $where )->order ( 'sortpath asc' )->select ();
	        $this->assign ( "shoplist", $shoplist );
	
	        // 输出当前Comment列表
	        $list = M ( "comment_detail" )->where ( 'commentno=' . $db ['commentno'] )->order ( 'id asc' )->select ();
	        $this->assign ( "list", $list );
	        $this->display ('editComment');
	    }
	}
	
	// 删除评价
	public function deleteComment() {
	    $id = I ( 'id' );
	    $db = M ( "comment" )->delete ( $id );
	
	    if ($db !== false) {
	        $this->success ( "删除成功！" );
	    } else {
	        $this->error ( "删除失败" );
	    }
	}
	
	
	
	
	/**
	 * 商品管理
	 */
	public function product($pid = null, $status = null,$rootid=3) {
	    $title = null;
	    $content = null;
	    $where = null;
	    $searchtype = I ( 'searchtype' );
	    $keyword = I ( 'keyword' );
	
	    switch ($searchtype) {
	        case '0' :
	            $title = $keyword;
	            break;
	        case '1' :
	            $content = $keyword;
	            break;
	        case '2' :
	            if (is_numeric ( $keyword )) {
	                $pid = $keyword;
	            }
	            break;
	        case '3' :
	            if (is_numeric ( $keyword )) {
	                $where ['id'] = $keyword;
	            }
	            break;
	        case '4' :
	            if (is_numeric ( $keyword )) {
	                $where ['supplyid'] = $keyword;
	            }
	            break;
	    }
	
	    if (is_numeric ( $status )) {
	        $where ['status'] = $status;
	    }
	
	    if (! isN ( $title )) {
	        $where ['title'] = array (
	            'like',
	            '%' . $title . '%'
	        );
	    }
	    if (! isN ( $content )) {
	        $where ['content'] = array (
	            'like',
	            '%' . $content . '%'
	        );
	    }
	    // 输出当前Content列表
	    if(isset($rootid)){
	        $where['sortpath'][]= array('like','%,'.$rootid.',%');
	    }
	    if (is_numeric ( $pid ) && $pid != 0) {
	        $where['sortpath'][]= array('like','%,'.$pid.',%');
	    }
	    // 		if (is_numeric ( $pid ) && $pid != 0) {
	    // 			$where ['pid'] = $pid;
	    // 		}
	
	    // 分页
	    $p = intval ( I ( 'p' ) );
	    $p = $p ? $p : 1;
	    $row = C ( 'VAR_PAGESIZE' );
	
	    $rs = M ( "content" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
	    $list = $rs->select ();
	    $this->assign ( "list", $list );
	    $count = $rs->where ( $where )->count ();
	
	    if ($count > $row) {
	        $page = new \Think\Page ( $count, $row );
	        $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
	        $this->assign ( 'page', $page->show () );
	    }
	
	    $this->assign ( "keyword", $keyword );
	    $this->assign ( "status", $status );
	    $this->assign ( "searchtype", $searchtype );
	    $this->assign ( "rootid", $rootid );
	
	    if($rootid==1){
	        $this->display('content1');
	    }else{
	        $this->display ();
	    }
	}
	
	// 添加商品
	public function addProduct($pid = 0,$rootid=3) {
	    if (IS_POST) {
	        $db = D ( "content" );
	        $data = empty ( $data ) ? $_POST : $data;
	        $ext =$data;
	        $data = $db->create ( $data );
	        // depth,sortpath
	        session ( 'last_pid', $pid );
	        $info = M ( 'Channel' )->getById ( $pid );
	        $sortpath = $info ['sortpath'];
	        $model_id = $info ['model_id'];
	        if($pid==0){
	            $this->error('必须选择所属分类！');
	        }
	        $data['sortpath']=$sortpath;
	        //赋默认值：关键词，描述，作者，来源
	        if(isN($data['keywords'])){
	            $data['keywords']=$data['title'];
	        }
	        if(isN($data['description'])){
	            $data['description']=$data['title'];
	        }
	        if(isN($data['source'])){
	            $data['source']=C('config.WEB_SITE_TITLE');
	        }
	        if(isN($data['author'])){
	            $data['author']='管理员';
	        }
	        $data['addip']=get_client_ip();
	        $data['supplyname']=get_cate($data['supplyid'],'supply');
	        $data['channelname']=get_cate($data['pid']);
	
	        if ($data) {
	            $lastid=$db->add ( $data );
	            if ($lastid) {
	
	                //$this->updateChannelNum($sortpath);
	
	                //保存扩展模型商品
	                if($model_id!=0){
	
	                    $table=('content_'.get_table_name($model_id));
	                    $db=M($table)->where('id='.$lastid)->find();
	                    if($db!==null){
	                        $db->save($ext);
	                    }else{
	                        $data['id']=$lastid;
	                        M($table)->add($ext);
	                    }
	
	                }
	                	
	                $this->success ( "添加商品成功！" );
	            } else {
	                $this->error ( '添加商品失败' );
	            }
	        } else {
	            $this->error ( $db->getError () );
	        }
	    } else {
	        $sort = M ( "content" )->max ( "id" );
	        $this->assign ( "sort", $sort + 1 );
	        $this->assign ( "pid", $pid );
	        	
	        $where=array();
	        // 输出当前Content列表
	        if(isset($rootid)){
	            $where['sortpath']= array('like','%,'.$rootid.',%');
	        }
	        $where['id']=get_role();
	        $list = M ( "Channel" )->where($where)->order ( 'sort asc' )->select ();
	        $list = list_to_tree ( $list );
	        $this->assign ( "list", $list );
	        	
	        // 输出门店列表
	        $where=array();
	        $where['id']=get_role('shop');
	        $list = M ( "Shop" )->where($where)->order ( 'sortpath asc' )->select ();
	        $this->assign ( "shoplist", $list );
	        $this->assign ( "shop_id", session('shop_id') );
	
	        // 输出模型列表
	        $modellist = M ( "model" )->where ( 'status=1' )->select ();
	        $this->assign ( "modellist", $modellist );
	        $this->assign ( "pid", session ( 'last_pid' ) );
	        	
	        // 输出供应商列表
	        $where=array();
	        $where['status']=1;
	        $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
	        $this->assign ( "supplylist", $list );
	        	
	        $this->display ('addProduct');
	    }
	}
	
	// 编辑商品
	public function editProduct($id = 0) {
	    if (IS_POST) {
	        $db = D ( "content" );
	        $data = empty ( $data ) ? $_POST : $data;
	        $ext =$data;
	        $refurl=($data['refurl']);
	        $data = $db->create ( $data );
	        	
	        if(isN($refurl)){
	            $refurl=I('SERVER.HTTP_REFERER');
	        }
	        	
	        $this->assign('refurl',$refurl);
	
	        // depth,sortpath
	        $info = M ( 'channel' )->getById ( $data ['pid'] );
	        $sortpath = $info ['sortpath'];
	        $data['sortpath']=$sortpath;
	        $data['supplyname']=get_cate($data['supplyid'],'supply');
	        $data['channelname']=get_cate($data['pid']);
	
	        if ($data) {
	            if ($db->save ( $data ) !== false) {
	                //$this->updateChannelNum($sortpath);
	                	
	                session('shop_id',$data['shop_id']);
	                // 更新model_id
	                $model_id=$info['model_id'];
	                $db = M ( 'content' )->where ( 'id=' . $id );
	                $db->save ( array (
	                    'model_id' =>  $model_id
	                ) );
	                	
	                //保存扩展模型商品
	                if($model_id!=0){
	                    $table=('content_'.get_table_name($model_id));
	
	                    $db=M($table)->where('id='.$id)->find();
	                    	
	                    if($db!==null){
	                        M($table)->save($ext);
	                    }else{
	                        M($table)->add($ext);
	                    }
	                }
	                $this->success ( "编辑商品成功！" ,$refurl);
	            } else {
	                $this->error ( '编辑商品失败！' );
	            }
	        } else {
	            $this->error ( $db->getError () );
	        }
	    } else {
	        	
	        $db = M ( "content" )->find ( $id );
	        	
	        $this->assign ( "db", $db );
	        	
	        $where = array();
	        if(!isN($db['sortpath'])){
	            $rootid=str2arr($db['sortpath']);
	            if(isset($rootid)){
	                $where['sortpath']= array('like','%,'.$rootid[1].',%');
	            }
	        }
	        // 输出门店列表
	        $list = M ( "Shop" )->order ( 'sort asc' )->select ();
	        $this->assign ( "shoplist", $list );
	
	        	
	        // 输出当前channel列表
	        $list = M ( "Channel" )->where($where)->order ( 'sort asc' )->select ();
	        $list = list_to_tree ( $list );
	        $this->assign ( "list", $list );
	        $this->assign ( "pid", $db['pid'] );
	        $refurl=I('SERVER.HTTP_REFERER');
	        $this->assign('refurl',$refurl);
	        	
	        	
	        // 输出供应商列表
	        $where=array();
	        $where['status']=1;
	        $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
	        $this->assign ( "supplylist", $list );
	        $this->display ('editProduct');
	    }
	}
	
	// 删除商品
	public function deleteProduct($id) {
	    $sortpath='';
	    $db = M ( "content" );
	    $db=$db->delete ( $id );
	    if ($db !== false) {
	        $this->success ( "删除成功！" );
	    } else {
	        $this->error ( "删除失败" );
	    }
	}

    //完成未完事项
    public function finishMemo(){
        $ctrl = A('Admin/Order');
        $ctrl->finishMemo();
    }


    public function addCat(){
        $name = I('catname');
        if($cat = SugCatModel::addCat($name)){
            apiReturn(CodeModel::CORRECT,'添加货源成功');
        }else{
            apiReturn(CodeModel::ERROR,'添加货源失败');
        }
    }

    //下架管理
    public function shelvesManagement(){
        $cat = SugCatModel::getCat();
        $where = null;
        $searchtype = I ( 'searchtype' );
        $keyword = I ( 'keyword' );
        if($searchtype && $keyword){
            switch ($searchtype) {
                case '1' :
                    $where ['title'] = array ( 'like','%' . $keyword . '%');
                    break;
                case '2' :
                    $where['channelname'][]= array('like','%'.$keyword.'%');
                    break;
                case '3' :
                    if (is_numeric ( $keyword )) {
                        $where ['id'] = $keyword;
                    }
                    break;
                case '4' :
                    if (is_numeric ( $keyword ) && $keyword != 0) {
                        $where['sortpath'][]= array('like','%,'.$keyword.',%');
                    }
                    break;
            }
        }
        if (is_numeric($_REQUEST['status'])) {
            $where ['status'] = $_REQUEST['status'];
        }
        //售状
        if (is_numeric($_REQUEST['sale_state'])) {
            $where ['sale_state'] = $_REQUEST['sale_state'];
        }
        //货源
        if(is_numeric($_REQUEST['sugcatid'])){
            $where['sugcatid']= array('eq',$_REQUEST['sugcatid']);
        }
        // 分页
        $p = intval ( I ( 'p' ) );
        $p = $p ? $p : 1;
        $row = C ( 'VAR_PAGESIZE' );

        $rs = M ( "content" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
        $list = $rs->select ();
        $this->assign ( "list", $list );
        $count = $rs->where ( $where )->count ();

        if ($count > $row) {
            $page = new \Think\Page ( $count, $row );
            $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
            $this->assign ( 'page', $page->show () );
        }

        $this->assign ( "keyword", $keyword );
        $this->assign ( "searchtype", $searchtype );
        $this->assign ( "catlist", $cat );
        $this->display ('shelves_management');
    }

    /**
     * 产地列表
     */
    public function origin(){
        $type = $_REQUEST['type']?$_REQUEST['type']:GoodsAttrModel::ORIGIN;
        $list = GoodsAttrModel::getGoodAttr($type);
        $this->assign ( "list", $list );
        $this->display ();
    }

    /**
     * 商品属性操作
     */
    public function modifyGoodAttr(){
        $id = I('post.id');
        if(regex($id,'number')){ //编辑
            if(GoodsAttrModel::modifyGoodsAttr($id,$_POST)){
                apiReturn(CodeModel::CORRECT,'编辑成功');
            }
        }else{ //添加
            if(GoodsAttrModel::addGoodsAttr($_POST)){
                apiReturn(CodeModel::CORRECT,'添加成功');
            }
        }
        apiReturn(CodeModel::ERROR,'操作失败，请刷新重试！');
    }

    /**
     * 添加、编辑优惠
     */
    public function modifyDiscount(){
        $id = I('post.id');
        if(regex($id,'number')){ //编辑
            if(DiscountModel::modifyDiscount($id,$_POST)){
                apiReturn(CodeModel::CORRECT,'编辑成功');
            }else{
                apiReturn(CodeModel::ERROR,'编辑失败');
            }
        }else{ //添加
            $data = M('discount')->create($_POST);
            if(empty($data)){
                apiReturn(CodeModel::ERROR,M()->getDbError());
            }
            if(DiscountModel::addDiscount($data)){
                apiReturn(CodeModel::CORRECT,'添加成功');
            }else{
                apiReturn(CodeModel::ERROR,'添加失败');
            }
        }
    }
    //折扣
    public function discount(){
        $list = DiscountModel::getAllDiscount();
        $this->assign('list',$list);
        $this->display ();
    }

    //库存关系
    public function stockInfo(){
        $where = array();
        $searchtype = I ( 'searchtype' );
        $keyword = I ( 'keyword' );
        if($searchtype && $keyword){
            switch ($searchtype) {
                case '1' : $where ['title'] = array ( 'like','%' . $keyword . '%'); break;
                case '2' : if (is_numeric ( $keyword )) { $where ['id'] = $keyword; } break;
                case '3' : $where['channelname'][]= array('like','%'.$keyword.'%');break;
            }
        }
        if ($_REQUEST['pid']>0) {
            $where['sortpath'][]= array('like','%,'.$_REQUEST['pid'].',%');
        }
        if ($_REQUEST['supplyid']>0) {
            $where ['supplyid'] = $_REQUEST['supplyid'];
        }
        if ( is_number($_REQUEST['status'])) {
            $where ['status'] = $_REQUEST['status'];
        }
        if (is_number($_REQUEST['good_type'])) {
            $where ['good_type'] = $_REQUEST['good_type'];
        }
        //是否预警
        if(isset($_REQUEST['stock_warn']) && $_REQUEST['stock_warn'] ==1){
            $where ['_string'] = ' stock <= stock_warn and stock_warn > 0 ';
        }
        //可售天数
        if($_REQUEST['sold_day_type'] && $_REQUEST['sold_day_way'] &&  is_number($_REQUEST['sold_day_val'])){
            if($_REQUEST['sold_day_type'] ==1){ //周平均可售天
                $where ['days_by_week'] =array( $_REQUEST['sold_day_way'], $_REQUEST['sold_day_val']);
            }else{//月平均可售天
                $where ['days_by_month'] =array( $_REQUEST['sold_day_way'], $_REQUEST['sold_day_val']);
            }
        }
        //已下架天数
        if( $_REQUEST['under_way'] && is_number($_REQUEST['under_val'])){
            $time = intval($_REQUEST['under_val']);
            $where ['status'] = \Common\Model\ContentModel::SOLD_OUT;
            $where ['under_time'] =array($_REQUEST['under_way'],date('Y-m-d',strtotime("-$time day")).' 00:00:00');
        }
        $order = 'update_time desc';
        if((isset($_REQUEST['ranktype']) && is_number($_REQUEST['ranktype']))&& (isset($_REQUEST['rank']) && !empty($_REQUEST['rank'])) ){
           switch ($_REQUEST['ranktype']) {
               case '1' : $order = 'update_time '. $_REQUEST['rank']; break;
               case '2' : $order = 'id '. $_REQUEST['rank']; break;
               case '3' : $order = 'days_by_week '. $_REQUEST['rank']; break;
               case '4' : $order = 'days_by_month '. $_REQUEST['rank']; break;
               case '5' : $order = 'sold '. $_REQUEST['rank']; break;
           }
        }
        // 分页
        $p = intval ( I ( 'p' ) );
        $p = $p ? $p : 1;
        $row = C ( 'VAR_PAGESIZE' );
        $rs = M ( "content" )->where ( $where )->order ( $order)->page ( $p, $row );
        $list = $rs->select ();
        foreach($list as &$val){
            if($val['under_time']){
                $val['under_time']=ceil((time()- strtotime( $val['under_time'])) /86400);
            }
        }
        $this->assign ( "list", $list );
        $count = $rs->where ( $where )->count ();
        if ($count > $row) {
            $page = new \Think\Page ( $count, $row );
            $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
            $this->assign ( 'page', $page->show () );
        }

        $where=array();
        // 输出分类
        $where['sortpath']= array('like','%,2,%');
        $where['id']=get_role();
        $list = M ( "Channel" )->where($where)->order ( 'sort asc' )->select ();
        $list = list_to_tree ( $list );
        $this->assign ( "catlist", $list );
        // 输出供应商列表
        $where=array();
        $where['status']=1;
        $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
        $this->assign ( "supplylist", $list );
        $this->display ('stock_info');
    }

    public function goodsStatusLog(){
        $where = array();
        $searchtype = I ( 'searchtype' );
        $keyword = I ( 'keyword' );
        if($searchtype && $keyword){
            switch ($searchtype) {
                case '1' : $where ['c.title'] = array ( 'like','%' . $keyword . '%'); break;
                case '2' : if (is_numeric ( $keyword )) { $where ['c.id'] = $keyword; } break;
                case '3' : $where['c.channelname'][]= array('like','%'.$keyword.'%');break;
            }
        }
        if ($_REQUEST['pid']>0) {
            $where['c.sortpath'][]= array('like','%,'.$_REQUEST['pid'].',%');
        }
        if ($_REQUEST['supplyid']>0) {
            $where ['c.supplyid'] = $_REQUEST['supplyid'];
        }
        if ( is_number($_REQUEST['status'])) {
            $where ['c.status'] = $_REQUEST['status'];
        }
        if (is_number($_REQUEST['good_type'])) {
            $where ['c.good_type'] = $_REQUEST['good_type'];
        }

        if (is_number($_REQUEST['uptype'])) {
            $where ['l.uptype'] = $_REQUEST['uptype'];
        }

        //已下架天数
        if( $_REQUEST['under_way'] && is_number($_REQUEST['under_val'])){
            $time = intval($_REQUEST['under_val']);
            $where ['c.status'] = \Common\Model\ContentModel::SOLD_OUT;
            $where ['c.under_time'] =array($_REQUEST['under_way'],date('Y-m-d',strtotime("-$time day")).' 00:00:00');
        }

        $order = 'c.update_time desc';
        if((isset($_REQUEST['ranktype']) && is_number($_REQUEST['ranktype']))&& (isset($_REQUEST['rank']) && !empty($_REQUEST['rank'])) ){
            switch ($_REQUEST['ranktype']) {
                case '1' : $order = 'c.update_time '. $_REQUEST['rank']; break;
                case '2' : $order = 'c.id '. $_REQUEST['rank']; break;
                case '3' : $order = 'c.sold '. $_REQUEST['rank']; break;
            }
        }
        $field ='c.id,c.title,c.namecn,c.channelname,c.supplyname,c.stock,c.under_time,l.old_stock,l.uptype,l.type,l.operator,l.note,l.addtime';
        $row = C ( 'VAR_PAGESIZE' );
        $count = M('content')->alias('c')->join("my_product_status_log as l on c.id = l.productid")
            ->where($where)->field($field)->count();
        $page = new  \Think\Page ( $count, $row );

        $list = M('content')->alias('c')->join("my_product_status_log as l on c.id = l.productid")
            ->where($where)->field($field)->limit($page->firstRow.",".$page->listRows)->order($order)->select();
        foreach($list as &$val){
            if($val['under_time']){
                $val['under_time']=ceil((time()- strtotime( $val['under_time'])) /86400);
            }
        }
        $this->assign("list",$list);
        $this->assign("page",$page->show());
        $where=array();
        // 输出分类
        $where['sortpath']= array('like','%,2,%');
        $where['id']=get_role();
        $list = M ( "Channel" )->where($where)->order ( 'sort asc' )->select ();
        $list = list_to_tree ( $list );
        $this->assign ( "catlist", $list );
        // 输出供应商列表
        $where=array();
        $where['status']=1;
        $list = M ( "supply" )->where($where)->order ( 'sort asc' )->select ();
        $this->assign ( "supplylist", $list );
        $this->display ('goods_status_log');
    }

    /**
     * 上架操作
     */
    public function changeContentState(){
        $id = I('post.id');
        $status = I('post.status');
        if(regex($id,'number') && is_number($status)){
            $data['under_time'] = date('Y-m-d H:i:s');
            $data['status'] = $status;
            if(ContentModel::modifyContent($id,$data,false)){
                $rs =ContentModel::getContentById($id,'stock');
                $logdata['productid'] = $id;
                $logdata['old_stock'] = $rs['stock'];
                $logdata['type'] = $status;
                $logdata['uptype'] = ProductStatusLogModel::UPTYPE_ADMIN;
                $logdata['operator'] = 'admin';
                $logdata['note'] =  I('post.note');
                ProductStatusLogModel::addProductStatusLog($logdata);
                apiReturn(CodeModel::CORRECT);
            }else{
                apiReturn(CodeModel::ERROR,'修改状态失败');
            }
        }else{
            apiReturn(CodeModel::ERROR,'参数错误，请刷新重试');
        }
    }
}
?>