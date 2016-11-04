<?php
namespace Common\Model;
use Think\Model;
class CategoryModel extends Model {
    const PRODUCT = 2;// 产品
    const NORMAL = 1;// 正常
    const CATEGORY_CACHE_KEY = 'CATEGORY:PID:';
    const CATEGORY_LIST_CACHE_KEY = 'CATEGORY:PID:LIST';

    public static function getCategoryByid( $pid=self::PRODUCT){
        $key = self::CATEGORY_CACHE_KEY . $pid ;
        if(!$category = S($key)){
            $where['status'] = self::NORMAL;
            $where['pid'] = $pid;
            $category =  M('Channel')->where($where)->order('sort asc')->select();
            S($key,$category,86400*30);
            return $category;
        }
        return $category;
    }

	/**
	 * 分类列表
	 */
    public static function getCategory( $pid=self::PRODUCT){
        $key = self::CATEGORY_LIST_CACHE_KEY . $pid ;
        if(!$category = S($key)){
            $where['status'] = self::NORMAL;
            $where['pid'] = $pid;
            $category =  M('Channel')->where($where)->order('sort asc')->select();
            foreach ($category as $key => &$val) {
                if ($val['pid']) {
                    $val['child'] = self::getCategoryByid($val['id']);
                }
            }
            S($key,$category,86400*30);//一个月
            return $category;
        }
        return $category;
    }


}

?>