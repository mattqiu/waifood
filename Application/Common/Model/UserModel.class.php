<?php
namespace Common\Model;
use Think\Model;

class UserModel extends Model
{
	const MALE = 1;//男
	const FEMALE = 0;//女
	const WeCHAT_USER = 3;//微信用户


    public static function getUserById($userId){
        return M('member')->where('id='.$userId)->find();
    }
    public static function getUserByCon($con){
        return M('member')->where($con)->find();
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
     * 检查用户名是否存在
     * @param $username
     * @return bool
     */
    public static  function checkUsernameToUp($username,$id){
        if(!$username){
            return false;
        }
        $where = "username = '$username'";
        $rs  =M ( 'member' )->where ( $where )->find ();
        if(!empty($rs) && $rs['id']!=$id){
            return true;
        }
        return false;
    }

    /**
     * 检查邮箱是否存在
     * @param $username
     * @return bool
     */
    public static  function checkEmailToUp($email,$id){
        if(!$email){
            return false;
        }
        $where = "username = '$email'";
        $rs  =M ( 'member' )->where ( $where )->find ();
        if(!empty($rs) && $rs['id']!=$id){
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


    /**
     * 微信注册新账号
     * @param string $openid
     * @return mixed
     */
    public static function createWechatUser($openid)
    {
        $user = S('openid_' . $openid);
        $maxid = time();
        $data = array();
        $data['usertype'] = 3;
        $data['sort'] = $maxid;
        $data['username'] =  $user['nickname'];
        $data['userpwd'] = md5($user['nickname']);
        $data['indexpic'] = $user['headimgurl'];
        $data['addip'] = get_client_ip();
        $data['sex'] = $user['sex'];
        $data['wechatid'] = $openid;
        $data['weixin'] = $user['nickname'] ;
        // 推荐人
        $u=M('member')->where('id='.get_fid())->find();
        if($u){
            $data['fatherid'] = get_fid();
        }
        $db = M('member')->add($data);
        if ($db) {
            M('member')->where('id=' . $db)->setField(array( 'sort' => $db ));
            self::loginWechat($openid);
        }
        return $db;
    }

    /**
     * 根据openid获取用户
     * @param $openid
     * @return bool|mixed
     */
    public static function getUserByOpenid($openid){
        if (strlen($openid) == 28) {
            $where = array();
            $where['status'] = 1;
            $where['wechatid'] = $openid;
            return M('member')->where($where)->find();
        }else{
            return false;
        }
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
            $where ['_string'] = "email = '{$username}' or telephone = '".str_replace("-", "", $username)."' or username = '{$username}'";
            $where ['userpwd'] =md5($userpwd);
            $user = M ( 'member' )->where ( $where )->find ();
            if (!empty($user)) {
                $data ['lastlogtime'] = time_format ();
                $data ['lastlogip'] = get_client_ip ();
                $data ['logtimes'] = $user ['logtimes'] + 1;
                self::modifyMember($user['id' ],$data);
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

    /**
     *超级管理员使用用户登录
     * @param $userid
     * @return bool
     */
    public static  function loginByAdmin($userid) {
        if ($userid) {
            $user =  self::getUserById($userid);
            if (!empty($user)) {
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

    /**
     * 根据openid登录
     * @param $openid
     * @return bool
     */
    public static  function loginWechat($openid){
        if (strlen($openid) == 28) {
            $user = self::getUserByOpenid($openid);
            if ($user != false) {
                $data = array ();
                $weixinuser = S('openid_' . $openid);
                if($weixinuser){
                    $data['indexpic'] = $weixinuser['headimgurl'];
                    $data['weixin'] = $weixinuser['nickname'] ;
                }
                $data ['lastlogtime'] = time_format ();
                $data ['lastlogip'] = get_client_ip ();
                $data ['logtimes'] = $user ['logtimes'] + 1;
                self::modifyMember( $user ['id' ],$data);
                //取用户折扣
                $level=M('level')->where('id='.$user['usertype'])->find();
                $rate=$level['rate'];
                if(!is_numeric($rate)){
                    $rate=1;
                }
                session('userrate', $rate );
                session ( 'userid', $user ['id'] );
                session ( 'username', $user ['username'] );
                session ( 'usertype', $user ['usertype'] );
                session ( 'wechatid', $user ['wechatid'] );
                return true;
            }
        } else {
            return false;
        }
    }

    /**编辑用户
     * @param $userid
     * @param $data
     * @return bool
     */
    public static function modifyMember($userid,$data){
        if(regex($userid,'number') && !empty($data)){
            $con['id'] = $userid;
            return M('member')->where($con)->save($data);
        }else{
            return false;
        }
    }

    /**重置密码
     * @param $keywrod
     */
    public static function reSetPwd($keywrod){
        $con['_string'] = "`username` = '$keywrod' or `email` = '$keywrod'";
        $user = UserModel::getUserByCon($con);
        if($email = $user['email']){
            $ctrl=new \Org\Util\String();
            $pwd = $ctrl->randString(6,1);
            $body=lbl('tpl_findpwd');
            if(isN($body)){
                apiReturn(CodeModel::ERROR,'sorry,email sent failed');
            }
            $preg="/{(.*)}/iU";
            $n=preg_match_all($preg,$body,$rs);
            $rs=$rs[1];
            if($n>0){
                foreach($rs as $v){
                    if(trim($v)=='name'){
                        $oArr[]='{ name }';
                        $tArr[]= $user['username']?$user['username']:$email;
                        $body=str_replace($oArr,$tArr,$body);
                    }
                    if(trim($v) == 'pwd'){
                        $oArr[]='{ pwd }';
                        $tArr[]= $pwd;
                        $body=str_replace($oArr,$tArr,$body);
                    }
                }
            }
            $subject='[waifood]retrive my password';
            if(send_mail($email,$subject,$body)){
                $where['username']=$user['username'];
                $where['email']=$email;
                if( M('member')->where($where)->setField('userpwd',md5($pwd))){
                    list($name,$end)=explode('@',$email);
                    if(strlen($name)>5){
                        $email =  substr($name,0,3).'***@'.$end;
                    }else{
                        $email =  substr($name,0,1).'***@'.$end;
                    }
                    apiReturn(CodeModel::CORRECT,"An Email with your new password was \r\n just sent to your Email: $email,",'/login/index');
                }
            }else{
                apiReturn(CodeModel::ERROR,'sorry,email sent failed');
            }
        }else{
            apiReturn(CodeModel::ERROR,'sorry,Could not find the user information');
        }
    }

}