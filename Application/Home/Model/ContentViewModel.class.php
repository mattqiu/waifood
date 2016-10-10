<?php
namespace  Home\Model;
use Think\Model\ViewModel;
/**
 * 调用方法：先取模型表名，再用D方法，接下来跟M一样筛选
 * session('modelext','dish');
    $model=D('ContentView'); 
 * @author Administrator
 *
 */
class ContentViewModel extends ViewModel {
// 	public $viewFields = array (
// 			'content' => array (
// 					'id', 
// 					'title' ,'_type'=>'LEFT'
// 			),
// 			'content_dish' => array (
// 					'ext_price' => 'ext_price',
// 					'_on' => 'content.id=content_dish.id' 
// 			) 
			
// 	);
	
	public $viewFields;  

	public function _initialize(){
		$fields=$this->getFields();
		$data['content']=$fields;
		
		$modelext=session('modelext');
		if (!isN($modelext)){
		$table='content_'.$modelext;
		$fields1=$this->getModelFields($table);
		$data[$table]=$fields1;
		$data[$table]['_on']='content.id='.$table.'.id';
		}
		
		$this->viewFields=$data; 
	}
	
	//得到content字段
	public function getFields(){
		$Model   = M('content');
		$fields = $Model->getDbFields();
		return $fields;
	}
	
	//得到模型字段
	public function getModelFields($table){
		$Model   = M($table);
		$fields = $Model->getDbFields();
		return $fields;
	}
	
	
}

?>