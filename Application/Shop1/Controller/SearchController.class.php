<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;

class SearchController extends BaseController { 
		public function index($id = 2,$order=1,$price='',$brand='',$keyword='',$group='') {
			//$creditid=C('DEFAULT_CREDIT_CHANNEL');
			$where=array();
			$where['status']=1;
			if(isset($id)){
				$where['sortpath'][]= array('like','%,'.$id.',%');
			}else{
				$where['sortpath'][]= array('like','%,2,%');
			} 
			$keyword=parse_param($keyword,true);
			//$where['sortpath'][]= array('notlike','%,'.$creditid.',%');
			
			
			if(!isN($keyword)){ 
				$where['_string'] = ' (title like "%'.$keyword.'%")  OR ( keywords like "%'.$keyword.'") '; 
				$this->addKeyword($keyword);
			}
			if($group!=''){
				$ids=M('magic')->where('id='.$group)->getField('contentids');
				$ids=str2arr($ids);
				$where['id']=array('in',$ids);
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
			 
			 
			// 分页
			$p = intval ( I ( 'p' ) );
			$p = $p ? $p : 1;
			$row = C ( 'VAR_PAGESIZE' );
		
			$rs = M ( "content" )->field('id,title,indexpic,price,price1,unit,stock,origin')->where ( $where )->order ( $orderstr )->page ( $p, $row );
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
    	$this->assign ( 'origin', $origin); 
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
			 
			 
			//4. seo信息 
			if($group!=''){
				$title=get_cate($group,'magictype');
			}else{
			$title=$keyword.' - search ';
			}
			 
			$keywords = $keyword.','.$title;
			$description = $keyword.','.$title ;
			
			$this->assign('title',$title);
			$this->assign('keywords',$keywords);
			$this->assign('description',$description);
			$this->assign('keyword',$keyword);
			$this->display ();
		}
		 
	
	/**
	 * 记录关键词
	 * @param string $keyword
	 */
	protected function addKeyword($keyword=''){
		if(strlen($keyword)>0){
			
			$data=array();
			$where=array();
			$where['title']=$keyword;
			$db=M('keyword')->where($where)->find();
			if($db){
				$data['userid']=get_userid();
				$data['times']=$db['times']+1; 
				$data['addtime']=time_format();
				$data['addip']=get_client_ip();
				$db=M('keyword')->where($where)->save($data);
			}else{
				$data['title']=$keyword;
				$data['userid']=get_userid();
				$data['times']=1; 
				$data['addip']=get_client_ip();
				$db=M('keyword')->add($data);
			}
		}
	}
	 
}
?>