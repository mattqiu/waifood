<?php

namespace Home\Controller;

use Common\Model\ContentModel;

class IndexController extends BaseController {
	public function index() { 
	    $where=array();
		$where['status']=1;
		$where['pid']=2; 
		$channel=M('Channel')->where($where)->order('sort asc')->select();
        $promotion  =  ContentModel::getGroupContent(ContentModel::PROMOTION);
        $newArrival  =  ContentModel::getGroupContent(ContentModel::NEW_ARRIVAL);
        $recommend  =  ContentModel::getGroupContent(ContentModel::RECOMMEND);
		$this->assign('recommend',$recommend);
		$this->assign('newArrival',$newArrival);
		$this->assign('promotion',$promotion);
		$this->assign('list',$channel);
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