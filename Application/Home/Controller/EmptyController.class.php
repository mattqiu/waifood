<?php

namespace Home\Controller; 
class EmptyController extends BaseController {
	public function index() {
        $this->redirect ( '/Public/404' );
	}
}
?>