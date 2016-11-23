<?php
namespace Common\Model;
use Think\Model;
class ContentModel extends Model {
    const PROMOTION = 9;//Promotion
    const NEW_ARRIVAL = 8;//New Arrival
    const RECOMMEND = 11;//Recommend
    const NORMAL = 1;//Recommend

    /**根据分组获取商品
     * @param $group
     * @return mixed
     */
    public static  function getGroupContent($group,$order=array()) {
        $db = M ( "magic" )->find ( $group );
        $where=array();
        $where['id']=array('in',$db['contentids']);
        $where ['status'] = 1;
        if(empty($order)){
            $order= 'addtime desc';
        }
        $field =  'id,title,indexpic,price,price1,description,unit,storage,origin,brand,stock';
        $list=M('content')->where($where)->field($field)->order($order)->select();
        return $list;

    }

}

?>