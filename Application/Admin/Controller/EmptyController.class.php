<?php

namespace Admin\Controller;
class EmptyController extends BaseController {
	public function index() {
        $this->redirect ( '/Public/404' );
	}
}
?>