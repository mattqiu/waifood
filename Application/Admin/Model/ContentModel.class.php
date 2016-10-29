<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 属性模型
 * @author huajie <banhuajie@163.com>
 */

class ContentModel extends Model {

    /* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '内容标识必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    	array('name', 'checkName', '该分类下相同内容标识已存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
    	array('title', '1,100', '标题长度不能超过100个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
       	array('pid', 'require', '必须选择所属分类', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH), 
    	array('pid', '0', '必须选择所属分类', self::VALUE_VALIDATE, 'notequal', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('addip', 'get_client_ip', self::MODEL_INSERT, 'string')
        //array('position', 'getPosition', self::MODEL_BOTH, 'callback'),
    );

    /* 操作的表名 */
    protected $table_name = null;

    /**
     * 检查同一张表是否有相同的字段
     * @author huajie <banhuajie@163.com>
     */
    protected function checkName(){
    	$name = I('post.name');
    	$pid = I('post.pid');
    	$id = I('post.id');
    	$map = array('name'=>$name, 'pid'=>$pid);
    	if(!empty($id)){
    		$map['id'] = array('neq', $id);
    	}
    	$res = $this->where($map)->find();
    	return empty($res);
    }

    /**
     * 生成推荐位的值
     * @return number 推荐位
     * @author huajie <banhuajie@163.com>
     */
    protected function getPosition(){
    	$position = I('post.position');
    	if(!is_array($position)){
    		return 0;
    	}else{
    		$pos = 0;
    		foreach ($position as $key=>$value){
    			$pos += $value;		//将各个推荐位的值相加
    		}
    		return $pos;
    	}
    }

    public static function underCenter($id){
        $con['id'] = $id;
        $data['under_time'] = date('Y-m-d H:i:s',time());
        M('content')->where($con)->save($data);
    }
    
}
