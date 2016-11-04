<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller; 
use Common\Model\CategoryModel;

class CategoryController extends BaseController {
	/**
	 * 分类列表
	 */
    public function index(){
        $channel =  CategoryModel::getCategory();
    	$this->assign('title','Categories');
    	$this->assign ( 'channel', $channel );
    	$this->display('Product/category');
    	
    } 
}
?>