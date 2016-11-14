<?php
/**
 * echo字符串，在模板中使用
 * @author eiver
 * @param unknown $str
 */
function ec($str){
	echo $str;
}

/**
 * 根据参数生成URL
 * @author eiver
 * @param array $join   		要加入的参数数组,同名直接覆盖
 * @param array|string $clean	要移除的参数，可为数组
 */
function u_pam($params, $join=array(), $clean=array())
{
	if( !empty($clean) ){
		if( is_array($clean) ){
			foreach ($params as $key => $value) {
				if( in_array($key, $clean) ){
					unset($params[$key]);
					continue;
				}
			}
		}
		if( is_string($clean) && isset($params[$clean]) ){
			unset($params[$clean]);
		}
	}
	return !empty($join) && is_array($join) ? array_merge($params, $join) : $params;
}


/**
 * 清除缓存
 * @return boolean
 */
function clr_cache(){
	if (file_exists ( RUNTIME_PATH)) {
		 
		$ctrl=new \Org\Net\File();
		$ctrl->unlinkDir(RUNTIME_PATH);
	}
	return true;
}

/**
 * 获取库存
 */
function get_stock($productid=0){
	$where=array();
	$where['id']=$productid;
	$where['status']=1;
	$stock=M('content')->where($where)->getField('stock');
	return (intval($stock));
}

/**
 * 订单库存为0：下架
 * @param number $productid
 */
function set_order_onoff($orderno='',$status=0){
	if($status==1){
		//退库存
		$where=array();
		$where['orderno']=$orderno;
		$list=M('order_detail')->where($where)->select();
		if($list){
			foreach ($list as $k=>$v){
				$productid=$v['productid'];
				$where1=array();
				$where1['id']=$productid;
				$find= M('content')->where($where1)->find();
				if($find){
					$data=array();
					$data['stock']=$find['stock']+$v['num'];
					$data['status']=$status;
					M('content')->where($where1)->data($data)->save();
				}
			}
		}
	}else{
		$where=array();
		$where['orderno']=$orderno;
		$list=M('order_detail')->where($where)->select();
		if($list){
			foreach ($list as $k=>$v){
				$productid=$v['productid'];
				$where=array();
				$where['id']=$productid;
				$where['stock']=0;
				$stock=M('content')->where($where)->setField('status',$status);
			}
		}
	}
}

/**
 * 调试函数
 * @param unknown $str
 */
function we($str = null) {
	dump ( $str );
	exit ();
}
/**
 * 判断请求是否为空
 */
function isN($C_char = null) {
	if (isset ( $C_char )) {
		if (strlen ( $C_char ) > 0)
			return false; // 是否是字符串类型
	}
	if (empty ( $C_char ))
		return true; // 是否已设定
	if ($C_char == '')
		return true; // 是否为空
	return true;
}

/**
 * 转换为货币格式
 * 
 * @param unknown $num        	
 * @return string
 */
function to_price($num) {
	if (! is_numeric ( $num )) {
		$num = 0;
	}
	$num = sprintf ( "%01.2f", $num );
	return $num;
}


function to_percent($num) {
	if (! is_numeric ( $num )) {
		$num = 0;
	}
	$num = sprintf ( "%01.2f", $num*100 ).'%';
	return $num;
}
function cutstring($str = null, $start = 0, $len = 20, $ext = '...') {
	if (strlen ( $str ) > $len) {
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		preg_match_all ( $pa, $str, $t_string );
		if (count ( $t_string [0] ) - $start > $len) {
			$str = join ( '', array_slice ( $t_string [0], $start, $len ) );
			$str .= $ext;
		}
	}
	return $str;
}

// 数组保存到文件
function mkdirss($dirs, $mode = 0777) {
	if (! is_dir ( $dirs )) {
		mkdirss ( dirname ( $dirs ), $mode );
		return @mkdir ( $dirs, $mode );
	}
	return true;
}
// 不存在就新建文件
function create_file($l1, $l2 = '') {
	$l1 = str_replace ( '//', '/', $l1 );
	if (! file_exists ( $l1 )) {
		write_file ( $l1, $l2 );
		return true;
	} else {
		return true;
	}
}
// 写入文件
function write_file($l1, $l2 = '') {
	$dir = dirname ( $l1 );
	if (! is_dir ( $dir )) {
		mkdirss ( $dir );
	}
	return @file_put_contents ( $l1, $l2 );
}
// 写入数组到文件
function arr2file($filename, $arr = '') {
	if (is_array ( $arr )) {
		// 数组转字符串
		$con = var_export ( $arr, true );
	} else {
		$con = $arr;
	}
	$con = "<?php\nreturn $con;\n?>"; // \n!defined('IN_MP') && die();\nreturn
	                                  // $con;\n
	write_file ( $filename, $con );
}

/**
 * *
 * 重组节点
 */
function node_merge($node, $access = null, $pid = 0) {
	$arr = array ();
	foreach ( $node as $v ) {
		if (is_array ( $access )) {
			$v ["access"] = in_array ( $v ["id"], $access ) ? 1 : 0;
		}
		if ($v ['pid'] == $pid) {
			$v ['child'] = node_merge ( $node, $access, $v ['id'] );
			$arr [] = $v;
		}
	}
	
	return $arr;
}
function node_merge1($node, $access = null, $pid = 0) {
	$arr = array ();
	foreach ( $node as $v ) {
		if (is_array ( $access )) {
			$v ["access"] = in_array ( $v ["id"], $access ) ? 1 : 0;
		}
		$arr [] = $v;
	}
	
	return $arr;
}

