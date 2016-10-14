<?php

namespace Home\Controller; 
class EmptyController extends BaseController {
	public function index() {
		$title = C('config.WEB_SITE_TITLE');
		$this->assign('title',$title);
		$this->display('Index/404');
	}  
}
?>