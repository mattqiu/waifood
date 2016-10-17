<?php
namespace Common\Model;
use Think\Model;

class UserModel extends Model
{
	const MALE = 1;//男
	const FEMALE = 0;//女

    public static function getUserById($userId){
        return M('member')->where('id='.$userId)->find();
    }

    /**
     * 检查用户名是否存在
     * @param $username
     * @return bool
     */
    public static  function checkUsername($username){
        if(!$username){
            return false;
        }
        $where = "username = '$username'";
        $rs  =M ( 'member' )->where ( $where )->find ();
        if(!empty($rs)){
            return true;
        }
        return false;
    }

    /**
     * 检查邮箱是否存在
     * @param $username
     * @return bool
     */
    public static  function checkEmail($email){
        if(!$email){
            return false;
        }
        $where = "username = '$email'";
        $rs  =M ( 'member' )->where ( $where )->find ();
        if(!empty($rs)){
            return true;
        }
        return false;
    }

    public static function reg($data){
        $db = M ( 'member' )->add ( $data );
        if($db>0){
            return $db;
        }
        return false;
    }

    public static function updataUserLogin(){

    }

    /**
     *用户登录
     * @param $username
     * @param $userpwd
     * @return bool
     */
    public static  function login($username, $userpwd) {
        if ($username && $userpwd) {
            $where = array ();
            $where ['status'] = 1;
            if(regex($username,'email')){ //邮箱登录
                $where ['email'] = $username;
            }elseif(regex($username,'mob')){//手机登录
                $where ['telephone'] = $username;
            }else{
                $where ['username'] = $username;//用户名登录
            }
            $where ['userpwd'] =md5($userpwd);
            $user = M ( 'member' )->where ( $where )->find ();
            if (!empty($user)) {
                $data ['id'] = $user ['id' ] ;
                $data ['lastlogtime'] = time_format ();
                $data ['lastlogip'] = get_client_ip ();
                $data ['logtimes'] = $user ['logtimes'] + 1;
                M ( 'member' )->save ( $data );
                //取用户折扣
                $level=M('level')->where('id='.$user['usertype'])->find();
                $rate=$level['rate'];
                if(!is_numeric($rate)){
                    $rate=1;
                }
                session ( 'userrate', $rate );
                session ( 'userid', $user ['id'] );
                session ( 'username', $user ['username'] );
                session ( 'usertype', $user ['usertype'] );
                session ( 'wechatid', $user ['wechatid'] );
                return true;
            }else {
                return false;
            }
        } else {
            return false;
        }
    }

}