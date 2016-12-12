<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;
use Common\Model\CodeModel;
use Common\Model\UserModel;
Use \Think\Controller;

class LoginController extends Controller {
	/**
	 * 会员首页
	 */
	public function _initialize() {
		//载入模板
		if(C('config.WEB_SITE_TEMPLATE')){
			C('DEFAULT_THEME',C('config.WEB_SITE_TEMPLATE'));
		}
	}

	public function index() {
        $title = 'login';
        $keywords = $title.lbl('subtitleshop');
        $description = $title.lbl('subtitleshop');
        $appid = C('WECHAT_APPID');
        $this->assign('appid',$appid);
        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->display();
	}

    /**
     * 登录
     */
    public function login() {
        //验证码
        if( !isVerifyCorrect()){
            apiReturn(CodeModel::ERROR,'Wrong captcha code.');
        }
        $username = I('post.username');
        $password = I('post.password');
        if(!$username || !$password){
            apiReturn(CodeModel::ERROR,'Wrong user name or password.');
        }
        if(true===$msg = UserModel::login($username,$password)){
            if($url = session('backurl')){
                session('backurl',false);
                apiReturn(CodeModel::CORRECT,'Successful',$url);
            }
            apiReturn(CodeModel::CORRECT,'Successful','/');
        }else{
            apiReturn(CodeModel::ERROR,$msg);
        }
    }

    //网页端扫码登陆微信回调界面
    public function weixin_callback(){
        $code = I('get.code');
        if (empty($code)) {
        }
        $state = I('get.state');
        $verifyState = session('verify_state') ? : I('get.rand');
        if (!$state or $state != $verifyState) {
            dump(333);exit;
        }
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?'
            .'appid=wx34c9f8748aad457e&secret=74485226ffdf4b35645588943b38fbc8&code='
            .$code.'&grant_type=authorization_code';
        import('ORG.lingdian.HttpRequest');
        $data = json_decode(HttpRequest::get($url), true);
        if (!isset($data['access_token'])) {
            throw new Exception('获取access_token错误返回');
        }
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='
            .$data['access_token'].'&openid=' . $data['openid'];
        $user = json_decode(HttpRequest::get($url), true);
        $this->_weiCallback($user);
    }

    public function reg(){
        $data = I('post.');
        $data['telephone'] = replaceTel($data['telephone']);
        if( !isVerifyCorrect()){
            apiReturn(CodeModel::ERROR,'Wrong captcha code.');
        }
        if (!regex($data['username'],'username') ) {
            apiReturn(CodeModel::ERROR,'Wrong user name format.');
        }
        if (!regex($data['email'],'email')) {
            apiReturn(CodeModel::ERROR,'Wrong Email format.');
        }
        if(UserModel::checkEmail($data['email'])){
            apiReturn(CodeModel::ERROR,'Sorry, this email already exists.');
        }
        if(UserModel::checkUsername($data['username'])){
            apiReturn(CodeModel::ERROR,'Sorry, this user name already exists.');
        }

        if (strlen($data['password'])>20 || strlen($data['password'])<4) {
            $this->error('Password should be at least 4 characters.');
        }
        if ($data['password'] !== $data['password1']) {
            apiReturn(CodeModel::ERROR,'The two passwords are not same.');
        }
        $username=$data['username'];
        $userpwd=$data['password'];
        $maxid = M ( 'member' )->count ();
        $data ['usertype'] = 1;
        $data ['sort'] = $maxid;
        $data ['userpwd'] = md5($userpwd);
        $data ['addip'] = get_client_ip ();
        $data ['fatherid'] = get_fid();
        unset($data['password1'],$data['verify'],$data['password']);
        $id = UserModel::reg($data);
        if($id){
            $subject='[waifood]register successfully';
            sendEmail($data['email'],$subject);
            //注册完后自动登录
            if(true===UserModel::login($username, $userpwd)){//注册后自动登录
                apiReturn(CodeModel::CORRECT,'Successful','/member/index');
            }else{
                apiReturn(CodeModel::CORRECT,'Successful','/login/index?returnurl=/member/index');
            }
        }else{
            apiReturn(CodeModel::ERROR,'Failed, unexpected problem.');
        }
    }

	public function register() {
		$title = 'register';
        $keywords = $title.lbl('subtitleshop');
        $description = $title.lbl('subtitleshop');
        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->display();
	}

	public function logout() {
		session_unset ();
		session_destroy ();
		session('[destroy]');
		$this->redirect('/');
	}

    /**
     * 找回密码
     */
    public function findpwdAction(){
        if( !isVerifyCorrect()){
            apiReturn(CodeModel::ERROR,'Wrong captcha code.');
        }
        $keywrod = I('post.keywrod');
        UserModel::reSetPwd($keywrod);
    }
}
?>