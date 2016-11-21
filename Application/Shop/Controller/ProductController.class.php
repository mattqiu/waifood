<?php
namespace Shop\Controller;
class ProductController extends BaseController {
	  
    /**
     * 产品列表
     */
    public function lists() {
        $id = I('id');
        $brand = I('brand');
        $origin = I('origin');

    	$creditid=C('DEFAULT_CREDIT_CHANNEL');
    	$where=array();
    	$where['stock']=array('gt',0);
    	$where['status']=1; 
    	if(isset($id)){
    		$where['sortpath'][]= array('like','%,'.$id.',%');
    	}else{ 
    		$where['sortpath'][]= array('like','%,2,%');
    	}
        $where['sortpath'][]= array('notlike','%,'.$creditid.',%');
        $orderstr='sort desc';
        if( I('price')){
            $orderstr = 'price '.I('price');
        }
        if( I('sales')){
            $orderstr = 'sold '.I('sales');
        }

    	//品牌
    	if(!isN($brand)){
    		//$brand = str_replace('_',' ',$brand);
    		$brand = parse_param($brand,true);
    		$where['brand']=$brand;
    	}
    	//品牌
    	if(!isN($origin)){
    		$origin = parse_param($origin,true);
    		$where['origin']=$origin;
    	}

    	// 分页
    	$p = intval ( I ( 'p' ) );
    	$p = $p ? $p : 1;
    	$row = C ( 'VAR_PAGESIZE' );
        $field = 'id,title,indexpic,price,price1,unit,stock,origin,storage,tag1,tag2,tag3,tag4';
    	$rs = M ( "content" )->field($field)->where ( $where )->order ( $orderstr )->page ( $p, $row );
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
//     	$where['sortpath']= array('like','%,2,%');
//     	$where['pid']= $id;
		$where['status']=1;
    	$where['sortpath']= array('like','%,'.$id.',%');
    	$brandArr=M('content')->where($where)->distinct(true)->order('brand asc')->getField('brand',true); 
    	$brandArr = arr2clr($brandArr);
//     	$brandStr = arr2str($brandArr);
//     	$brandStr=str_replace(' ','%20',$brandStr);
//     	$brandArr=str2arr($brandStr);
    	$this->assign('brandlist',$brandArr);
    	
    	//2.1 产地
    	$where=array();
//     	$where['sortpath']= array('like','%,2,%');
//     	$where['pid']= $id;
    	$where['sortpath']= array('like','%,'.$id.',%');
    	$brandArr=M('content')->where($where)->distinct(true)->order('origin asc')->getField('origin',true); 
    	$brandArr = arr2clr($brandArr);
    	$this->assign('originlist',$brandArr);

    	//3. 价格区间
    	$priceArr=array(1,50,100,200,500); 
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
    public function view(){
        $id = I('id');
    	$where['status']=1;
    	$where['id']=$id;
    	$where['sortpath']= array('notlike','%,1,%');
        $good = M ( "content" )->where($where)->find ();
    	if($good){
            $this->assign ( "good", $good );
//    	    if(strpos($db['sortpath'],',3,')){
//    	        redirect(U('Service/view','id='.$id));
//    	    }
//    		M('content')->where($where)->setInc('hits');
//    		$url = $db['linkurl'];
//    		if(!isN($url)){
//    			header("location:$url");
//    		}
//    		$arr=str2arr(cookie('view_history'));
//    		$arr=arr2clr($arr);
//    		if(!in_array($id,$arr)){
//    			$arr[]=$id;
//    			cookie('view_history',arr2str($arr));
//    		}

	    	$this->assign ( "gallery", get_imgs($good['images']) );
//	    	$this->assign ( 'title', $db['title']);
    	}else{
    		$this->error('Sorry,target not exists.');
    	}
    	$this->display();
    }
    
    
}
?>