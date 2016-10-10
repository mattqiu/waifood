<?php

namespace Home\Controller;

class IndexController extends BaseController {
	public function index() { 
		$title = C ( 'config.WEB_SITE_TITLE' );
		$this->assign ( 'title', $title );
		$where=array();
		$where['status']=1;
		$where['pid']=2; 
		$channel=M('Channel')->where($where)->order('sort asc')->select(); 
		
		$this->assign('list',$channel);
// 		for($i=2;$i<10;$i++){
// 			$this->assign('index_'.$i,get_index($i));
// 		}
		$this->display ();
	} 
	
	public function img(){
	    $font=(LIB_PATH.'\Think\Verify\ttfs\1.ttf');
	    $pic='1.jpg';
	    $ctrl = new \Think\Image ( 1, $pic );
	    $img = $ctrl->text ( '001',$font,250,'#ffffff',5)->save ( '2.jpg' );
	    we('ok');
	}
}
?>