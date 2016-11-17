<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller; 
class PointController extends BaseController {
	  
    /**
     * 产品列表
     */
    public function lists($id = 0,$order=1,$price='',$brand='') {
    	$id=C('DEFAULT_CREDIT_CHANNEL');
    	$where=array();
    	$where['status']=1; 
    	if(isset($id)){
    		$where['sortpath']= array('like','%,'.$id.',%');
    	}else{ 
    		$where['sortpath']= array('like','%,2,%');
    	}
    	$orderstr='sort desc';
    	switch($order){
    		case 3: 
    			$orderstr='sold desc';
    			break;
    		case 2: 
    			$orderstr='price asc';
    			break;
    		case 4: 
    			$orderstr='id desc';
    			break;
    	}
    	//价格区间
    	if(!isN($price)){
    		$prices=str2arr($price,'-');
    		if(is_numeric($prices[0])&&is_numeric($prices[1])){
    			$where['price']=array('between',$prices);
    		}else{
    			if(is_numeric($prices[0])){
    				$where['price']=array('egt',$prices[0]);
    			}
    			if(is_numeric($prices[1])){
    				$where['price']=array('elt',$prices[1]);
    			}
    		}
    	}
    	//品牌
    	if(!isN($brand)){
    		$where['brand']=$brand;
    	}
    	
    	// 分页
    	$p = intval ( I ( 'p' ) );
    	$p = $p ? $p : 1;
    	$row = C ( 'VAR_PAGESIZE' );
    
    	$rs = M ( "content" )->field('id,title,indexpic,price,unit')->where ( $where )->order ( $orderstr )->page ( $p, $row );
    	$list = $rs->select ();
    	
    	$this->assign ( "list", $list );
    	$count = $rs->where ( $where )->count ();
    
    	if ($count > $row) {
    		$page = new \Think\Page ( $count, $row );
    		$page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
    		$page->setConfig ( 'prev', '<' );
    		$page->setConfig ( 'next', '>' );
    		$this->assign ( 'page', $page->show () );
    	}
    	
    	$cateinfo=M('channel')->field('id,pid')->where('id='.$id)->find();
    	  
    	$this->assign ( 'id', $id); 
    	$this->assign ( 'brand', $brand); 
    	$this->assign ( 'price', $price);
    	$this->assign ( 'order', $order); 
    	
    	//1. 上下页
    	$pagecount=$page->totalPages;
    	if(!is_numeric($pagecount)){
    		$pagecount=1;
    	}
    		$pageprev='';
    		$pagenext='';
    	if($pagecount>1){
    		if($p==1)
    		{
    			$pagenext=$p+1;
    		}else{
    			if($p==$pagecount){
    				$pageprev=$p-1;
    			}else{
    				$pageprev=$p-1;
    				$pagenext=$p+1;
    			}
    		}
    	}
    	$this->assign('pageprev',$pageprev);
    	$this->assign('pagenext',$pagenext);
    	
    	//2. 品牌
    	$where=array();
    	$where['sortpath']= array('like','%,2,%');
    	$where['pid']= $id;
    	$brandArr=M('content')->where($where)->distinct(true)->getField('brand',true); 
    	$this->assign('brandlist',$brandArr);
    	
    	//3. 价格区间
    	$priceArr=array(1,500,1000,2000,5000); 
    	$newarr=array();
    	foreach($priceArr as $k=>$v){
    		$v1=$priceArr[$k+1]-1;
    		if($v1==-1){
    			$v1='';
    		}
	    	$newarr[]=array($v,$v1);
    	}
    	$this->assign('pricelist',$newarr);
    	
    	//4. seo信息
    	$title = get_cate ($id) ;
    	if(isN($title)){
    		$title='商品中心';
    	}
    	$keywords = $title.lbl('subtitleshop');
    	$description = $title.lbl('subtitleshop');
    	
		$this->assign('title',$title);
		$this->assign('keywords',$keywords);
		$this->assign('description',$description);
    	$this->display ();
    }
    
    /**
     * 产品详细
     * @param number $id
     */
    public function view($id=0){
    	$where=array();
    	$where['status']=1;
    	$db = M ( "content" )->where($where)->find ($id);
    	if($db){
    		$arr=str2arr(cookie('view_history'));
    		$arr=arr2clr($arr);
    		if(!in_array($id,$arr)){
    			$arr[]=$id;
    			cookie('view_history',arr2str($arr));
    		}
	    	$this->assign ( "db", $db );
	    	$this->assign ( "gallery", get_imgs($db['images']) );
	    	$this->assign ( 'title', $db['title']);
    	}else{
    		$this->error('对不起，您访问的信息不存在！');
    	}
    	$this->display();
    	 
    }
    
    
}
?>