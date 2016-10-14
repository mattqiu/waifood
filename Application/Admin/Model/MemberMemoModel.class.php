<?php 
namespace Admin\Model;
use Common\Model\RedisModel;
use Think\Model;
 
class MemberMemoModel extends Model{
    const UNTREATED = 1;//未处理
    const FINISH = 0;//已处理

    /**
     * 根据用户id获取备忘录
     * @param $userId
     * @return bool
     */
    public static function getMemo($userId){
        if($userId>0){
            $con['user_id'] = $userId;
            $con['state'] = self::UNTREATED;
            $data = M('MemberMemo')->where($con)->find();
            if($data['content']){
                $key = 'MEMO:USER:ID:'.$data['user_id'];
                RedisModel::set($key,md5(trim($data['content'])));
                return $data;
            }
            return false;
        }else{
            return false;
        }
    }

    /**
     * 添加备忘录
     * @param $userId
     * @param $content
     * @return bool
     */
    public static function modifyMemo($mid,$content){
        if($mid>0 && !empty($content)){
            $con['m_id'] = $mid;
            $data['content'] = $content;
            $rs = M('MemberMemo')->where($mid)->save($data);
            if(false !== $rs){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 完成未完事项
     * @param $mid
     * @return bool
     */
    public static function finishMemo($mid){
        $con['m_id'] = $mid;
        $data['state'] = self::FINISH;
        $rs = M('MemberMemo')->where($con)->save($data);
        if($rs){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 添加备忘录
     * @param $userId
     * @param $content
     * @return bool
     */
    public static function addMemo($userId,$content){
        if($userId>0 && !empty($content)){
            $data['user_id'] = $userId;
            $data['content'] = $content;
            $rs = M('MemberMemo')->add($data);
            if($rs){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
