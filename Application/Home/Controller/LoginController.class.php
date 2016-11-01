<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

use Common\Model\CodeModel;
use Common\Model\UserModel;
Use \Think\Controller;

class LoginController extends Controller
{
    protected $conf='';
    private  $returnUrl="http://www.waifood.com/home/"; //回调地址

    /**
     * 会员首页
     */
    public function _initialize()
    {
        // 载入模板
        if (C('config.WEB_SITE_TEMPLATE')) {
            C('DEFAULT_THEME', C('config.WEB_SITE_TEMPLATE'));
        }
        $this->conf=C('config');
        $this->assign('shoptitle', 'Waifood home');
    }

    public function index()
    {
        if (is_wechat()) {
           if($openid = openid()){
               UserModel::loginWechat($openid);
           }else if (!get_userid()) { //没有用户信息,进行微信授权
                $conf =  $this->conf;
                $state = mt_rand(100000,999999);
                session('verify_state', $state);
                $appid = $conf['WECHAT_APPID'];
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=' . urlencode($this->returnUrl.'/Weixin/weixin_callback') . '&response_type=code&scope=snsapi_userinfo&state=' . $state . '#wechat_redirect';
                trace("url $url");
                header("Location:$url");
                exit();
            }
        }
    }

    public function register()
    {
        if (IS_POST) {
            $data = I('post.');
            $data['telephone'] = replaceTel($data['telephone']);
            if( !isVerifyCorrect()){
                apiReturn(CodeModel::ERROR,'sorry,verifycation code is illegal.');
            }
            if(!regex($data['telephone'],'mob')){
                apiReturn(CodeModel::ERROR,'sorry,the phone number format is wrong!');
            }
            if (!regex($data['username'],'username') ) {
                apiReturn(CodeModel::ERROR,'sorry,the user name format is not correct');
            }
            if (strlen($data['userpwd'])>20 || strlen($data['userpwd'])<4) {
                apiReturn(CodeModel::ERROR,'Sorry,the password should be 4 to 20 characters!');
            }
            if ($data['userpwd'] !== $data['userpwd1']) {
                apiReturn(CodeModel::ERROR,'enter the password twice inconsistent.');
            }
            if (!regex($data['email'],'email')) {
                apiReturn(CodeModel::ERROR,'sorry,email is illegal.');
            }
            if(UserModel::checkEmail($data['email'])){
                apiReturn(CodeModel::ERROR,'sorry,the email already exists');
            }
            if(UserModel::checkUsername($data['username'])){
                apiReturn(CodeModel::ERROR,'sorry,the username already exists');
            }
            $username=$data['username'];
            $userpwd=$data['userpwd'];
            $maxid = M ( 'member' )->count ();
            $data ['usertype'] = 1;
            $data ['sort'] = $maxid;
            $data ['userpwd'] = md5($userpwd);
            $data ['addip'] = get_client_ip ();
            $data ['fatherid'] = get_fid();
            unset($data['userpwd1'],$data['verify']);
            $id = UserModel::reg($data);
            if($id){
                $subject='[waifood]register successfully';
                sendEmail($data['email'],$subject);
                //注册完后自动登录
                if(UserModel::login($username, $userpwd)){//注册后自动登录
                    apiReturn(CodeModel::CORRECT,'Registered successfully','/member/index');
                }else{
                    apiReturn(CodeModel::CORRECT,'Registered successfully','/login/index');
                }
            }else{
                apiReturn(CodeModel::ERROR,'Registration failed');
            }
        } else {
            // 输出当前Member等级列表
            $levels = M("level")->where('status=1')
                ->order('id asc')
                ->select();
            $this->assign("levels", $levels);
            $this->assign('title', 'Register');
            $this->display();
        }
    }


    /**
     * 绑定
     */
    public function bind()
    {
        if (IS_POST) {
            $data = $_POST;
            $login = $this->login($data['username'], md5($data['userpwd']));
            if ($login) {
                if ($data['bind']) {
                    // 已登录自动绑定，可能是有问题滴
                    $openid = openid();
                    $data=array();
                    $wid = M('member')->where('id=' . get_userid())->setField('wechatid', $openid);
                    session('wechatid', $openid);
                } else {}
                redirect(U('Member/index'));
            } else {
                $this->error('wrong user name or password.');
            }
        } else {
            if (is_wechat()) {
                $user = S('openid_' . openid());
                if (!$user) {//没有用户信息进入微信授权
                    session ( 'userid','');
                    redirect(U('Login/index'));
                }
                $this->assign('user', $user);
            } else {
                // we('请使用微信浏览');
            }
            $this->assign('title', 'Login');
            $this->display();
        }
    }

    /**
     * 已登录直接绑定
     */
    public function bindNow()
    {
        $openid = openid();
        $wid = M('member')->where('id=' . get_userid())->setField('wechatid', $openid);
        session('wechatid', $openid);
        redirect(U('m_cashier'));
    }

    public function loginAtion(){
        $key = 'verify_err_num';
        $num = cookie($key);
        if(isset($num) && $num>=3){
            if( !isVerifyCorrect()){
                apiReturn(CodeModel::ERROR, 'sorry,verifycation code is illegal.');
            }
        }
        // 登录
        $username = I('username');
        $userpwd = I('userpwd');
        if (UserModel::login($username, $userpwd)) {
            cookie($key,0);//成功登陆清除登陆失败记录次数
            apiReturn(CodeModel::CORRECT, 'Congratulations, login successfully','/member/index');
        } else {
            //记录登录失败的次数
            $num = cookie($key);
            if(!$num){
                $num =1;
            }else{
                $num++;
            }
            cookie($key,$num);
            apiReturn(CodeModel::ERROR, 'wrong user name or password.');
        }
    }