function list_to_csv($list=null,$coding='gbk',$header='',$csvfile=''){
	if($csvfile==''){
		$csvfile=get_order_no();
	}
	
	if($header==''){
		if(count($list)>0){
			$header[]=array_keys($list[0]);
		}
	}
	$list=array_merge($header,$list);
	//echo(chr(0xEF).chr(0xBB).chr(0xBF));
	
	$content=generateCsv($list);
	if($coding=='utf-8'){
		header("Content-Type:APPLICATION/OCTET-STREAM");
	}else{
		$content=iconv("UTF-8","UTF-16LE",$content);
		$content = "\xFF\xFE".$content; //添加BOM
		header("Content-type: text/csv;charset=UTF-16LE") ; 
	}
	header("Content-Disposition: attachment; filename=$csvfile.csv"); 
	echo($content);
	exit();
}
function generateCsv($data, $delimiter = "\t", $enclosure = '"') {
	$handle = fopen('php://temp', 'r+');
	foreach ($data as $line) {
		fputcsv($handle, $line, $delimiter, $enclosure);
	}
	rewind($handle);
	while (!feof($handle)) {
		$contents .= fread($handle, 8192);
	}
	fclose($handle);
	return $contents;
}

/**
 * 把返回的数据集转换成Tree
 * access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array ();
	if (is_array ( $list )) {
		// 创建基于主键的数组引用
		$refer = array ();
		foreach ( $list as $key => $data ) {
			$refer [$data [$pk]] = & $list [$key];
		}
		foreach ( $list as $key => $data ) {
			// 判断是否存在parent
			$parentId = $data [$pid];
			if ($root == $parentId) {
				$tree [] = & $list [$key];
			} else {
				if (isset ( $refer [$parentId] )) {
					$parent = & $refer [$parentId];
					$parent [$child] [] = & $list [$key];
				}
			}
		}
	}
	return $tree;
}
/**
 * 数组清洁工作,注意会把0抹掉！
 * 
 * @param unknown $arr        	
 * @return multitype:
 */
function arr2clr($arr = null) {
	$arr = array_filter ( $arr );
	$arr = array_unique ( $arr );
	return $arr;
}
/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 *
 * @param string $str
 *        	要分割的字符串
 * @param string $glue
 *        	分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ',') {
	return explode ( $glue, $str );
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 *
 * @param array $arr
 *        	要连接的数组
 * @param string $glue
 *        	分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ',') {
	return implode ( $glue, $arr );
}
function md6($str, $decrypt = false) {
	if ($decrypt) {
		return think_decrypt ( $str );
	} else {
		return think_encrypt ( $str );
	}
}
/**
 * 系统加密方法
 *
 * @param string $data
 *        	要加密的字符串
 * @param string $key
 *        	加密密钥
 * @param int $expire
 *        	过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
	$key = md5 ( empty ( $key ) ? C ( 'DATA_AUTH_KEY' ) : $key );
	$data = base64_encode ( $data );
	$x = 0;
	$len = strlen ( $data );
	$l = strlen ( $key );
	$char = '';
	
	for($i = 0; $i < $len; $i ++) {
		if ($x == $l)
			$x = 0;
		$char .= substr ( $key, $x, 1 );
		$x ++;
	}
	
	$str = sprintf ( '%010d', $expire ? $expire + time () : 0 );
	
	for($i = 0; $i < $len; $i ++) {
		$str .= chr ( ord ( substr ( $data, $i, 1 ) ) + (ord ( substr ( $char, $i, 1 ) )) % 256 );
	}
	return str_replace ( array (
			'+',
			'/',
			'=' 
	), array (
			'-',
			'_',
			'' 
	), base64_encode ( $str ) );
}

/**
 * 系统解密方法
 *
 * @param string $data
 *        	要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key
 *        	加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '') {
	$key = md5 ( empty ( $key ) ? C ( 'DATA_AUTH_KEY' ) : $key );
	$data = str_replace ( array (
			'-',
			'_' 
	), array (
			'+',
			'/' 
	), $data );
	$mod4 = strlen ( $data ) % 4;
	if ($mod4) {
		$data .= substr ( '====', $mod4 );
	}
	$data = base64_decode ( $data );
	$expire = substr ( $data, 0, 10 );
	$data = substr ( $data, 10 );
	
	if ($expire > 0 && $expire < time ()) {
		return '';
	}
	$x = 0;
	$len = strlen ( $data );
	$l = strlen ( $key );
	$char = $str = '';
	
	for($i = 0; $i < $len; $i ++) {
		if ($x == $l)
			$x = 0;
		$char .= substr ( $key, $x, 1 );
		$x ++;
	}
	
	for($i = 0; $i < $len; $i ++) {
		if (ord ( substr ( $data, $i, 1 ) ) < ord ( substr ( $char, $i, 1 ) )) {
			$str .= chr ( (ord ( substr ( $data, $i, 1 ) ) + 256) - ord ( substr ( $char, $i, 1 ) ) );
		} else {
			$str .= chr ( ord ( substr ( $data, $i, 1 ) ) - ord ( substr ( $char, $i, 1 ) ) );
		}
	}
	return base64_decode ( $str );
}
/**
 * 格式化字节大小
 *
 * @param number $size
 *        	字节数
 * @param string $delimiter
 *        	数字和单位分隔符
 * @return string 格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
	$units = array (
			'B',
			'KB',
			'MB',
			'GB',
			'TB',
			'PB' 
	);
	for($i = 0; $size >= 1024 && $i < 5; $i ++)
		$size /= 1024;
	return round ( $size, 2 ) . $delimiter . $units [$i];
}

/**
 * 时间戳格式化
 *
 * @param int $time        	
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i:s') {
	$time = $time === NULL ? NOW_TIME : intval ( $time );
	return date ( $format, $time );
}

/**
 * 创建一个目录树
 *
 * @param [type] $dir
 *        	[description]
 * @param integer $mode
 *        	[description]
 * @return [type] [description]
 */
