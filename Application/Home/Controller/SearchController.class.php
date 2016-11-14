<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

class SearchController extends BaseController {
	public function index($id = 0, $order = 1, $price = '', $brand = '', $keyword = '', $group = '',$sort='desc') {
		$creditid = C ( 'DEFAULT_CREDIT_CHANNEL' );
		$where = array ();
		$where ['status'] = 1;
	    $where['sortpath'][]= array('like','%,2,%'); 
		if (! isN ( $keyword )) {
			$where ['_string'] = ' (title like "%' . $keyword . '%")  OR ( keywords like "%' . $keyword . '") ';
			$this->addKeyword ( $keyword );
		}
		if ($group != '') {
			// $ids=M('magic')->where('id='.$group)->getField('contentids');
			// $ids=str2arr($ids);
			// $where['id']=array('in',$ids);
			$where [$group] = 1;
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
        $list = M ( "content" )->field ( 'id,title,indexpic,price,price1,description,unit,storage,origin,brand,stock' )->where ( $where )->order ( $orderstr )->select ();
        if(empty($list)){
            $this->assign ( "noSearch", 'No Results Found' );
        }else{
            $this->assign ( "list", $list );
        }
		$cateinfo = M ( 'channel' )->field ( 'id,pid' )->where ( 'id=' . $id )->find ();
		
		$this->assign ( 'id', $id );
		$this->assign ( 'brand', $brand );
		$this->assign ( 'price', $price );
		$this->assign ( 'order', $order );
		$this->assign ( 'sort', $sort );

		// 4. seo信息
		if ($group != '') {
			switch($group){
				case 'tag1':
					$title='限时特惠';
					break;
				case 'tag2':
					$title='推荐产品';
					break;
			}
		} else {
			$title = $keyword;
		}
		
		$keywords = $keyword . ',' . $title;
		$description = $keyword . ',' . $title;
		
		$this->assign ( 'title', $title );
		$this->assign ( 'keywords', $keywords );
		$this->assign ( 'description', $description );
		$this->assign ( 'keyword', $keyword );
        $this->display('Product/lists');
	}
	
	/**
	 * 记录关键词
	 * 
	 * @param string $keyword        	
	 */
	protected function addKeyword($keyword = '') {
		if (strlen ( $keyword ) > 0) {
			
			$data = array ();
			$where = array ();
			$where ['title'] = $keyword;
			$db = M ( 'keyword' )->where ( $where )->find ();
			if ($db) {
				$data ['userid'] = get_userid ();
				$data ['times'] = $db ['times'] + 1;
				$data ['addtime'] = time_format ();
				$data ['addip'] = get_client_ip ();
				$db = M ( 'keyword' )->where ( $where )->save ( $data );
			} else {
				$data ['title'] = $keyword;
				$data ['userid'] = get_userid ();
				$data ['times'] = 1;
				$data ['addip'] = get_client_ip ();
				$db = M ( 'keyword' )->add ( $data );
			}
		}
	}
}
?>