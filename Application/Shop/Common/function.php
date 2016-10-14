<?php

/**
 * 信息列表
 * @param number $pid
 * @param number $num
 * @return unknown
 */
function get_lists($pid = 0, $num = 10, $type = 1)
{
    $where = array();
    if ($type == 2) {
        $where['isresume'] = 1;
    }
    $where['sortpath'] = array(
        'like',
        '%,' . $pid . ',%'
    );
    $where['status'] = 1;
    $db = M('content')->where($where)
        ->order('sort desc')
        ->limit($num)
        ->select();
    return $db;
}

/**
 * 信息内容
 *
 * @param number $id            
 * @return unknown
 */
function get_content($id = 0)
{
    $where = array();
    $where['id'] = $id;
    $where['status'] = 1;
    $db = M('content')->where($where)->select();
    return $db;
}

/**
 * 栏目递规
 */
function get_location($id = 0, $rid = 1)
{
    $sortpath = M('channel')->where('id=' . $id)->getField('sortpath');
    $arr = str2arr($sortpath);
    $arr = arr2clr($arr);
    sort($arr);
    unset($arr[0]);
    
    $html = '<a href="/">首页</a>';
    if (count($arr) > 0) {
        foreach ($arr as $key => $value) {
            $html .= ' &gt; <a href="' . U('Content/lists', 'id=' . $value) . '">' . get_cate($value) . '</a>';
        }
    }
    return ($html);
}

/**
 * 产品分类列表
 *
 * @param number $pid            
 * @param number $num            
 * @param number $type            
 */
function get_product_channel($pid = 0, $num = 10, $type = 1)
{
    $where = array();
    if ($type == 2) {
        $where['isresume'] = 1;
    }
    // $where['sortpath']=array('like','%,'.$pid.',%');
    $where['pid'] = $pid;
    $where['status'] = 1;
    $key = md5($pid);
    $db = F($key);
    if (! $db) {
        $db = M('channel')->where($where)
            ->order('sort asc')
            ->limit($num)
            ->select();
        F($key, $db);
    }
    
    if ($db) {
        return $db;
    } else {
        $pid1 = M('channel')->where('id=' . $pid)->getField('pid');
        $db1 = M('channel')->where($where)
            ->order('sort asc')
            ->limit($num)
            ->select();
        return $db1;
    }
}

/**
 * 产品列表
 *
 * @param number $pid            
 * @param number $num            
 * @return unknown
 */
function get_product_lists($pid = 2, $num = 10, $type = 1)
{
    $where = array();
    if ($type == 2) {
        $where['isresume'] = 1;
    }
    if (isN($pid)) {
        $pid = 2;
    }
    $where['sortpath'][] = array(
        'like',
        array(
            '%,' . $pid . ',%'
        )
    );
    if ($pid != C('DEFAULT_CREDIT_CHANNEL')) {
        $where['sortpath'][] = array(
            'notlike',
            array(
                '%,' . C('DEFAULT_CREDIT_CHANNEL') . ',%'
            )
        );
    }
    $where['status'] = 1;
    
    $db = M('content')->where($where)
        ->order('sort desc')
        ->limit($num)
        ->select();
    return $db;
}

/**
 * 产品内容
 *
 * @param number $id            
 * @return unknown
 */
function get_product_content($id = 0)
{
    $where = array();
    $where['id'] = $id;
    $where['status'] = 1;
    $db = M('content')->where($where)->select();
    return $db;
}

/**
 * 产品栏目递规
 */
function get_product_location($id = 0, $rid = 1)
{
    $sortpath = M('channel')->where('id=' . $id)->getField('sortpath');
    $arr = str2arr($sortpath);
    $arr = arr2clr($arr);
    sort($arr);
    unset($arr[0]);
    
    $html = '<a href="/">Home</a>';
    if (count($arr) > 0) {
        foreach ($arr as $key => $value) {
            $html .= ' &gt; <a href="' . U('Product/lists', 'id=' . $value) . '">' . get_cate($value) . '</a>';
        }
    }
    return ($html);
}

function get_product_magic($pid = 1, $num = 2)
{
    $where = array();
    $where['pid'] = $pid;
    $where['status'] = 1;
    $db = M('magic')->where($where)
        ->order('sort desc')
        ->find();
    if ($db) {
        $mtid = $db['contentids'];
        $where = array();
        $where['id'] = array(
            'in',
            str2arr($mtid)
        );
        $where['status'] = 1;
        $where['stock'] = array(
            'gt',
            0
        );
        $db = M('content')->where($where)
            ->order('sort1 desc')
            ->limit($num)
            ->select();
        return $db;
    } else {
        return false;
    }
}

function set_url1($url = '', $decode = false)
{
    if ($decode) {
        $url = str_replace('_', ' ', $url);
        $url = str_replace(',', '\'', $url);
    } else {
        $url = str_replace(' ', '_', $url);
        $url = str_replace('\'', ',', $url);
    }
    return $url;
}

function set_url($p = '', $v = '')
{
    $p = strtolower($p);
    $param = $_GET;
    $param = array_change_key_case($param, CASE_LOWER);
    if ($v != '') {
        $v = strtolower($v);
        $v = parse_param($v);
    }
    if (isset($param['keyword'])) {
        $param['keyword'] = strtolower($param['keyword']);
        $param['keyword'] = parse_param($param['keyword']);
    }
    $param[$p] = $v;
    return U(CONTROLLER_NAME . '/' . ACTION_NAME, $param);
}

function parse_param($str = '', $de = false)
{
    if ($de) {
        $str = str_replace('_', ' ', $str);
        $str = str_replace('@', "'", $str);
    } else {
        $str = str_replace(' ', '_', $str);
        $str = str_replace("'", '@', $str);
    }
    return $str;
}

?>