function mkdirs($dir, $mode = 0777) {
	if (! is_dir ( $dir )) {
		mkdirs ( dirname ( $dir ), $mode );
		return mkdir ( $dir, $mode );
	}
	return true;
}

// 获取模型名称
function get_model_by_id($id) {
	return $model = M ( 'Model' )->getFieldById ( $id, 'title' );
}

// 获取属性类型信息
function get_attribute_type($type = '') {
	// TODO 可以加入系统配置
	static $_type = array (
			'num' => array (
					'数字',
					'int(10) UNSIGNED NOT NULL' 
			),
			'string' => array (
					'字符串',
					'varchar(255) NOT NULL' 
			),
			'textarea' => array (
					'文本域',
					'text NOT NULL' 
			),
			'datetime' => array (
					'时间',
					'timestamp NOT NULL' 
			),
			// 'bool' => array('布尔','tinyint(2) NOT NULL'),
			'select' => array (
					'下拉框',
					'char(50) NOT NULL' 
			),
			'radio' => array (
					'单选',
					'char(10) NOT NULL' 
			),
			'checkbox' => array (
					'多选',
					'varchar(100) NOT NULL' 
			),
			'editor' => array (
					'编辑器',
					'text NOT NULL' 
			) 
	// 'picture' => array('上传图片','int(10) UNSIGNED NOT NULL'),
	// 'file' => array('上传附件','int(10) UNSIGNED NOT NULL'),
		);
	return $type ? $_type [$type] [0] : $_type;
}

/**
 * 获取当前文档的分类
 *
 * @param int $id        	
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_cate($cate_id = null,$tbl='Channel',$col='name') {
	if (empty ( $cate_id )) {
		return false;
	}
	$cate = M ( $tbl )->where ( 'id=' . $cate_id )->getField ( $col );
	return $cate;
}

/**
 * 获取会员等级
 *
 * @param int $id        	
 * @return array
 * @author huajie <banhuajie@163.com>
 */
function get_level($cate_id = null) {
	if (empty ( $cate_id )) {
		return false;
	}
	$cate = M ( 'level' )->where ( 'id=' . $cate_id )->getField ( 'name' );
	return $cate;
}
/**
 * 获取店铺名称
 *
 * @param int $id        	
 * @return array
 * @author huajie <banhuajie@163.com>
 */
function get_shopname($cate_id = null) {
	if (empty ( $cate_id )) {
		return false;
	}
	$cate = M ( 'shop' )->where ( 'id=' . $cate_id )->getField ( 'name' );
	return $cate;
}

/**
 * 获取套餐分类名称
 *
 * @param int $id        	
 * @return array
 * @author huajie <banhuajie@163.com>
 */
function get_magictypename($cate_id = null) {
	if (empty ( $cate_id )) {
		return false;
	}
	$cate = M ( 'magictype' )->where ( 'id=' . $cate_id )->getField ( 'name' );
	return $cate;
}

/**
 * 获取收支状态名称
 *
 * @param int $id        	
 * @return array
 * @author huajie <banhuajie@163.com>
 */
function get_iotypename($type_id = null, $type_name = 'balancetype') {
	if (empty ( $type_id )) {
		return false;
	}
	
	$cate = M ( $type_name )->where ( 'id=' . $type_id )->getField ( 'name' );
	return $cate;
}

/**
 * 获取折扣
 *
 * @param int $id        	
 */
function get_rate() {
	if (get_userid () == 0) {
		return to_price ( 1 );
	} else {
		return to_price ( session ( 'userrate' ) );
	}
}

/**
 * 根据索引数字获取状态
 *
 * @param unknown $status_id        	
 */
function get_status($status_id) {
	$str = C ( 'config.ORDER_STATUS_LIST' );
    return $str[$status_id];
    /*
    GLog('///////////////1',json_encode($str));
	$arr = str2arr ( $str, "\r\n" );GLog('////////////////2',json_encode($arr));
	foreach ( $arr as $key => $value ) {
		$arr2 = str2arr ( $value, ':' );
        GLog('///////////////3',json_encode($arr2));
        GLog('///////////////status_id',$status_id);
		if ($arr2 [0] == $status_id) {
            GLog('///////////////status_id',$arr2[1]);
			return $arr2[1];
		}
	}*/
}

/**
 * 获取表名（不含表前缀）
 *
 * @param string $model_id        	
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null) {
	if (empty ( $model_id )) {
		return false;
	}
	$Model = M ( 'Model' );
	$name = '';
	$info = $Model->getById ( $model_id );
	$name .= $info ['name'];
	return $name;
}

/**
 * 根据ID取模型字段
 *
 * @param number $model_id        	
 * @return unknown
 */
function get_model_fields($model_id = 0) {
	$where = array (
			'model_id' => $model_id,
			'status' => 1,
			'is_show' => 1 
	);
	$db = M ( 'field' )->where ( $where )->order ( 'sort asc' )->select ();
	return $db;
}

// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function parse_field_attr($string) {
	if (0 === strpos ( $string, ':' )) {
		// 采用函数定义
		return eval ( substr ( $string, 1 ) . ';' );
	}
	$array = preg_split ( '/[,;\r\n]+/', trim ( $string, ",;\r\n" ) );
	if (strpos ( $string, ':' )) {
		$value = array ();
		foreach ( $array as $val ) {
			list ( $k, $v ) = explode ( ':', $val );
			$value [$k] = $v;
		}
	} else {
		$value = $array;
	}
	return $value;
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 *
 * @param number $pos
 *        	推荐位的值
 * @param number $contain
 *        	指定推荐位
 * @return boolean true 包含 ， false 不包含
 */
function check_document_position($pos = 0, $contain = 0) {
	if (empty ( $pos ) || empty ( $contain )) {
		return false;
	}
	
	// 将两个参数进行按位与运算，不为0则表示$contain属于$pos
	$res = $pos & $contain;
	if ($res !== 0) {
		return true;
	} else {
		return false;
	}
}
function get_verify() {
    $config = array(
        'codeSet' => '2345678',
        'length' => 4
            );
    ob_clean();
    $verify = new \Think\Verify($config);
    $verify->entry(1);
}

/**
 * 检测验证码
 *
 * @param integer $id
 *        	验证码ID
 * @return boolean 检测结果
 */
function check_verify($code, $id = 1) {
	$verify = new \Think\Verify ();
	return $verify->check ( $code, $id );
}
/**
 * 根据订单号取订单产品列表
 *
 * @param string $orderno        	
 */
function get_order_products($orderno = '', $format = 0) {
	$where = array ();
	$where ['orderno'] = $orderno;

	if($format!=0){
		$db = M ( 'order_detail' )->field ( 'productid,productname,indexpic,price,num,ext' )->limit($format)->where ( $where )->select ();
	}else{
		$db = M ( 'order_detail' )->field ( 'productid,productname,indexpic,ext' )->where ( $where )->select ();
	}
	$html = '';
	if ($db != null) {
		if($format!=0){
			return $db;
		}else{
			foreach ( $db as $pro ) {
				$html .= "<img class='thumbpic' src='{$pro['indexpic']}' alt='{$pro['productname']}' title='{$pro['productname']}' />";
			}
		}
	}
	echo ($html);
}
function get_order_details($orderno = '') {
	$where = array ();
	$where ['orderno'] = $orderno;
	$db = M ( 'order_detail' )->field ( 'productid,productname,indexpic' )->where ( $where )->select ();
	
	$html = '';
	if ($db != null) {
		foreach ( $db as $pro ) {
			$html .= $pro ['productname'] . ', ';
		}
		$html = substr ( $html, 0, strlen ( $html ) - 2 );
	}
	echo ($html);
}

/**
 * 生成随机订单号
 */
function get_order_no() {
	return date ( 'YmdHis' ) .  rand ( 1000, 9999 );
}

/**
 * 取会员ID
 */
function get_userid() {
	$uid = session ( 'userid' );
	if (isN ( $uid )) {
		return 0;
	} else {
		if (! is_numeric ( $uid )) {
			return 0;
		} else {
			return $uid;
		}
	}
}

/**
 * 根据会员ID取用户名
 */
function get_username($uid = 0) {
	$db = M ( 'member' )->getFieldById ( $uid, 'username' );
	if ($db !== false) {
		return $db;
	} else {
		return '';
	}
}
/**
 * 根据会员ID取用户名
 */
function get_displayname() {
    if(session ( 'usertype')==2){ 
        return "";
    }else{
	   return get_username ( get_userid () );
    }
}
/**
 * 根据用户名取会员ID
 */
function get_useridbyname($username = '') {
	$db = M ( 'member' )->getFieldByusername ( $username, 'id' );
	if ($db !== false) {
		return $db;
	} else {
		return '';
	}
}
/**
 * 取余额
 * 
 * @param number $uid        	
 * @return number unknown
 */
function get_useramount($uid = null) {
	if (isN ( $uid ) || $uid == 0) {
		$uid = get_userid ();
	}
	$db = M ( 'member' )->getFieldByid ( $uid, 'balance' );
	if ($db == false) {
		return to_price ( 0 );
	} else {
		return to_price ( $db );
	}
}
/**
 * 取积分
 * 
 * @param number $uid        	
 * @return number unknown
 */
function get_usercredit($uid = null) {
	if (isN ( $uid ) || $uid == 0) {
		$uid = get_userid ();
	}
	$db = M ( 'member' )->getFieldByid ( $uid, 'credit' );
	if ($db == false) {
		return to_price ( 0 );
	} else {
		return to_price ( $db );
	}
}

/**
 * 获取产品价格（多型号）
 *
 * @param number $pid        	
 * @param number $price        	
 * @param string $name        	
 */
function get_price($pid = 0, $price = 0, $name = '') {
	$ctrl = A ( 'Home/Price' );
	return $ctrl->getPrice ( $pid, $price, $name );
}

/**
 * 根据取商品评论，根据订单详细ID
 *
 * @param unknown $orderdetailid
 */
function get_comment($orderdetailid=0) {
	$comment=M('comment')->where('orderdetailid='.$orderdetailid)->getField('info');
	return $comment;
}


/**
 * 生成缩略图
 * 
 * @param string $pic        	
 * @param string $w        	
 * @param string $h        	
 */
