<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;
use Common\Model\CodeModel;
use Common\Model\UserModel;
Use \Think\Controller;
use Think\Log;

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

	public function test(){
        we(ini_get('session.gc_maxlifetime'));
	}

    public function findpwdAction(){
        if( !isVerifyCorrect()){
            apiReturn(CodeModel::ERROR, 'sorry,verifycation code is illegal.');
        }
        $keywrod = I('post.keywrod');
        UserModel::reSetPwd($keywrod);
    }

	public function findpwd(){
        $title = 'Retrieve my password - step 1.';
        $keywords = $title.lbl('subtitleshop');
        $description = $title.lbl('subtitleshop');
        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->display();
	}
	
	Public function findpwd2($email=''){
		if(IS_POST){
			$data = empty ( $data ) ? $_POST : $data;
			$userpwd=$data['userpwd'];
			$userpwd1=$data['userpwd1'];
			$pwdcode=$data['pwdcode'];
			if (isN($userpwd)) {
				$this->error('sorry, password cannot be empty.');
			}
//   			if (strlen($data['userpwd'])<2) {
// 				$this->error('sorry,the length of the password must be at least 2.');
//   			}
			if ($userpwd != $userpwd1) {
				$this->error('enter the password twice inconsistent.');
			}

			$where=array();
			$where['pwdcode']=$pwdcode;
			$db=M('member')->where($where)->find();
			if($db){
				M('member')->where($where)->setField(array(
				'pwdcode'=>'',
				'userpwd'=>md5($userpwd)
				));
				$this->success('Your password has been reseted successfully.',U('Login/index'));

			}else{
				$this->error('sorry,password reset failed.');
			}

		}else{
			$title = 'Retrieve my password - step 2.';
			$keywords = $title.lbl('subtitleshop');
			$description = $title.lbl('subtitleshop');
			$this->assign('title',$title);
			$this->assign('keywords',$keywords);
			$this->assign('description',$description);
			$this->assign('email',$email);
			$this->display();
		}
	}

	public function index() {
        $key = 'verify_err_num';
        $url=I('returnurl')?I('returnurl'):'/';
        if(IS_POST){
//            $num = cookie($key);
//            if(isset($num) && $num>=3){
                if( !isVerifyCorrect()){
                    $this->error ( 'sorry,verifycation code is illegal.');
                }
//            }
            //登录
            $username=I('username');
            $userpwd=I('userpwd');
            if(UserModel::login($username,$userpwd)){
                cookie($key,0);//成功登陆清除登陆失败记录次数
                if(!strpos(strtolower($url),'login')){
                    redirect($url);
                }else{
                    $this->redirect('Member/index');
                }
            }else{
                //记录登录失败的次数
                $num = cookie($key);
                if(!$num){
                    $num =1;
                }else{
                    $num++;
                }
                cookie($key,$num);
                $this->error('wrong user name or password.');
            }
		}else{
            $title = 'login';
			$keywords = $title.lbl('subtitleshop');
			$description = $title.lbl('subtitleshop');
			$this->assign('title',$title);
			$this->assign('keywords',$keywords);
			$this->assign('description',$description);
			$this->display();
		}
	}

    public function reg(){
        $data = I('post.');
        $data['telephone'] = replaceTel($data['telephone']);
        if( !isVerifyCorrect()){
            apiReturn(CodeModel::ERROR,'sorry,verifycation code is illegal.');
        }
        if (!regex($data['username'],'username') ) {
            apiReturn(CodeModel::ERROR,'sorry,the user name format is not correct');
        }
        if (strlen($data['userpwd'])>20 || strlen($data['userpwd'])<4) {
            $this->error('Sorry,the password should be 4 to 20 characters!');
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
                apiReturn(CodeModel::CORRECT,'register successfully','/member/index');
            }else{
                apiReturn(CodeModel::CORRECT,'register successfully','/login/index?returnurl=/member/index');
            }
        }else{
            apiReturn(CodeModel::ERROR,'Registration failed');
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
	
	public function loginWechat($openid = null) {
		if (null== $openid){$openid=I('openid');};
		if (strlen ( $openid ) == 28) { 
			if (get_userid () == 0) {
				$where = array ();
				$where ['status'] = 1;
				$where ['wechatid'] = $openid;
				$db = M ( 'member' )->where ( $where )->find ();
				if ($db != false) {

					$this->login ( $db ['username'], $db ['userpwd'] );
				} else {
					$newid = $this->createWechatUser ( $openid );
					$db= M ( 'member' )->where ( $where )->find ($newid);
					if ($db != false) {
						$this->login ( $db ['username'], $db ['userpwd']);
					}
				}
			}else{
				return false; 
			}
		} else {
			return false;
			//$this->redirect ( 'Login/index' );
		}
	}
	public function createWechatUser($openid = null) {
		if (strlen ( $openid ) != 28) {
			return false;
		}
		$where = array ();
		$where ['wechatid'] = $openid;
		$db = M ( 'member' )->where ( $where )->find ();
		if ($db != false) {
			return $db ['id'];
		} else {
			$maxid = M ( 'member' )->count ();
			$username = 'wechat_' . ($maxid + 1);
			$data = array ();
			$data ['usertype'] = 1;
			$data ['sort'] = $maxid;
			$data ['username'] = $username;
			$data ['userpwd'] = md5 ( $username );
			$data ['addip'] = get_client_ip ();
			$data ['sex'] = 1;
			$data ['wechatid'] = $openid;
			$data ['status'] = 1;
			$db = M ( 'member' )->add ( $data );
			return $db;
		}

		
	}
	
	//自动创建用户
	public function create($openid = null) { 
		if ( $openid==null) {
			$openid=session_id();
		}
		$where = array ();
		$where ['wechatid'] = $openid;
		$db = M ( 'member' )->where ( $where )->find ();
		if ($db != false) {
			$uid=   $db ['id'];
		} else {
			$maxid = M ( 'member' )->count ();
			$username = 'user_' . ($maxid + 1);
			$data = array ();
			$data ['usertype'] = 2;
			$data ['sort'] = $maxid;
			$data ['username'] = $username;
			$data ['userpwd'] = md5 ( $username );
			$data ['addip'] = get_client_ip ();
			$data ['sex'] = 1;
			$data ['wechatid'] = $openid;
			$data ['status'] = 1;
			$db = M ( 'member' )->add ( $data );
			$uid= $db; 
		}
		
	    M('member')->where('id=' . $uid)->setField(array(
	    'username' => 'user_' .$uid 
	    ));
		    
		$where = array ();
		$where ['status'] = 1;
		$db= M ( 'member' )->where ( $where )->find ($uid);
		if ($db != false) {
			$this->login ( $db ['username'], $db ['userpwd'] );
			if( IS_AJAX ){
				echo '1';
			}else{
				$this->redirect('Settle/cashier');
			}
		}
	}

	public function loginbox($username = null, $userpwd = null ) {
			$data=$this->login ($username,md5($userpwd) );
			if($data){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		
	}
	/**
	 * 用户登录
	 * 
	 * @param string $username        	
	 * @param string $userpwd        	
	 */
	protected function login($username, $userpwd) {
		if (!(isN($username)||isN($userpwd))) {
			// 设置id,username, wechatid
			$where = array ();
			$where ['status'] = 1;
			$where ['_string'] = "email = {$username} or telephone = {". replaceTel($username)."} or username = {$username}";
			$where ['userpwd'] = $userpwd;
			$user = M ( 'member' )->where ( $where )->find ();
            if (!empty($user)) {
                $key = 'verify_err_num';
                cookie($key,0);//成功登陆清除登陆失败记录次数
                $data = array ();
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
	public function logout() {
		session_unset ();
		session_destroy ();
		session('[destroy]');
		$this->redirect('Index/index');
	}
	// 验证码生成
	public function verify() {
		return get_verify ();
	}
}
?>