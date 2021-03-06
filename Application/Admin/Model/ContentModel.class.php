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
use Common\Model\OrderModel;
use Common\Model\ProductStatusLogModel;
use Think\Model;

/**
 * 属性模型
 * @author huajie <banhuajie@163.com>
 */

class ContentModel extends Model {
    const GENERAL_GOODS = 0;//普通商品
    const COMPOSITE_GOODS = 1;//复合商品
    const COMBINATION_OF_GOODS = 2;//组合商品
    const CAN_NEGATIVE_AOLD = 1;//支持可负销售
    const CANNOT_NEGATIVE_AOLD = 0;//不支持可负销售

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

    /**
     * 根据商品id查找商品，
     * @param $id
     * @param string $field  筛选字段
     * @return bool|mixed
     */
    public static function getContentById($id,$field=''){
        if(regex($id,'number')){
            if(!empty($field)){
                return M('content')->field($field)->find($id);
            }else{
                return M('content')->find($id);
            }
        }else{
            return false;
        }
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
    public static function modifyContent($id,$data,$autoUnder = true){
        if(regex($id,'number') && !empty($data)){
            $con['id'] = $id;
            $data['update_time'] = date('Y-m-d H:i:s');
            if(isset($data ['pid']) && $data ['pid']){
                $info = M ( 'channel' )->getById ( $data ['pid'] );
                $data['sortpath']= $info ['sortpath'];
                $data['supplyname']=get_cate($data['supplyid'],'supply');
                $data['channelname']=get_cate($data['pid']);
                session('shop_id',$data['shop_id']);
                    // 更新model_id
                $model_id=$info['model_id'];
                M ( 'content' )->where ( 'id=' . $id )->save ( array ('model_id' =>  $model_id ) );
            }

            if( M('content')->where($con)->save($data)){
                //自动下架
                if($autoUnder == true){
                    $con['id'] = $id;
                    //库存小于1的商品自动下架
                    $con = array();
                    $con['stock'] = array('lt',1);
                    $con['negative'] = self::CANNOT_NEGATIVE_AOLD; //只能下架不支持可负销售的
                    $savedata['status'] = 0;
                    $savedata['under_time'] = date('Y-m-d H:i:s');//下架时间
                    M('content')->where($con)->save($savedata);
                    $field = 'stock';
                    $rs = self::getContentById($id,$field);
                    if( is_number($rs['stock']) && $rs['stock']<1){
                        $logdata['productid'] = $id;
                        $logdata['old_stock'] = $rs['stock'];
                        $logdata['type'] = 0;
                        $logdata['uptype'] = ProductStatusLogModel::UPTYPE_AUTO;
                        $logdata['note'] = '自动下架';
                        ProductStatusLogModel::addProductStatusLog($logdata);
                    }
                }
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @param $type 1 上架 ，0 下架
     */
    public static function contentStatus($id,$type){
        if(regex($id,'number')){
            if($type == 1){
                $con['id'] = $id;
                //库存大于0，状态为下架的的商品自动下架
                $con = array();
                $con['stock'] = array('gt',0);
                $con['status'] = array('eq',0);
                $savedata['status'] = 1;
                $savedata['under_time'] = date('Y-m-d H:i:s');
                M('content')->where($con)->save($savedata);
                $field = 'stock';
                $rs = self::getContentById($id,$field);
                if( is_number($rs['stock']) && $rs['stock'] > 0){
                    $logdata['productid'] = $id;
                    $logdata['old_stock'] = $rs['stock'];
                    $logdata['type'] = 1;
                    $logdata['uptype'] = ProductStatusLogModel::UPTYPE_AUTO;
                    $logdata['note'] = '自动上架';
                    ProductStatusLogModel::addProductStatusLog($logdata);
                }
            }else{
                $con['id'] = $id;
                //库存大于0，状态为下架的的商品自动下架
                $con = array();
                $con['stock'] = array('lt',1);
                $con['negative'] = self::CANNOT_NEGATIVE_AOLD; //只能下架不支持可负销售的
                $savedata['status'] = 0;
                $savedata['under_time'] = date('Y-m-d H:i:s');//下架时间
                M('content')->where($con)->save($savedata);
                $field = 'stock';
                $rs = self::getContentById($id,$field);
                if( is_number($rs['stock']) && $rs['stock']<1){
                    $logdata['productid'] = $id;
                    $logdata['old_stock'] = $rs['stock'];
                    $logdata['type'] = 0;
                    $logdata['uptype'] = ProductStatusLogModel::UPTYPE_AUTO;
                    $logdata['note'] = '自动下架';
                    ProductStatusLogModel::addProductStatusLog($logdata);
                }
            }
        }
    }

    /**
     * 判断组合或复合商品的子商品是否支持可负销售
     * @param $groupid
     * @return int
     */
    public static function checkNegativeSold($groupid){
        if(!empty($groupid) && strpos($groupid,',')>0){
            $idsArr =array_filter(explode('|',$groupid));
            $data = array();
            foreach($idsArr as $key=>$val){
                $idarr =array_filter(explode(',',$val));
                if(regex($idarr[0],'number')){
                    $goods=\Admin\Model\ContentModel::getContentById($idarr[0]);
                    $data[$key]['id']=$idarr[0];
                    $data[$key]['negative']=$goods['negative'];
                }
            }
            //按negative数字从小到大排序（组合商品的可负状态取决子商品是否支持）
            $datanew = myArraySort($data,'negative',SORT_ASC);
            if($datanew[0]['negative'] == self::CAN_NEGATIVE_AOLD){
                return true;
            }else{
                return $datanew[0]['id'];
            }
        }
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