// get_thumb('/Public/uploadfile/remote/2014-03-01/201403011449371576.jpg');
function get_thumb($pic = null, $w = 80, $h = 60) {
	// $pic=$_SERVER["DOCUMENT_ROOT"].$pic;
	$pic = str_replace ( '/Public/', 'Public/', $pic );
	$returl = $pic;
	$thumburl = str_replace ( 'uploadfile', 'uploadfile/thumbs', $pic );
	$thumburl = str_replace ( '.', '_' . $w . 'x' . $h . '.', $thumburl );
	$furl = file_exists ( $thumburl );
	if ($furl) {
		$returl = $thumburl;
	} else {  
		if (file_exists ( $pic )) {
			mkdirs ( dirname ( $thumburl ) );
			$ctrl = new \Think\Image ( 1, $pic );
			$img = $ctrl->thumb ( $w, $h )->save ( $thumburl );
			$returl = $thumburl;
		} else{
			$returl=$pic;
		}
	}
	$returl = str_replace ( 'Public/', '/Public/', $returl );
	return $returl;
}

function imagecreatefrombmp($p_sFile)
{
	$file    =    fopen($p_sFile,"rb");
	$read    =    fread($file,10);
	while(!feof($file)&&($read<>""))
		$read    .=    fread($file,1024);
	$temp    =    unpack("H*",$read);
	$hex    =    $temp[1];
	$header    =    substr($hex,0,108);
	if (substr($header,0,4)=="424d")
	{
		$header_parts    =    str_split($header,2);
		$width            =    hexdec($header_parts[19].$header_parts[18]);
		$height            =    hexdec($header_parts[23].$header_parts[22]);
		unset($header_parts);
	}
	$x                =    0;
	$y                =    1;
	$image            =    imagecreatetruecolor($width,$height);
	$body            =    substr($hex,108);
	$body_size        =    (strlen($body)/2);
	$header_size    =    ($width*$height);
	$usePadding        =    ($body_size>($header_size*3)+4);
	for ($i=0;$i<$body_size;$i+=3)
	{
		if ($x>=$width)
		{
			if ($usePadding)
				$i    +=    $width%4;
			$x    =    0;
			$y++;
			if ($y>$height)
				break;
		}
		$i_pos    =    $i*2;
		$r        =    hexdec($body[$i_pos+4].$body[$i_pos+5]);
		$g        =    hexdec($body[$i_pos+2].$body[$i_pos+3]);
		$b        =    hexdec($body[$i_pos].$body[$i_pos+1]);
		$color    =    imagecolorallocate($image,$r,$g,$b);
		imagesetpixel($image,$x,$height-$y,$color);
		$x++;
	}
	unset($body);
	return $image;
}

function imagebmp(&$im, $filename = '', $bit = 8, $compression = 0)
{
	if (!in_array($bit, array(1, 4, 8, 16, 24, 32)))
	{
		$bit = 8;
	}
	else if ($bit == 32) // todo:32 bit
	{
		$bit = 24;
	}
	$bits = pow(2, $bit);

	// 调整调色板
	imagetruecolortopalette($im, true, $bits);
	$width  = imagesx($im);
	$height = imagesy($im);
	$colors_num = imagecolorstotal($im);

	if ($bit <= 8)
	{
		// 颜色索引
		$rgb_quad = '';
		for ($i = 0; $i < $colors_num; $i ++)
		{
			$colors = imagecolorsforindex($im, $i);
			$rgb_quad .= chr($colors['blue']) . chr($colors['green']) . chr($colors['red']) . "\0";
		}

		// 位图数据
		$bmp_data = '';

		// 非压缩
		if ($compression == 0 || $bit < 8)
		{
			if (!in_array($bit, array(1, 4, 8)))
			{
				$bit = 8;
			}
			$compression = 0;
				
			// 每行字节数必须为4的倍数，补齐。
			$extra = '';
			$padding = 4 - ceil($width / (8 / $bit)) % 4;
			if ($padding % 4 != 0)
			{
				$extra = str_repeat("\0", $padding);
			}

			for ($j = $height - 1; $j >= 0; $j --)
			{
				$i = 0;
				while ($i < $width)
				{
					$bin = 0;
					$limit = $width - $i < 8 / $bit ? (8 / $bit - $width + $i) * $bit : 0;

					for ($k = 8 - $bit; $k >= $limit; $k -= $bit)
					{
						$index = imagecolorat($im, $i, $j);
						$bin |= $index << $k;
						$i ++;
					}

					$bmp_data .= chr($bin);
				}

				$bmp_data .= $extra;
			}
		}
		// RLE8 压缩
		else if ($compression == 1 && $bit == 8)
		{
			for ($j = $height - 1; $j >= 0; $j --)
			{
				$last_index = "\0";
				$same_num   = 0;
				for ($i = 0; $i <= $width; $i ++)
				{
					$index = imagecolorat($im, $i, $j);
					if ($index !== $last_index || $same_num > 255)
					{
						if ($same_num != 0)
						{
							$bmp_data .= chr($same_num) . chr($last_index);
						}

						$last_index = $index;
						$same_num = 1;
					}
					else
					{
						$same_num ++;
					}
				}

				$bmp_data .= "\0\0";
			}

			$bmp_data .= "\0\1";
		}
		$size_quad = strlen($rgb_quad);
		$size_data = strlen($bmp_data);
	}
	else
	{
		// 每行字节数必须为4的倍数，补齐。
		$extra = '';
		$padding = 4 - ($width * ($bit / 8)) % 4;
		if ($padding % 4 != 0)
		{
			$extra = str_repeat("\0", $padding);
		}
		// 位图数据
		$bmp_data = '';
		for ($j = $height - 1; $j >= 0; $j --)
		{
			for ($i = 0; $i < $width; $i ++)
			{
				$index  = imagecolorat($im, $i, $j);
				$colors = imagecolorsforindex($im, $index);
				if ($bit == 16)
				{
					$bin = 0 << $bit;

					$bin |= ($colors['red'] >> 3) << 10;
					$bin |= ($colors['green'] >> 3) << 5;
					$bin |= $colors['blue'] >> 3;

					$bmp_data .= pack("v", $bin);
				}
				else
				{
					$bmp_data .= pack("c*", $colors['blue'], $colors['green'], $colors['red']);
				}

				// todo: 32bit;
			}
			$bmp_data .= $extra;
		}
		$size_quad = 0;
		$size_data = strlen($bmp_data);
		$colors_num = 0;
	}

	// 位图文件头
	$file_header = "BM" . pack("V3", 54 + $size_quad + $size_data, 0, 54 + $size_quad);

	// 位图信息头
	$info_header = pack("V3v2V*", 0x28, $width, $height, 1, $bit, $compression, $size_data, 0, 0, $colors_num, 0);

	// 写入文件
	if ($filename != '')
	{
		$fp = fopen($filename, "wb");
		fwrite($fp, $file_header);
		fwrite($fp, $info_header);
		fwrite($fp, $rgb_quad);
		fwrite($fp, $bmp_data);
		fclose($fp);
		return true;
	}

	// 浏览器输出
	header("Content-Type: image/bmp");
	echo $file_header . $info_header;
	echo $rgb_quad;
	echo $bmp_data;
	return true;
}


