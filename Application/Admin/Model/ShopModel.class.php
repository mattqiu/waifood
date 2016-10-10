<?php
namespace Admin\Model;
use Think\Model;
 
class ShopModel extends Model{ 
    protected $_validate = array(
        array('name', 'require', '门店名不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '', '门店名已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH)
    );

    protected $_auto = array( 
        array('status', '1', self::MODEL_INSERT),
        array('sorttype', '0', self::MODEL_INSERT),
    ); 

}
