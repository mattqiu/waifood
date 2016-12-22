<?php
namespace Common\Model;
use Home\Model\WeixinModel;
use Think\Model;

class UserModel extends Model
{
	const MALE = 1;//男
	const FEMALE = 0;//女
	const WECHAT_USER = 3;//微信用户

	const NORMAL_USERS = 1;//正常使用的账号
	const ATTENTION = 1;//关注

    public static function setUser($user){
        session('user',$user);
    }

    public static function getUser(){
        return session('user');
    }


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
        $data['usertype'] = self::WECHAT_USER;
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
            $user = M('member')->where($where)->find();
            if(!empty($user) && regex($user['id'],'number')){
                return $user;
            }else{
                return false;
            }
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
                $user ['userrate'] = $rate;
                self::setUser($user); //设置user缓存
                //确定后删除下面
                session ( 'userrate', $rate );
                session ( 'userid', $user ['id'] );
                session ( 'username', $user ['username'] );
                session ( 'usertype', $user ['usertype'] );
                session ( 'wechatid', $user ['wechatid'] );
                return true;
            }else {
                return 'Wrong password';
            }
        } else {
            return 'Sorry,password cannot be empty';
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
                $user ['userrate'] = $rate;
                self::setUser($user); //设置user缓存
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
                if($oldUserOpenid = S('oldUserOpenid')){
                    $con['wechatid'] = $oldUserOpenid;
                    $data['wechatid'] = $openid;//跟换openid
                    S('oldUserOpenid','');
                }else{
                    $con['id'] =  $user ['id'];
                }
                $data ['lastlogtime'] = time_format ();
                $data ['lastlogip'] = get_client_ip ();
                $data ['logtimes'] = $user ['logtimes'] + 1;
                self::modifyMemberByCon($con,$data);
                //self::modifyMember( $user ['id' ],$data);
                //取用户折扣
                $level=M('level')->where('id='.$user['usertype'])->find();
                $rate=$level['rate'];
                if(!is_numeric($rate)){
                    $rate=1;
                }
                $user ['userrate'] = $rate;
                self::setUser($user); //设置user缓存
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

    /**编辑用户
     * @param $userid
     * @param $data
     * @return bool
     */
    public static function modifyMemberByCon($con,$data){
        if(!empty($con) && !empty($data)){
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
                apiReturn(CodeModel::ERROR,'Failed to send Email, please confirm your Email address or network');
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
                    apiReturn(CodeModel::CORRECT,"An Email with your new password was \r\n just sent to: $email,",'/login/index');
                }
            }else{
                apiReturn(CodeModel::ERROR,'sorry,email sent failed');
            }
        }else{
            apiReturn(CodeModel::ERROR,'Could not find your information');
        }
    }

    /**绑定用户，将微信用户信息合并到pc用户（金额，积分，订单，地址。。。）
     * @param $openid
     * @param $data
     * @return bool
     */
    public static function bindMember($openid,$data){
        GLog('bindMember','star');
        if (strlen($openid) == 28 && $data['username'] && $data['userpwd']) {
            $wechatuser =  self::getUserByOpenid($openid);//获取当前登录用户信息
            //能被openid找到的非微信用户（即：已绑定的不能再绑定）
            if($wechatuser['usertype'] != self::WECHAT_USER){
                GLog('bindMember','该微信号已经绑定网络账号');
                apiReturn(CodeModel::ERROR,'Your account was already bound');
                return false;
            }

            $where['status'] = UserModel::NORMAL_USERS;
            $where ['_string'] = "email = '{$data['username']}' or telephone = '". replaceTel($data['username'])."' or username = '{$data['username']}'";
            $where['userpwd'] = md5($data['userpwd']);
            $pcuser = M('member')->where($where)->find();
            if(!$pcuser){
                GLog('bindMember','账号密码错误');
                apiReturn(CodeModel::ERROR,'Wrong account or password.');
                return false;
            }
            $savedata = array();
            $savedata['wechatid'] = $wechatuser['wechatid'];
            $savedata['indexpic'] = $wechatuser['indexpic'];
            $savedata['weixin'] = $wechatuser['weixin'];
            $savedata['amount'] = floatval($wechatuser['amount'] + $pcuser['amount']);
            $savedata['balance'] = floatval($wechatuser['balance'] + $pcuser['balance']);
            //使用事务保证绑定成功
            M()->startTrans();
            //将微信用户的订单绑定到pc用户上
            $ordercon['userid'] = $wechatuser['id'];
            $orderSaveData['userid'] = $pcuser['id'];
            $rs = OrderModel::modifyOrderByCon($ordercon,$orderSaveData);
            if(!$rs){
                GLog('bindMember','修改微信用户订单失败');
              //  M()->rollback();
              //  return false;
            }

            //将微信用户的收货地址绑定到pc用户上
            $addrcon['userid'] = $wechatuser['id'];
            $addrSaveData['userid'] = $pcuser['id'];
            $rs = AddressModel::modifyAddrByCon($addrcon,$addrSaveData);
            if(!$rs){
                GLog('bindMember','修改微信用户收货地址失败');
             //   M()->rollback();
               // return false;
            }
            $con['id'] = $pcuser['id'];
            $rs = UserModel::modifyMemberByCon($con,$savedata);
            if(!is_number($rs)){
                GLog('bindMember','合并微信与pc用户的信息失败：'.M()->getDbError());
                M()->rollback();
                return false;
            }
            //删除该微信用户
            $deletecon['id'] = $wechatuser['id'];
            $rs = M('member')->where($deletecon)->delete();
            if(!$rs){
                GLog('bindMember','删除微信用户失败');
                M()->rollback();
                return false;
            }
            self::login($data['username'],$data['userpwd']);
            M()->commit();
            return true;
        }else{
            return false;
        }
    }

    public static function sendRegEmail($userid){
        $body = lbl('tpl_register');
        $user = self::getUserById($userid);
        if (!isN($body) && !empty($user)) {
            $subject='[waifood]register successfully';
            $preg = "/{(.*)}/iU";
            $n = preg_match_all($preg, $body, $rs);
            if ($n > 0) {
                $rs = $rs[1];
                foreach ($rs as $v) {
                    if (isset($user[$v])) {
                        $oArr[] = '{' . $v . '}';
                        $tArr[] = $user[$v];
                        $body = str_replace($oArr, $tArr, $body);
                    }
                }
            };
            if (send_mail($user['email'], $subject, $body)) {
                GLog('reg send email','send reg email success');
            } else {
                GLog('reg send email','send reg email error');
            }
        }
    }

    /**
     * 是否关注
     */
    public static  function isAttention() {
        // 用户权限检查
        $user = self::getUser();
        if(empty($user)){
            return false;
        }else{
            //没有关注
            if(!$isAttention = S('IS_SUBSCRIBE')){
                if($user['subscribe'] != self::ATTENTION){
                    $rs = self::checkUserIsAttention($user['wechatid']);
                    if($rs){
                        S('IS_SUBSCRIBE',1,86400*15);
                        return true;
                    }else{
                        S('IS_SUBSCRIBE',0,86400*15);//半个月检测一次
                        return false;
                    }
                }else{
                    S('IS_SUBSCRIBE',1,86400*15);
                }
            }
            return  $isAttention;
        }
    }

    /**检测用户是否关注
     * @param $openid
     * @return bool
     */
    public static function checkUserIsAttention ($openid){
        if(!$openid){
            return false;
        }
        $token = WxJsSdkModel::getWxToken();
        $subscribe_msg = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid";
        $data =json_decode(WeixinModel::http_get($subscribe_msg));
        if($data){
            $con['wechatid'] = $openid;
            $savedata['subscribe'] = $data->subscribe;
            self::modifyMemberByCon($con,$savedata);
            self::setUser(self::getUserByOpenid($openid)); //修改后设置认证状态
            if($data->subscribe === self::ATTENTION){
                return true;
            }else{
                return false;
            }
        }
    }
}