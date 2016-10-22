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

class OrderModel extends Model {
    const  DRAFT = 0;//
    const   CONFIRMED  = 1;//
    const    DELIVERING  = 2;//
    const    COMPLETED = 3;//
    const    CANCELLED = 4;//
}
