<?php
/**
 * 设置openid，暂时只管28位的微信
 */
function openid($open_id = '') {
    if ($open_id === '') {
        return cookie('_openid');
    } else {
        if (strlen($open_id) == 28) {
            cookie('_openid', $open_id, 3600 * 24 * 30);
        } else {
            cookie('_openid', null);
        }
    }
}

/**
 * 是否绑定了微信
 * @param string $openid
 */
function is_bind($openid=''){
   $uid=get_userid();
   $ret= false;
   if($uid!=0){
       if($openid==''){
           $openid=openid();
       }
       if(session('wechatid')){
       if($openid==session('wechatid')){
           $ret= true;
       }
       }else{
           $ret=false;
       }
   }
   
   return $ret;
}


//判断是否微信浏览器
function is_wechat(){
    $user_agent = $_SERVER ['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MicroMessenger') === false) { 
        return false;
    } else { 
        return true;
    }
}

function Uu($url,$param=''){
   return U($url,$param.set_fid()); 
}

function get_index($id=0){
	$where=array();
	$where['status']=1;
	if(is_numeric($id)){
		$where['pid']=$id;
	}else{
		$where['pid']=array(neq,C('DEFAULT_CREDIT_CHANNEL'));
		$where[$id]=1;
	} 
	return(get_list($where,'product','id,title,price,indexpic,tag1,tag2,status,pid','sort desc',3));
}


//带缓存的列表读取函数
function get_list($id=null,$tbl='content',$col='*',$order='id desc',$num=0,$ch=true){
    if(empty($id)){
        return false;
    }
    $where = is_numeric($id)?array('id'=>$id):$id;
    if($num==0){
        $limit=null;
    }else{
        $limit=$num;
    }
    if(!$ch){
        $cache= (M ( $tbl )->field($col)->order($order)->where ( $where )->limit($limit)->select (  ));
    }else {
        $key=md5(serialize($where).'_'.$tbl.'_'.$col.'_'.$order);
        $cache= F('list_'.$key);
        if(!$cache){
            $cache= (M ( $tbl )->field($col)->order($order)->where ( $where )->limit($limit)->select());
            F('list_'.$key,$cache);
        }
    }
    return $cache;

}
?>