/**
 * 发送HTTP请求方法，目前只支持CURL发送请求，供微信接口使用
 *
 * @param string $url
 *        	请求URL
 * @param array $params
 *        	请求参数
 * @param string $method
 *        	请求方法GET/POST
 * @return array $data 响应数据
 */
function http($url, $params, $method = 'GET', $header = array(), $multi = false) {
	$opts = array (
			CURLOPT_TIMEOUT => 30,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTPHEADER => $header 
	);
	
	/* 根据请求类型设置特定参数 */
	switch (strtoupper ( $method )) {
		case 'GET' :
			$opts [CURLOPT_URL] = $url . '?' . http_build_query ( $params );
			break;
		case 'POST' :
			// 判断是否传输文件
			// $params = $multi ? $params : http_build_query($params);
			$opts [CURLOPT_URL] = $url;
			$opts [CURLOPT_POST] = 1;
			$opts [CURLOPT_POSTFIELDS] = $params;
			break;
		default :
			throw new Exception ( '不支持的请求方式！' );
	}
	
	/* 初始化并执行curl请求 */
	$ch = curl_init ();
	curl_setopt_array ( $ch, $opts );
	$data = curl_exec ( $ch );
	$error = curl_error ( $ch );
	curl_close ( $ch );
	if ($error)
		throw new Exception ( '请求发生错误：' . $error );
	return $data;
}

/**
 * 不转义中文字符和\/的 json 编码方法，供微信接口使用
 *
 * @param array $arr
 *        	待编码数组
 * @return string
 */
function jsencode($arr) {
	$str = str_replace ( "\\/", "/", json_encode ( $arr ) );
	$search = "#\\\u([0-9a-f]+)#ie";
	
	if (strpos ( strtoupper ( PHP_OS ), 'WIN' ) === false) {
		$replace = "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))"; // LINUX
	} else {
		$replace = "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))"; // WINDOWS
	}
	
	return preg_replace ( $search, $replace, $str );
}
/**
 * 获取角色权限
 * 
 * @param string $node        	
 * @return multitype:string number |multitype:string multitype:
 */
function get_role($node = 'channel') {
	if (isN ( session ( $node . '_role' ) )) {
		return array (
				'gt',
				0 
		);
	} else {
		return array (
				'in',
				str2arr ( session ( $node . '_role' ) ) 
		);
	}
}

/**
 * 提取编辑器里的图片，返回数组
 * 
 * @param string $content        	
 */
function get_imgs($content = '') {
	$pattern = '/<img.*?src=\s*?"?([^"\s]+)(?!\/>)"?\s*?/is';
	preg_match_all ( $pattern, $content, $matches );
	return ($matches [1]);
}
 
/**
 * 发送邮件
 * 
 * @param string $to        	
 * @param string $subject        	
 * @param string $body        	
 */
function send_mail($to = null, $subject = null, $body = null,$async=true) {
	if (C ( 'config.WEB_SITE_EMAIL' )) {
		if(is_email($to)){
			$mail = new \Org\Util\Email ();
			$data ['mailto'] = $to; // 收件人
			$data ['subject'] = $subject; // 邮件标题
			$data ['body'] = $body; // 邮件正文内容
				
			return $mail->send ( $data );
		}else{
			return false;
		}
	} else {
		return false;
	}
}
function send_mail1($to = null, $subject = null, $body = null,$async=true) {
	if($async){
		$errno='';
		$errstr=''; 
		
		$body=str_replace("\n","",$body);
		$body=str_replace("\r","",$body);
		$body=str_replace("\r\n","",$body);
		$body=str_replace("\"","",$body);
		$body=urlencode($body); 
		$subject=urlencode($subject);
		
		
		$domain = $_SERVER['SERVER_NAME']; 
		$port=$_SERVER['SERVER_PORT'];
		$param = "/Admin/Login/mail?to=$to&subject=$subject&body=$body";
		
		$header = "GET $param HTTP/1.1\r\n";
		$header .= "Host: $domain\r\n";
		$header .= "Content-Type:application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length:" . strlen($param) . "\r\n\r\n";
		$header .= "Connection: close\r\n";
		$fp = @fsockopen($domain, $port, $errno, $errstr, 30);
		fwrite($fp, $header);
		
// 		$html = '';
		
// 		while (!feof($fp)) {
// 			$html.=fgets($fp);
// 		} 
// 		we($html);

		fclose($fp);
		
	  	return true; 
    		
	}else{
		// 需要先开启邮件接口
		if (C ( 'config.WEB_SITE_EMAIL' )) {
			if(is_email($to)){
			$mail = new \Org\Util\Email ();
			$data ['mailto'] = $to; // 收件人
			$data ['subject'] = $subject; // 邮件标题
			$data ['body'] = $body; // 邮件正文内容 
		 
			return $mail->send ( $data );
			}else{
				return false;
			}
		} else {
			return false;
		}
	}
}

