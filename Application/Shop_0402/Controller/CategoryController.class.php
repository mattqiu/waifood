<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller; 
class CategoryController extends BaseController {
	/**
	 * 分类列表
	 */
    public function index($rootid=2,$pid=0){
    	$where=array();
    	$where['status']=1;
    	$where['id']=array('neq',$rootid);
    	$where['sortpath']=array('like','%,'.$rootid.',%');
    	$channel=M('channel')->where($where)->order('sort asc')->select();

    	$this->assign('title','分类列表');
    	$this->assign ( 'channel', $channel );
    	$this->display('Product/category');
    	
    } 
}
?>