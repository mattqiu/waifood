<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller; 
class ContentController extends BaseController {

    public function view($id = 0) {
		// 1. 基本信息
		$where = array ();
    	$where['sortpath']= array('like','%,1,%');
		$where ['status'] = 1;
    	$where['id']=$id;
		$content = M ( "content" )->where ( $where )->find ();
		if ($content) {
			$this->assign ( "content", $content );
			// 2. 栏目信息
			$cateinfo = M ( 'channel' )->where ( 'id=' . $content ['pid'] )->find ();
			// 没有下级就取同级栏目列表
			$where = array ();
			$where ['status'] = 1;
			$where ['pid'] = $cateinfo ['pid'];
			$channel = M ( 'channel' )->where ( $where )->order ( 'sort asc' )->select ();
			if ($cateinfo ['pid'] == 1) {
				$cateinfo ['remark'] = '';
				$this->assign ( 'fathername', lbl('sitename') );
			} else {
				$this->assign ( 'fathername', get_cate ( $cateinfo ['pid'], 'channel' ) );
			}
			
			// 3. 当前位置信息
			$this->assign('location',get_location($content['pid']));
			
			// 4. 上下篇
			//上一篇
			$where = array ();
			$where ['status'] = 1;
			$where ['pid'] = $cateinfo ['id'];
			$where ['id'] = array('neq',$content['id']);
			$where ['sort'] = array('lt',$content['sort']);
			$order='sort desc';
			$rs = M ( "content" )->where ( $where )->order($order)->find ();
			if($rs){ 
				$this->assign('prev','<a href="'.U('Content/view','id='.$rs['id']).'">'.$rs['title'].'</a>');
			}else{
				$this->assign('prev','none');
			}
			//下一篇
			$where = array ();
			$where ['status'] = 1;
			$where ['pid'] = $cateinfo ['id'];
			$where ['id'] = array('neq',$content['id']);
			$where ['sort'] = array('gt',$content['sort']);
			$order='sort asc';
			$rs = M ( "content" )->where ( $where )->order($order)->find ();
			if($rs){ 
				$this->assign('next','<a href="'.U('Content/view','id='.$rs['id']).'">'.$rs['title'].'</a>');
			}else{
				$this->assign('next','none');
			}
			// 赋值
			$this->assign ( 'pid', $cateinfo ['id'] );
			$this->assign ( 'channel', $channel );
			$this->assign ( 'cateinfo', $cateinfo );
			$this->assign ( 'channelname', $cateinfo ['name'] );
			$this->assign ( 'title', $content ['title'] );
			$this->assign ( 'keywords', $content ['keywords'] );
			$this->assign ( 'description', $content ['description'] );
		} else {
            $this->error ('Sorry,target not exists.',$_SERVER['HTTP_REFERER'] );
		}
		$this->display ('Index:consult');
	}

    /**
	 * 内容管理
	 */
	public function lists($id = null) {
		// 输出当前Content列表
		$where = array ();
		if (isset ( $id )) {
			$where ['sortpath'] = array ('like','%,' . $id . ',%');
		}
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		$rs = M ( "content" )->where ( $where )->order ( 'sort desc' )->page ( $p, $row );
		$list = $rs->select ();
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
			$page->setConfig ( 'prev', 'Prev' );
			$page->setConfig ( 'next', 'Next' );
			$this->assign ( 'page', $page->show () );
		}
		if ($count == 1) {
			$this->redirect ( 'Content/view?id=' . $list [0] ['id'] );
			exit ();
		}
		
		// 当前栏目信息
		$cateinfo = M ( 'channel' )->where ( 'id=' . $id )->find ();
		if($cateinfo){
			// 3. 当前位置信息
			$this->assign('location',get_location($cateinfo['id']));
			
		$where = array ();
		$where ['status'] = 1;
		$where ['id'] = array ('neq',$id );
		$where ['sortpath'] = array ('like',	'%,' . $id . ',%' );
		// 取下级栏目列表
		$channel = M ( 'channel' )->where ( $where )->order ( 'sort asc' )->select ();
		$this->assign ( 'fathername', $cateinfo ['name'] );
		if ($channel == null) {
			// 没有下级就取同级栏目列表
			$where = array ();
			$where ['status'] = 1;
			$where ['pid'] = $cateinfo ['pid'];
			$channel = M ( 'channel' )->where ( $where )->order ( 'sort asc' )->select ();
			if ($cateinfo ['pid'] == 1) {
				$cateinfo ['remark'] = '';
				$this->assign ( 'fathername', lbl('sitename') );
			} else {
				$this->assign ( 'fathername', get_cate ( $cateinfo ['pid'], 'channel' ) );
			}
		}

		// 赋值
		$this->assign ( 'channel', $channel );
		$this->assign ( 'pid', $id );
		$this->assign ( 'cateinfo', $cateinfo );
		$this->assign ( 'channelname', $cateinfo ['name'] );
		
		$this->assign ( 'title', $cateinfo ['name'] );
		$this->assign ( 'keywords', $cateinfo ['name'] );
		$this->assign ( 'description', $cateinfo ['name'] );
		}
		$this->display ('Index:consult_lists');
	}
}
?>