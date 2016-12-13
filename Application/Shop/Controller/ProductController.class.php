<?php
namespace Shop\Controller;
use Common\Model\ContentModel;

class ProductController extends BaseController {
	  
    /**
     * 产品列表
     */
    public function lists() {
        $orderstr='sort desc';
        if( I('price')){
            $orderstr = 'price '.I('price');
        }
        if( I('sales')){
            $orderstr = 'sold '.I('sales');
        }
        if($_REQUEST['group'] == ContentModel::NEW_ARRIVAL){
            $title = 'New Arrvial';
            $list  =  ContentModel::getGroupContent(ContentModel::NEW_ARRIVAL,$orderstr);
        }else if($_REQUEST['group'] == ContentModel::RECOMMEND){
            $title =  'Recommend';
            $list  =  ContentModel::getGroupContent(ContentModel::RECOMMEND,$orderstr);
        }else if($_REQUEST['group'] == ContentModel::PROMOTION){
            $title =  'Promotion';
            $list  =  ContentModel::getGroupContent(ContentModel::PROMOTION,$orderstr);
        }else{
            $id = I('id');
            $brand = I('brand');
            $origin = I('origin');
            $keyword=parse_param($_REQUEST['keyword'],true);
            $where = array ();
            $where ['status'] = 1;
            if(regex($id,'number')){
                $title = get_cate ($id) ;
                $where['sortpath'][]= array('like','%,'.$id.',%');
            }elseif($keyword){
                $title=$keyword;
                $where['_string'] = ' (title like "%'.$keyword.'%")  OR ( keywords like "%'.$keyword.'") ';
             //   $this->addKeyword($keyword);
            }else{
                $where['sortpath'][]= array('like','%,2,%');
            }
            //品牌
            if(!isN($brand)){
                $brand = parse_param($brand,true);
                $where['brand']=$brand;
            }
            //品牌
            if(!isN($origin)){
                $origin = parse_param($origin,true);
                $where['origin']=$origin;
            }

            $count = M ("content")->where($where )->count();
            $size = C ( 'VAR_PAGESIZE' );
            $page = new  \Think\Page($count, $size);
            $limit = "$page->firstRow, $page->listRows";
            $field =  'id,title,indexpic,price,price1,description,unit,storage,origin,brand,stock';
            $list = M ( "content" )->field ($field)->where ( $where )->order ( $orderstr )->limit($limit)->select ();
            $where=array();
            $where['status']=1;
            $where['sortpath']= array('like','%,'.$id.',%');
            $brandArr=M('content')->where($where)->distinct(true)->order('brand asc')->getField('brand',true);
            $brandArr = arr2clr($brandArr);
            $this->assign('brandlist',$brandArr);
            //2.1 产地
            $where=array();
            $where['sortpath']= array('like','%,'.$id.',%');
            $brandArr=M('content')->where($where)->distinct(true)->order('origin asc')->getField('origin',true);
            $brandArr = arr2clr($brandArr);
            $this->assign('originlist',$brandArr);
            $this->assign("page",$page->show());
            if(!$title){
                $title='Commodity center';
            }
        }
        $this->assign ( "list", $list );
    	$keywords = $title.lbl('subtitleshop');
    	$description = $title.lbl('subtitleshop');
		$this->assign('title',$title);
		$this->assign('keywords',$keywords);
		$this->assign('description',$description);
    	$this->display ();
    }
    
    /**
     * 产品详细
     * @param number $id
     */
    public function view(){
        $id = I('id');
    	$where['status']=1;
    	$where['id']=$id;
    	$where['sortpath']= array('notlike','%,1,%');
        $good = M ( "content" )->where($where)->find ();
    	if($good){
            $this->assign ( "good", $good );
	    	$this->assign ( "gallery", get_imgs($good['images']) );
    	}else{
    		$this->error('Sorry,target not exists.');
    	}
    	$this->display();
    }

    /**
     * 记录关键词
     * @param string $keyword
     */
    protected function addKeyword($keyword){
        if(strlen($keyword)>0){
            $data=array();
            $where=array();
            $where['title']=$keyword;
            $db=M('keyword')->where($where)->find();
            if($db){
                $data['userid']=get_userid();
                $data['times']=$db['times']+1;
                $data['addtime']=time_format();
                $data['addip']=get_client_ip();
                $db=M('keyword')->where($where)->save($data);
            }else{
                $data['title']=$keyword;
                $data['userid']=get_userid();
                $data['times']=1;
                $data['addip']=get_client_ip();
                $db=M('keyword')->add($data);
            }
        }
    }
    
}
?>