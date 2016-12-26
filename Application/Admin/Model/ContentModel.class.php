<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Common\Model\CodeModel;
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

    public static function getContentById($id){
        if(regex($id,'number')){
            return M('content')->find($id);
        }
        return false;
    }

    public static function underCenter($id){
        $con['id'] = $id;
        $data['under_time'] = date('Y-m-d H:i:s',time());
        M('content')->where($con)->save($data);
    }

    /**
     * 删除商品时，删除对应商品的形象图和详情图
     * @param $id
     */
    public static function delContent($id){
        if(regex($id,'number')){
            $path = substr(C('UPLOAD_PATH'),0,-8);
            $content =  M('content')->find($id);
            //相册
            $img = array_filter(get_imgs($content['images']));
            if(!empty($img)){
                foreach($img as $value){
                    $file = $path.$value;
                    if(file_exists($file)){
                        unlink ($file);
                    }
                }
            }
            //形象图
            if(file_exists($path.$content['indexpic'])){
                unlink ($path.$content['indexpic']);
            }
            //详情图
            preg_match_all("/\<img.*?src\=\"(.*?)\"[^>]*>/i", $content['content'], $match);
            if(is_array($match[1])){
                foreach($match[1] as $val){
                    $file = $path.$val;
                    if(file_exists($file)){
                        unlink ($file);
                    }
                }
            }
            //删除商品
          return  M('content')->delete($id);
        }
        return false;
    }

    /**
     * 编辑商品
     * @param $id
     * @param $data
     * @return bool|\Model
     */
    public static function modifyContent($id,$data){
        if(regex($id,'number') && !empty($data)){
            $con['id'] = $id;
            $data['under_time'] = date('Y-m-d H:i:s');
            $info = M ( 'channel' )->getById ( $data ['pid'] );
            $data['sortpath']= $info ['sortpath'];
            $data['supplyname']=get_cate($data['supplyid'],'supply');
            $data['channelname']=get_cate($data['pid']);
            session('shop_id',$data['shop_id']);
            // 更新model_id
            $model_id=$info['model_id'];
            $db = M ( 'content' )->where ( 'id=' . $id );
            $db->save ( array ('model_id' =>  $model_id ) );
            return M('content')->where($con)->save($data);
        }
        return false;
    }

    /**
     * 添加商品
     * @param $data
     */
    public static function addContent($data){
        $table = D( "content" );
        $data = $table->create ( $data );
        if($data){
            session ( 'last_pid', $data['pid'] );
            //赋默认值：关键词，描述，作者，来源
            if(isN($data['keywords'])){
                $data['keywords']=$data['title'];
            }
            if(isN($data['description'])){
                $data['description']=$data['title'];
            }
            if(isN($data['source'])){
                $data['source']=C('config.WEB_SITE_TITLE');
            }
            if(isN($data['author'])){
                $data['author']='管理员';
            }
            $info = M('Channel')->getById($data['pid']);
            $sortpath = $info ['sortpath'];
            $data['sortpath']=$sortpath;
            $data['sort']=$data['name'];
            $data['addip']=get_client_ip();
            $data['supplyname']=get_cate($data['supplyid'],'supply');
            $data['channelname']=get_cate($data['pid']);
            $data['shop_id']=1;//不知道什么作用？
            return $table->add($data);
        }else{
            apiReturn(CodeModel::ERROR,M()->getError());
        }
    }

}
