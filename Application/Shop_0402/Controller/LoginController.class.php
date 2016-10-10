<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;
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

	public function test(){
		we(ini_get('session.gc_maxlifetime'));
		
	}
	
	public function findpwd(){

		if(IS_POST){

			$data = empty ( $data ) ? $_POST : $data;
			$username=$data['username'];
			$email=$data['email'];

			if (isN($username)) {
				$this->error('sorry, user name cannot be empty.');
			}
			if (!is_email($email)) {
				$this->error('sorry, email illegal.');
			}
			
			$where=array();
			$where['username']=$username;
			$where['email']=$email;
			$db=M('member')->where($where)->find();
			if($db){
				//发送邮件;
				$ctrl=new \Org\Util\String();
				$pwdcode=$ctrl->randString(6);
				$to=$email;
				$subject='[waifood]retrive my password';
				//$body='To reset your password, please click the link next: <a href="" target="_blank">Reset my password</a>.  verification code:'.$pwdcode;
				$body=lbl('tpl_findpwd');
				if(isN($body)){
					$this->error('sorry,email sent failed');
				}
				
				
				$preg="/{(.*)}/iU";
				$n=preg_match_all($preg,$body,$rs);
				$rs=$rs[1];
				if($n>0){
					foreach($rs as $v){
						if(isset($$v)){
							$oArr[]='{'.$v.'}';
							$tArr[]=$$v;
							$body=str_replace($oArr,$tArr,$body);
						}
					}
				}
				
				
				if(send_mail($to,$subject,$body)){
					M('member')->where($where)->setField('pwdcode',$pwdcode);
					$this->redirect('Login/findpwd2?email='.$to);
				}else{
					$this->error('sorry,email sent failed');
				}
				
			}else{
				$this->error('sorry,the information does not match.');
			} 
			
		}else{

			$title = 'Retrieve my password - step 1.';
			$keywords = $title.lbl('subtitleshop');
			$description = $title.lbl('subtitleshop');
			$this->assign('title',$title);
			$this->assign('keywords',$keywords);
			$this->assign('description',$description);
				
			$this->display();
		}
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
		if(IS_POST){
			//登录
			$username=I('username');
			$userpwd=md5(I('userpwd'));
			$verify= I('verify');
			$url=I('returnurl');
			if($this->login($username,$userpwd,$verify)){
			    if(!strpos(strtolower($url),'login')){
			        redirect($url);
			    }else{
				    $this->redirect('Member/index');
			    }
			}else{
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
	
	public function register() {  
		if(IS_POST){
			$data = empty ( $data ) ? $_POST : $data;
			$username=$data['username'];
			$userpwd=md5($data['userpwd']);
			$userpwd1=md5($data['userpwd1']);
			$verify=($data['verify']);
			if (isN($username)) {
				$this->error('sorry, user name cannot be empty.');
			} 
			
// 			if (strlen($data['userpwd'])<2) {
// 				$this->error('sorry,the length of the password must be at least 2.');
// 			}
			if ($userpwd != $userpwd1) {
				$this->error('enter the password twice inconsistent.');
			}
			if (!is_email($data['email'])) {
				$this->error('sorry,email is illegal.');
			}
			
			if (! check_verify ( $verify )) {
				$this->error ( 'sorry,verifycation code is illegal.' );
			}
			
			
			$where = array ();
			$where ['username'] = $username;
			$db = M ( 'member' )->where ( $where )->find ();
			
			if ($db != false) {
				$this->error('user name ['.$username.'] already exists.');
			} else {
				$maxid = M ( 'member' )->count (); 
				//$data = array ();
				$data ['usertype'] = 1;
				$data ['sort'] = $maxid;
				$data ['username'] = $username;
				$data ['userpwd'] =  $userpwd;
				$data ['addip'] = get_client_ip ();
				$data ['sex'] = 1; 
				//$data ['wechatid'] = $openid;
				$data ['status'] = 1;
				$data ['fatherid'] = get_fid(); 
				
				unset($data['userpwd1'],$data['verify'],$data['imageField']);
				$db = M ( 'member' )->add ( $data );
				if($db){
					
					$rs=array();
					$rs['username']=$data['username'];
					$rs['telephone']=$data['telephone'];
					$rs['sex']=$data['sex'];
					$rs['email']=$data['email'];
					$rs['address']=$data['address']; 
					$rs['userid']=$db; 
					M('address')->add($rs);
					
				}
				//注册完后自动登录
				if($this->login($username, $userpwd,'111111')){
					$to=$data['email'];
					$subject='[waifood]register successfully';
					$body=lbl('tpl_register');
					if(!isN($body)){
					
						$preg="/{(.*)}/iU";
						$n=preg_match_all($preg,$body,$rs);
						$rs=$rs[1];
						if($n>0){
							foreach($rs as $v){
								if(isset($data[$v])){
									$oArr[]='{'.$v.'}';
									$tArr[]=$data[$v];
									$body=str_replace($oArr,$tArr,$body);
								}
							}
						}
						 
						if(send_mail($to,$subject,$body)){
	
						}	
					}
					$this->redirect('Member/index');
				}else{
					$this->error('sorry, user name cannot be empty.');
				}
			}
			 
		}else{

			$title = 'register';
			$keywords = $title.lbl('subtitleshop');
			$description = $title.lbl('subtitleshop');
			$this->assign('title',$title);
			$this->assign('keywords',$keywords);
			$this->assign('description',$description);
			
			$this->display();
		}
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
					$this->login ( $db ['username'], $db ['userpwd'], '' );
				} else {
					$newid = $this->createWechatUser ( $openid );
					$db= M ( 'member' )->where ( $where )->find ($newid);
					if ($db != false) {
						$this->login ( $db ['username'], $db ['userpwd'], '' );
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
		    
			$this->login ( $db ['username'], $db ['userpwd'], '111111' );
			$this->redirect('Settle/cashier');
		}
	}

	public function loginbox($username = null, $userpwd = null ) {
			$data=$this->login ($username,md5($userpwd), '111111' );
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
	 * @param string $verify        	
	 */
	protected function login($username = null, $userpwd = null, $verify = null) {
		if (!(isN($username)||isN($userpwd))) {
			
			if($verify!='111111'){ 
				if (! check_verify ( $verify )) {
				$this->error ( 'sorry,verifycation code is illegal.' );
				}
			}
			
			// 设置id,username, wechatid
			$where = array ();
			$where ['status'] = 1;
			$where ['username'] = $username;
			$where ['userpwd'] = $userpwd;
			$db = M ( 'member' )->where ( $where )->find ();
			if ($db != false) {
				$data = array ();
				$data ['id'] = $db ['id' ] ;
				$data ['lastlogtime'] = time_format ();
				$data ['lastlogip'] = get_client_ip ();
				$data ['logtimes'] = $db ['logtimes'] + 1;
				M ( 'member' )->save ( $data );
				
				//取用户折扣
				$level=M('level')->where('id='.$db['usertype'])->find();
				$rate=$level['rate'];
				if(!is_numeric($rate)){
					$rate=1;
				}
				session ( 'userrate', $rate );
				session ( 'userid', $db ['id'] );
				session ( 'username', $db ['username'] );
				session ( 'usertype', $db ['usertype'] );
				session ( 'wechatid', $db ['wechatid'] );
				return true;
			} else {
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