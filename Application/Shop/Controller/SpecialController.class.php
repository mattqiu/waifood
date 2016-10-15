<?php
/**
 * 专题
 * @author eiver
 *
 */
namespace Shop\Controller;

class SpecialController extends BaseController
{
	public function index($id)
	{
		$special = M('Special')->find($id);
		if( !$special ){
			E('没有找到该页面');
		}
		
		//继承
		if( $special['inherit_id'] > 0 ){
			$inherit = M('Special')->find( $special['inherit_id'] );
			if( $inherit ){
				$special['content'] = $inherit['content'];
			}
		}
		
		//解析内容
		$paramKeys = !empty($special['param_keys']) ? json_decode($special['param_keys'], true) : array();
		$paramValues = !empty($special['param_values']) ? json_decode($special['param_values'], true) : array();
		$this->assign('paramKeys', $paramKeys);
		$this->assign('paramValues', $paramValues);
		$content = $this->fetch('', $special['php'] . htmlspecialchars_decode($special['content']));
		
		//显示
		$this->assign('page_title', $special['page_title']);
		$this->assign('page_keywords', $special['page_keywords']);
		$this->assign('page_desc', $special['page_desc']);
		$this->assign('style', $special['style']);
		$this->assign('script', $special['script']);
		$this->assign('content', $content);
		$this->display();
	}
	
}
?>