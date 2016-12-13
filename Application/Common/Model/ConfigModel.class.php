<?php
namespace Common\Model;
use Think\Model;
class ConfigModel extends Model {

    /**获取配置
     * @param $name
     * @return bool
     */
    public static function getConfig($name){
        if($name){
            $con['name'] = $name;
            $conf = M('config')->where($con)->find();
            if(!empty($conf)){
                return $conf['value'];
            }
        }
        return false;
    }
}

?>