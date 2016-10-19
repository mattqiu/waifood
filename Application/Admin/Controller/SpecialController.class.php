<?php
/**
 * 专题
 * @author eiver
 *
 */
namespace Admin\Controller;
use Think\Model;

class SpecialController extends BaseController
{
	public function index()
	{
		$where = null;
		$keyword = I ( 'keyword' );
		$params = array();

		if( $keyword ){
			$where['name'] = array('like', '%'.$keyword.'%');
			$params['keyword'] = $keyword;
		}
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p>0 ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( 'Special' )->field('')->where ( $where )->order ( 'id desc' )->page ( $p, $row );
		$rs1 = clone $rs;
		$count = $rs1->count ();
		unset($rs1);
		$list = $rs->select ();
		
		$this->assign ( "list", $list );
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( 'page', $page->show () );
		}

		$this->assign ( 'p', $params );
		$this->display ('Special/index');
	}
	
	public function add()
	{
		if( IS_POST ){
			$Model = M('Special');
			if( !$Model->create($_POST, Model::MODEL_INSERT) ) $this->error('操作失败');
			if( !$Model->add() ) $this->error('操作失败');
			return $this->success('操作成功', U(CONTROLLER_NAME.'/index'));
		}
		$this->display ('Special/edit');
	}
	
	// 编辑
	public function edit()
	{
		$id = I ( 'id' );	
		$db = M ('Special')->find($id);
		if( !$db ) E('错误的地址');
		$params = !empty($db['param_keys']) ? json_decode($db['param_keys'], true) : null;
		
		if( IS_POST ){
			$Model = M('Special');
			if( !$Model->create($_POST, Model::MODEL_UPDATE) ) $this->error('操作失败');
			
			$values = array();
			foreach ( $params as $key=>$item ){
				$values[$key] = I('p_'.$key);
			}
			
			$Model->param_values = json_encode($values);
			if( !$Model->save() ) $this->error('操作失败'.$Model->getLastSql());
			return $this->success('操作成功', U(CONTROLLER_NAME.'/edit', array('id'=>$id)));
		}
		
		if( $params ){
			$values = !empty($db['param_values']) ? json_decode($db['param_values'], true) : null;
			foreach ($params as $key=>$item){
				if( isset($values[$key]) ){
					$params[$key]['value'] = $values[$key];
				}
			}
		}
		
		$this->assign ('db', $db);
		$this->assign ('params', $params);
		$this->display ();
	}
	
	//高级设置
	public function editSetting()
	{
		$id = I ( 'id' );
		$db = M ('Special')->find($id);
		if( !$db ) E('错误的地址');
		
		if( IS_POST ){
			$Model = M('Special');
			if( !$Model->create($_POST, Model::MODEL_UPDATE) ) $this->error('操作失败');
			//dump($Model);
			//exit;
			if( !$Model->save() ) $this->error('操作失败'.$Model->getLastSql());
			return $this->success('操作成功', U(CONTROLLER_NAME.'/editSetting', array('id'=>$id)));
		}
		$this->assign ('db', $db);
		$this->assign ('params', $params);
		$this->display ('Special/editSetting');
	}
	
	// 删除
	public function remove()
	{
		$id = I('id');
		if( !M ('Special')->delete ($id) ) $this->error ( "删除失败" );
		$this->success ( "删除成功！" );
	}
}
?>