<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller; 
class CategoryController extends BaseController {
	/**
	 * 分类列表
	 */
    public function index( $pid=0){
    	$where=array();
    	$where['status']=1; 
    	$where['depth']=array('gt',1); 
    	$where['sortpath']=array('like','0,2,%'); 
    	$channel=M('Channel')->where($where)->order('sort asc')->select();

    	$this->assign('title','Categories');
    	$this->assign ( 'channel', $channel );
    	$this->display('Product/category');
    	
    } 
}
?>