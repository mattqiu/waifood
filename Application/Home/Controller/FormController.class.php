<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

class FormController extends BaseController {
	
	/**
	 * 表单详细
	 *
	 * @param number $id        	
	 */
	public function show($id = 0, $msg = '') {
		$where = array ();
		$where ['status'] = 1;
		$db = M ( "formtype" )->where ( $where )->find ( $id );
		if ($db) {
			
			$where = array ();
			if (isset ( $id )) {
				$where ['type'] = $id;
			}
			$where ['userid'] = get_userid ();
			
			// 分页
			$p = intval ( I ( 'p' ) );
			$p = $p ? $p : 1;
			$row = C ( 'VAR_PAGESIZE' );
			
			$rs = M ( "Form" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
			$list = $rs->select ();
			$this->assign ( "list", $list );
			$count = $rs->where ( $where )->count ();
			
			if ($count > $row) {
				$page = new \Think\Page ( $count, $row );
				$page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
				$page->setConfig ( 'prev', '上一页' );
				$page->setConfig ( 'next', '下一页' );
				$this->assign ( 'page', $page->showm () );
			}
			
			$this->assign ( "db", $db );
			$this->assign ( "id", $id );
			$this->assign ( 'title', $db ['name'] . '-在线技术指导' );
			$this->assign ( 'remark', $db ['remark'] );
			// $this->assign ( 'html', $html );
		} else {
			$this->error ( '对不起，您访问的信息不存在！' );
		}
		$this->display ();
	}
	public function save() {
		if (IS_POST) {
			$db = D ( "form" );
			$data = empty ( $data ) ? $_POST : $data;
			if (isN ( $data ['ext1'] )) {
				$this->error ( '对不起，请输入要咨询的问题！' );
			}
			$data ['userid'] = get_userid ();
			$data ['addip'] = get_client_ip ();
			if (false !== $db->add ( $data )) {
				$this->success ( "恭喜，提交成功！" );
			} else {
				$this->error ( '对不起，提交失败！' );
			}
		} else {
			$this->display ();
		}
	}
}
?>