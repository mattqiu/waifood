<?php

/**
 * 调试日志
 *
 * @param unknown $text
 */
function logdebug($text)
{
    file_put_contents('log.txt', $text . "\n", FILE_APPEND);
}

/**
 * 获取公众号对象
 *
 * @param number $mpid            
 */
function get_wechat_obj()
{
    $options = array(
        'token' => C('config.WECHAT_TOKEN'), // 填写你设定的key
        'encodingaeskey' => '',
        'appid' => C('config.WECHAT_APPID'), // 填写高级调用功能的app id
        'appsecret' => C('config.WECHAT_APPSECRET'), // 填写高级调用功能的密钥
        'partnerid' => '', // 财付通商户身份标识
        'partnerkey' => '', // 财付通商户权限密钥Key
        'paysignkey' => '', // 商户签名密钥Key
        'debug' => true,
        'logcallback' => 'logdebug'
    );
    $wechat = new \Org\Util\Wechat($options);
    return $wechat;
}

/**
 * 获取当前网址全路径
 */
function get_current_url()
{
    $url = array();
    $url[0] = $_SERVER['REQUEST_SCHEME'];
    $url[1] = $_SERVER['SERVER_NAME'];
    $url[2] = $_SERVER['SERVER_PORT'];
    $url[3] = $_SERVER['REQUEST_URI'];
    if ($url[0] == '') {
        $url[0] = 'http';
    }
    if ($url[2] == '80') {
        $u = $url[0] . '://' . $url[1] . $url[3];
    } else {
        $u = $url[0] . '://' . $url[1] . ':' . $url[2] . $url[3];
    }
    return $u;
}

/**
 * 检测是否填写了收货地址
 */
function is_address()
{
    $where = array();
    $where['id'] = get_userid();
    $db = M('member')->where($where)->find();
    if ($db) {
        if (  isN($db['telephone']) || isN($db['email']) || isN($db['address']) ) {
            return false;
        } else {
            return $db;
        }
    } else {
        return false;
    }
}

/**
 * 推广者ID
 * 
 * @return mixed|number
 */