/**
 * 发送手机短信
 * 
 * @param string $to        	
 * @param string $content        	
 */
function send_sms($to = null, $content = null) {
	// 需要先开启邮件接口
	if (C ( 'config.WEB_SITE_SMS' )) {
		return true;
	} else {
		return false;
	}
}
/**
 * 自定义标签调用
 * @param unknown $name
 * @param string $value
 * @return Ambigous <mixed, void, boolean>|string
 */

/**
 * 自定义标签调用|TP专用
 *
 * @param unknown $name        	
 * @param string $value        	
 * @return Ambigous <mixed, void, boolean>|string
 */
function lbl($name, $value = null) {
    $cachename = 'label_' . $name;
    $cache = S ( $name );
    if ($value == null) {
		if (! $cache) {
			$where = array ();
			$where ['status'] = 1;
			$where ['name'] = $name;
			$db = M ( 'label' )->where ( $where )->find ();
            if ($db) {
				$cache = $db ['info'];
				S ( $cachename, $cache );
			}
		}
	} else {
		S ( $cachename, $value );
		$cache = $value;
	}
    return $cache;
}

/**
 * 省市区联动
 * @param string $tbl
 * @param string $id
 * @param number $cid
 * @return string
 */
function get_area($tbl = 'china_province', $id = null, $cid = 0) {
	$html = '';
	$html .= '<option value="0">--请选择--</option>';
	switch ($tbl) {
		case 'china_province' :
			$db = M ( $tbl )->select ();
			if ($db) {
				foreach ( $db as $key => $value ) {
					if ($cid == $value ['ProID']) {
						$html .= '<option value="' . $value ['ProID'] . '" selected="selected">' . $value ['ProName'] . '</option>';
					} else {
						$html .= '<option value="' . $value ['ProID'] . '">' . $value ['ProName'] . '</option>';
					}
				}
			}
			break;
		case 'china_city' :
			$db = M ( $tbl )->where ( 'ProID=' . $id )->select ();
			if ($db) {
				foreach ( $db as $key => $value ) {
					if ($cid == $value ['CityID']) {
						$html .= '<option value="' . $value ['CityID'] . '" selected="selected">' . $value ['CityName'] . '</option>';
					} else {
						$html .= '<option value="' . $value ['CityID'] . '">' . $value ['CityName'] . '</option>';
					}
				}
			}
			break;
		case 'china_district' :
			$db = M ( $tbl )->where ( 'CityID=' . $id )->select ();
			if ($db) {
				foreach ( $db as $key => $value ) { 
					if ($cid == $value ['Id']) {
						$html .= '<option value="' . $value ['Id'] . '" selected="selected">' . $value ['DisName'] . '</option>';
					} else {
						$html .= '<option value="' . $value ['Id'] . '">' . $value ['DisName'] . '</option>';
					}
				}
			}
			break;
	}
	return $html;
}

function get_form_fields(){
	return array('姓名','电话','Email','QQ','地址','备注','其它一','其它二');
}


/**
 * zouhao619@gmail.com 	zouhao
 * 一些验证方法
 */
/**
 * 是否是手机号码
 *
 * @param string $phone	手机号码
 * @return boolean
 */
function is_phone($phone) {
	if (strlen ( $phone ) != 11 || ! preg_match ( '/^1[3|4|5|8][0-9]\d{4,8}$/', $phone )) {
		return false;
	} else {
		return true;
	}
}
/**
 * 验证字符串是否为数字,字母,中文和下划线构成
 * @param string $username
 * @return bool
 */
function is_check_string($str){
	if(preg_match('/^[\x{4e00}-\x{9fa5}\w_]+$/u',$str)){
		return true;
	}else{
		return false;
	}
}
/**
 * 是否为一个合法的email
 * @param sting $email
 * @return boolean
 */
function is_email($email){
	if (filter_var ($email, FILTER_VALIDATE_EMAIL )) {
		return true;
	} else {
		return false;
	}
}
/**
 * 是否为一个合法的url
 * @param string $url
 * @return boolean
 */
function is_url($url){
	if (filter_var ($url, FILTER_VALIDATE_URL )) {
		return true;
	} else {
		return false;
	}
}
/**
 * 是否为一个合法的ip地址
 * @param string $ip
 * @return boolean
 */
function is_ip($ip){
	if (ip2long($ip)) {
		return true;
	} else {
		return false;
	}
}
/**
 * 是否为整数
 * @param int $number
 * @return boolean
 */
function is_number($number){
	if(preg_match('/^[-\+]?\d+$/',$number)){
		return true;
	}else{
		return false;
	}
}
/**
 * 是否为正整数
 * @param int $number
 * @return boolean
 */
