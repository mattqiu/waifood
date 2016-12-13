<?php
/**
 * 商品价格解析类
 * 同一商品不同型号不同价格处理方案
 * $priceext="10￠name1￠info1￠info2¤10￠name2￠info1￠info2¤10￠name3￠info1￠info2¤";
 */
namespace Shop\Controller; 
class PriceController extends BaseController {
	
	/**
	 * 根据产品ID取产品价格信息
	 * @param number $pid
	 * @return multitype:
	 */
	public function getPriceInfo($pid=0){
		$price=M('content')->field('priceinfo')->find($pid); 
		if($price){
			$priceext=$price['priceinfo'];
		}else{
			$priceext='';
		}
		//$priceext="10￠name1￠info1￠info2¤10￠name1￠info1￠info2¤10￠name1￠info1￠info2¤10￠name2￠info1￠info2¤10￠name3￠info1￠info2¤0";
		$arr=str2arr($priceext, '¤');
		$arr=array_filter($arr);
		$arr=array_unique($arr);
		foreach($arr as $item){
			$items[]=str2arr($item,'￠');
		}
		return ($items); 
	}	
	
	/**
	 * 根据型号取价格
	 * @param number $pid
	 * @param string $name
	 * @return unknown|boolean
	 */
	public function getPriceByName($pid=0,$name=''){
		$arr=$this->getPriceInfo($pid);
		foreach ($arr as $key=>$value){ 
			if($name==$arr[$key][0]){
				return ($arr[$key][1]);
				break; 
			}  
		}
		return false;
	}
	
	public function getPrice($pid=0,$price=0,$name=''){
		$num=0;
		if($name!=''){
			$num= $this->getPriceByName($pid,$name);
		}else{
			$num= $price;
		}
		return to_price($num);
	}
	
	
}
?>