function get_fid()
{
    $prefix = C('COUPON.PREFIX');
    $cookiefid = cookie('fid');
    $fid = str_replace($prefix, '', $cookiefid);
    if ($fid) {
        if (is_number($fid)) {
            if (get_userid() == $fid && get_userid() != 0) {
                return 0;
            } else {
                return $fid;
            }
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

function set_fid()
{
    $url = '';
    $uid = get_userid();
    $prefix = C('COUPON.PREFIX');
    $fid = C('COUPON.FID');
    if ($uid != 0) {
        $url .= "&" . $fid . "=" . $prefix . $uid;
    }
    return $url;
}

/**
 * 推广网址
 */
function get_furl($uid)
{
    $prefix = C('COUPON.PREFIX');
    $fid = C('COUPON.FID');
    $url = "http://www.waifood.com/"; 
    if ($uid != 0) {
        $url .= "?" . $fid . "=" . $prefix . $uid;
    }
    return $url;
}

/**
 * 时间差|TP专用
 *
 * @param string $date1            
 * @param string $date2            
 * @param string $elaps            
 */
function get_date_diff($date1 = '', $date2 = '', $elaps = "d")
{
    $ctrl = new \Org\Util\Date($date1);
    $df = $ctrl->dateDiff($date2, $elaps);
    return $df;
}

/**
 * 时间差：友好显示|TP专用
 *
 * @param string $date1            
 * @param string $date2            
 * @return string
 */
function get_time_diff($date1 = '', $date2 = '')
{
    $ctrl = new \Org\Util\Date($date1);
    $df = $ctrl->timeDiff($date2);
    return $df;
}

/**
 * 时间增加|TP专用
 *
 * @param string $date1            
 * @param number $num            
 * @param string $elaps            
 * @return string
 */
function get_date_add($date1 = '', $num = 0, $elaps = 'd')
{
    $ctrl = new \Org\Util\Date($date1);
    $da = $ctrl->dateAdd($num, $elaps)->format();
    return $da;
}

/**
 * 优惠券
 */

/**
 * 获取当前时间段
 */
function get_current_range()
{
    // $expire:1-月，2-季，3-半年，4-年
    $expire = C("COUPON.EXPIRE");
    $from = '';
    $to = '';
    switch ($expire) {
        case 1:
            $from = date('Y-m-01 00:00:00');
            $to = date('Y-m-t 23:59:59');
            break;
        case 2:
            $month = date('m');
            switch ($month) {
                case 1:
                case 2:
                case 3:
                    $from = date('Y-01-01 00:00:00');
                    $to = date('Y-03-31 23:59:59');
                    break;
                case 4:
                case 5:
                case 6:
                    $from = date('Y-04-01 00:00:00');
                    $to = date('Y-06-30 23:59:59');
                    break;
                case 7:
                case 8:
                case 9:
                    $from = date('Y-07-01 00:00:00');
                    $to = date('Y-09-30 23:59:59');
                    break;
                case 10:
                case 11:
                case 12:
                    $from = date('Y-10-01 00:00:00');
                    $to = date('Y-12-31 23:59:59');
                    break;
            }
            break;
        case 3:
            $month = date('m');
            if ($month < 7) {
                $from = date('Y-01-01 00:00:00');
                $to = date('Y-06-30 23:59:59');
            } else {
                $from = date('Y-07-01 00:00:00');
                $to = date('Y-12-31 23:59:59');
            }
            break;
        case 4:
            $from = date('Y-01-01 00:00:00');
            $to = date('Y-12-31 23:59:59');
            break;
    }
    $ret = array(
        $from,
        $to
    );
    return ($ret);
}

/**
 * 获取下个时间段
 *
 * @return multitype:string
 */
function get_next_range()
{
    $expire = C("COUPON.EXPIRE");
    $idle = C("COUPON.IDLE");
    if (! $idle) {
        $idle = 0;
    }
    $from = '';
    $to = '';
    $current = get_current_range();
    $current[0]=time_format();
    switch ($expire) {
        case 1:
            
            // $from = get_date_add($current[0], 1, 'm');
            // $to = date('Y-m-t', strtotime($from));
            $from = get_date_add($current[0], $idle, 'd'); 
            $to = get_date_add($from, 1, 'm'); 
            break;
        case 2:
            
            // $from = get_date_add($current[0], 3, 'm');
            //$to = get_date_add($current[1], 3, 'm');
            $from = get_date_add($current[0], $idle, 'd');
            $to = get_date_add($from, 3, 'm'); 
            break;
        case 3:
            
            // $from = get_date_add($current[0], 6, 'm');
            $from = get_date_add($current[0], $idle, 'd');
            $to = get_date_add($from, 6, 'm'); 
            break;
        case 4:
            
            // $from = get_date_add($current[0], 1, 'Y');
            $from = get_date_add($current[0], $idle, 'd');
            $to = get_date_add($from, 1, 'Y'); 
            break;
    }
    $ret = array(
        $from,
        $to
    );
    return ($ret);
}

/**
 * 当前可用优惠券总额
 *
 * @return string
 */
function get_my_coupon()
{
    $now = time_format();
    $where = array();
    $where['userid'] = get_userid();
    $where['status'] = 1;
    $where['timefrom'] = array(
        'elt',
        $now
    );
    $where['timeto'] = array(
        'egt',
        $now
    );
    $list = M('coupon')->where($where)
        ->field('amount,coupontype')
        ->select();
    if ($list) {
        $total = 0;
        foreach ($list as $k => $v) {
            if ($v['coupontype'] == 1) {
                $total += $v['amount'];
            } else {
                
                $total -= $v['amount'];
            }
        }
        if ($total < 0) {
            $total = 0;
        }
    } else {
        $total = 0;
    }
    $total = to_price($total);
    return ($total);
}

/**
 * 生成/使用优惠券
 *
 * @param number $userid            
 * @param number $amount            
 * @param number $typeid:
 *            1-消费返券，2-推广返券首次，3-推广再次（获取）
 *            4-订单抵扣（消费）
 * @param string $remark            
 */
function create_coupon($userid = 0, $amount = 0, $typeid = 0, $remark = '')
{
    if ($userid == 0 || $amount == 0 || $typeid == 0) {
        return false;
    }
    
    // $type:0-支出，1-收入
    $type = get_data($typeid, 'coupontype', 'type');
    $data = array();
    $data['userid'] = $userid;
    $data['amount'] = $amount;
    $data['coupontypeid'] = $typeid;
    $data['remark'] = $remark;
    $data['coupontype'] = $type;
    
    $data['addip'] = get_client_ip();
    $data['status'] = 1;
    $data['expiretype'] = C('COUPON.EXPIRE');
    if ($type == 1) {
        // 收入下期生效
        $range = get_next_range();
    } else {
        // 支出马上生效
        $range = get_current_range();
    }
    $data['timefrom'] = $range[0];
    $data['timeto'] = $range[1];
    $db = M('coupon')->data($data)->add();
    if ($db) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取优惠券，返回金额
 *
 * @param number $amount:订单金额            
 * @param number $type:订单类型1-消费，2-推广首次，3-推广再次，4-使用限制            
 */
function get_coupon($amount = 0, $type = 0)
{
    if ($amount == 0 || $type == 0) {
        return false;
    }
    switch ($type) {
        case 1:
            
            // 正常消费
            $set = C('COUPON.SET_X');
            $get = C('COUPON.GET_Y');
            break;
        case 2:
            
            // 推广首次
            $set = C('COUPON.B_SET_X');
            $get = C('COUPON.A_GET_Y');
            break;
        case 3:
            
            // 推广再次
            $set = C('COUPON.B_SET_X1');
            $get = C('COUPON.A_GET_Y1');
            break;
        case 4:
            
            // 消费限制
            $set = C('COUPON.BUY_X');
            $get = C('COUPON.USE_Y');
            break;
    }
    if ($amount < $set) {
        return 0;
    } else {
        if ($type == 2) {
            $total = $get;
        } else {
            $total = intval($amount / $set) * $get;
        }
        return to_price($total);
    }
}

/**
 * 获取优惠券状态
 * Idle , Available , Expired , Used
 * 
 * @param string $status            
 * @param string $timefrom            
 * @param string $timeto            
 */
function get_coupon_status($coupontype = '', $timefrom = '', $timeto = '')
{
    if ($coupontype == 0) {
        return 'Used';
    } else {
        if (get_date_diff($timefrom, time_format(), 's') > 0 && get_date_diff($timeto, time_format(), 's') < 0) {
            return 'Available';
        } else {
            if (get_date_diff($timefrom, time_format(), 's') < 0) {
                return 'Idle';
            } else {
                return 'Expired';
            }
        }
    }
}

/**
 * 当前最多可用
 *
 * @param number $amount            
 */
function get_coupon_maxuse($amount = 0)
{
    $my = get_my_coupon();
    $max = get_coupon($amount, 4);
    $total = $my > $max ? $max : $my;
    return to_price($total);
}

/**
 * 订单完成，返券
 *
 * @param unknown $orderno            
 */
function finishOrder($orderno)
{
    $total = 0;
    $where = array();
    $where['orderno'] = $orderno;
    $where['status'] = 3;
    $db = M('order')->where($where)->find();
    if ($db) {
        // 用户ID
        $userid = $db['userid'];
        // 实际支付-减运费
        $amount = $db['amount'] - $db['shipfee'];
        
        // 1检查是否扣过抵扣积分
        $where = array();
        $where['coupontypeid'] = 4;
        $where['remark'] = $orderno;
        $where['userid'] = $userid;
        $deducted = M('coupon')->where($where)->find();
        if (! $deducted) {
            // 抵扣优惠券
            create_coupon($userid, $db['couponamount'], 4, $orderno);
        }
        
        // 2相关返利
        $where = array();
        $where['coupontypeid'] = 1;
        $where['remark'] = $orderno;
        $where['userid'] = $userid;
        $creditAdded = M('coupon')->where($where)->find();
        if (! $creditAdded) {
            
            // 1.是否有推广人
            $fid = M('member')->where('id=' . $userid)->getField('fatherid');
            if ($fid) {
                // 1.1被推广人是否第一次订单
                $where = array();
                $where['userid'] = $userid;
                $isfirst = M('order')->where($where)->count();
                if ($isfirst == 1) {
                    $total = get_coupon($amount, 2);
                    create_coupon($fid, $total, 2, $orderno);
                } else {
                    $total = get_coupon($amount, 3);
                    create_coupon($fid, $total, 3, $orderno);
                }
            }
            
            // 2.消费返券
            $total = get_coupon($amount, 1);
            create_coupon($userid, $total, 1, $orderno);
        }
    }
    return $total;
}

/**
 * 退单
 * 
 * @param unknown $orderno            
 */
function withdrawOrder($orderno)
{
    $total = 0;
    $where = array();
    $where['orderno'] = $orderno;
    $where['status'] = 3;
    $db = M('order')->where($where)->find();
    if ($db) {
        $where = array();
        $where['remark'] = $orderno;
        M('coupon')->where($where)->delete();
    }
}
?>