function is_positive_number($number){
	if(ctype_digit ($number)){
		return true;
	}else{
		return false;
	}
}
/**
 * 是否为小数
 * @param float $number
 * @return boolean
 */
function is_decimal($number){
	if(preg_match('/^[-\+]?\d+(\.\d+)?$/',$number)){
		return true;
	}else{
		return false;
	}
}
/**
 * 是否为正小数
 * @param float $number
 * @return boolean
 */
function is_positive_decimal($number){
	if(preg_match('/^\d+(\.\d+)?$/',$number)){
		return true;
	}else{
		return false;
	}
}
/**
 * 是否为英文
 * @param string $str
 * @return boolean
 */
function is_english($str){
	if(ctype_alpha($str))
		return true;
	else
		return false;
}
/**
 * 是否为中文
 * @param string $str
 * @return boolean
 */
function is_chinese($str){
	if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$str))
		return true;
	else
		return false;
}
/**
 * 判断是否为图片
 * @param string $file	图片文件路径
 * @return boolean
 */
function is_image($file){
	if(file_exists($file)&&getimagesize($file===false)){
		return false;
	}else{
		return true;
	}
}
/**
 * 是否为合法的身份证(支持15位和18位)
 * @param string $card
 * @return boolean
 */
function is_card($card){
	if(preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/',$card)||preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/',$card))
		return true;
	else
		return false;
}
/**
 * 验证日期格式是否正确
 * @param string $date
 * @param string $format
 * @return boolean
 */
function is_date($date,$format='Y-m-d'){
	$t=date_parse_from_format($format,$date);
	if(empty($t['errors'])){
		return true;
	}else{
		return false;
	}
}

/**
 * 带缓存的数据读取函数|TP专用
 *
 * @param string $id        	
 * @param string $tbl        	
 * @param string $col        	
 * @param string $ch        	
 * @return boolean unknown
 */
function get_data($id = null, $tbl = 'channel', $col = 'name', $ch = true) {
	if (empty ( $id )) {
		return false;
	}
	$where = is_numeric ( $id ) ? array (
			'id' => $id 
	) : $id;
	if (! $ch) {
		$cache = (M ( $tbl )->where ( $where )->getField ( $col ));
	} else {
		$key = md5 ( serialize ( $where ) . '_' . $tbl . '_' . $col );
		$cache = F ( 'data_' . $key );
		if (! $cache) {
			$cache = (M ( $tbl )->where ( $where )->getField ( $col ));
			F ( 'data_' . $key, $cache );
		}
	}
	return $cache;
}

/**
 *
 * @param type $value
 *          验证的值
 * @param string $rule
 *           用于验证的表达式
 * @return boolean
 * true 值匹配表达式，反之不匹配
 *
 */
function regex($value, $rule){
    $validate = array(
        'require'=>'/.+/',
        'email'=>'/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
        'url'=>'/^http:\/\/[A-Za-z0-9|-]+\.[A-Za-z0-9|-]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
        'currency'=>'/^\d+(\.\d+)?$/',
        'number'=>'/^\d+$/',
        'zip'=>'/^[1-9]\d{5}$/',
        'integer'=>'/^[-\+]?\d+$/',
        'double'=>'/^[-\+]?\d+(\.\d+)?$/',
        'english'=>'/^[A-Za-z]+$/',
        'time'=>'/^[0-9]{4}-[0-9]{2}-[0-9]{1,2}\s+[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', // 2012-03-13 11:09:11
        'date'=>'/^[0-9]{4}-[0-9]{2}-[0-9]{1,2}$/',
        'tel'=>'/^(\d{11})|((\d{3}-\d{8})|(8\d{10})|(9\d{9})|(\d{7})|(\d{4}-\d{7}))$/',
        'qq'=>'/^\d{6,11}$/',
        'short_url'=>'/^[0-9A-Za-z]+$/', // 短域名
        'short_tel'=>'/^(\d){3,6}$/',
        'china'=>'/^[\x{4e00}-\x{9fa5}]+$/u',
        'mob_tel'=>'/^(\d{11})|((\d{3}-\d{8})|(8\d{10})|(9\d{9})|(\d{7})|(\d{4}-\d{7}))$/', //移动电话
        'mob'=>'/\S{7,20}/', //7-20位
        'username'=>'/\w{3,50}/', //3-50字符串
    );
    // 检查是否有内置的正则表达式
    if(isset($validate[strtolower($rule)])) $rule = $validate[strtolower($rule)];
    return preg_match($rule, $value)===1;
}

function apiReturn($code = CodeModel::CORRECT,$msg="", $data = array()){
    $result['code'] = $code;
    if(empty($msg)){
        $msg = \Common\Model\CodeModel::getMessage($code);
    }
    $result['message'] = $msg;
    $result['data'] = $data;
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($result));
}

function replaceTel($tel){
    if(strpos($tel,'-')>0){
        return str_replace("-", "",$tel);
    }
    return $tel;
}

/**配送费
 * @param int $n
 * @return int
 */
function getShipfee($n = 0) {
    $freight = lbl ( 'freight' );
    if (isN ( $freight )) {
        return 0;
    } else {
        $arr = parse_field_attr ( $freight );

        $arr = arr2clr ( $arr );
        ksort ( $arr );
        $i = 0;
        foreach ( $arr as $k => $v ) {
            if ($n >= $i && $n < $k) {
                return $v;
                break;
            }
            $i = $k;
        }
        return 0;
    }
}

?>