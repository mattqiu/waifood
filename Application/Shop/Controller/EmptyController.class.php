<?php
namespace Shop\Controller;
class EmptyController extends BaseController {
	public function index() {
        $this->redirect ( '/Public/404' );
	}
}
?>