    /**
     * 用户登录
     *
     * @param string $username            
     * @param string $userpwd            
     * @param string $verify            
     */
    protected function login($username, $userpwd)
    {
        if (! (isN($username) || isN($userpwd))) {
            // 设置id,username, wechatid
            $where = array();
            $where['status'] = 1;
            $where ['_string'] = "email = '{$username}' or telephone = '". replaceTel($username)."' or username = '{$username}'";
            $where['userpwd'] = $userpwd;
            $db = M('member')->where($where)->find();
            if ($db != false) {
                $data = array();
                $data['id'] = $db['id'];
                $data['lastlogtime'] = time_format();
                $data['lastlogip'] = get_client_ip();
                $data['logtimes'] = $db['logtimes'] + 1;
                M('member')->save($data);
                
                // 取用户折扣
                $level = M('level')->where('id=' . $db['usertype'])->find();
                $avatar = $level['indexpic'];
                $rate = $level['rate'];
                if (! is_numeric($rate)) {
                    $rate = 1;
                }
                if (isN($db['avatar'])) {
                    $wid = M('member')->where('id=' . $db['id'])->setField('avatar', $avatar);
                }

                session('userid', $db['id']);
                session('username', $db['username']);
                session('userrate', $rate); // 没作用1
                session('usertype', $db['usertype']); // 没作用2
                session('wechatid', $db['wechatid']);
                
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        session('[destroy]');
        $this->redirect('Index/index');
    }
    // 验证码生成
    public function verify()
    {
        return get_verify();
    }

    public function findpwdAction(){
        if( !isVerifyCorrect()){
            apiReturn(CodeModel::ERROR, 'sorry,verifycation code is illegal.');
        }
        $keywrod = I('post.keywrod');
        UserModel::reSetPwd($keywrod);
    }

    public function findpwd()
    {
        if (IS_POST) {
            
            $data = empty($data) ? $_POST : $data;
            $username = $data['username'];
            $email = $data['email'];
            
            if (isN($username)) {
                $this->error('sorry, user name cannot be empty.');
            }
            if (! is_email($email)) {
                $this->error('sorry, email illegal.');
            }
            
            $where = array();
            $where['username'] = $username;
            $where['email'] = $email;
            $db = M('member')->where($where)->find();
            if ($db) {
                // 发送邮件;
                $ctrl = new \Org\Util\String();
                $pwdcode = $ctrl->randString(6);
                $to = $email;
                $subject = '[waifood]retrive my password';
                // $body='To reset your password, please click the link next: <a href="" target="_blank">Reset my password</a>. verification code:'.$pwdcode;
                $body = lbl('tpl_findpwd');
                if (isN($body)) {
                    $this->error('sorry,email sent failed');
                }
                
                $preg = "/{(.*)}/iU";
                $n = preg_match_all($preg, $body, $rs);
                $rs = $rs[1];
                if ($n > 0) {
                    foreach ($rs as $v) {
                        if (isset($$v)) {
                            $oArr[] = '{' . $v . '}';
                            $tArr[] = $$v;
                            $body = str_replace($oArr, $tArr, $body);
                        }
                    }
                }
                
                if (send_mail($to, $subject, $body)) {
                    M('member')->where($where)->setField('pwdcode', $pwdcode);
                    $this->redirect('Login/findpwd2?email=' . $to);
                } else {
                    $this->error('sorry,email sent failed');
                }
            } else {
                $this->error('sorry,the information does not match.');
            }
        } else {
            
            $title = 'Retrieve my password - step 1.';
            $keywords = $title . lbl('subtitleshop');
            $description = $title . lbl('subtitleshop');
            $this->assign('title', $title);
            $this->assign('keywords', $keywords);
            $this->assign('description', $description);
            
            $this->display();
        }
    }

    Public function findpwd2($email = '')
    {
        if (IS_POST) {
            
            $data = empty($data) ? $_POST : $data;
            $userpwd = $data['userpwd'];
            $userpwd1 = $data['userpwd1'];
            $pwdcode = $data['pwdcode'];
            
            if (isN($userpwd)) {
                $this->error('sorry, password cannot be empty.');
            }
            
//             if (strlen($data['userpwd']) < 2) {
//                 $this->error('sorry,the length of the password must be at least 2.');
//             }
            if ($userpwd != $userpwd1) {
                $this->error('enter the password twice inconsistent.');
            }
            
            $where = array();
            $where['pwdcode'] = $pwdcode;
            $db = M('member')->where($where)->find();
            if ($db) {
                M('member')->where($where)->setField(array(
                    'pwdcode' => '',
                    'userpwd' => md5($userpwd)
                ));
                $this->success('Your password has been reseted successfully.', U('Login/index'), 3);
            } else {
                $this->error('sorry,password reset failed.');
            }
        } else {
            
            $title = 'Retrieve my password - step 2.';
            $keywords = $title . lbl('subtitleshop');
            $description = $title . lbl('subtitleshop');
            $this->assign('title', $title);
            $this->assign('keywords', $keywords);
            $this->assign('description', $description);
            $this->assign('email', $email);
            
            $this->display();
        }
    }
}
?>