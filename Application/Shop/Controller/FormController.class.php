<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller; 
use Common\Model\UserModel;

class FormController extends BaseController {
	   
    
    /**
     * 表单详细
     * @param number $id
     */
    public function view($id=0,$msg=''){
        $user= UserModel::getUserById(get_userid());
    	if(substr($user['username'],0,4)=='user_'){
    		$user['username']='';
    		$user['telephone']='';
    		$user['email']='';
    	}
    	
    	$this->assign('user',$user);
    	
    	$where=array();
    	$where['status']=1; 
    	$db = M ( "formtype" )->where($where)->find ($id);
    	if($db){ 
	    	$this->assign ( "db", $db ); 
	    	$this->assign ( "id", $id ); 
	    	$this->assign ( 'title', $db['name']); 
	    	$this->assign ( 'msg', $msg);
    	}else{
    		$this->error('Sorry, the information you access does not exist!');
    	}
    	$this->display();
    	 
    }

     
    
    public function save(){
    	if (IS_POST) {
			$db = D ( "form" );
			$data = empty ( $data ) ? $_POST : $data;
			if(isN($data['ext1'])){
				$this->error('Sorry, your name cannot be empty!');
			}
			if(isN($data['ext2'])){
				$this->error('Sorry, phone number cannot be empty!');
			}

			if(!is_email($data['ext3'])){
				$this->error('Sorry, email is illegal!');
			}
			if(isN($data['ext5'])){
				$this->error('Sorry, subject cannot be empty!');
			}
			if(isN($data['ext6'])){
				$this->error('Sorry, wish list can not be empty!');
			}
			$data ['addip'] = get_client_ip (); 
			if (false !== $db->add ( $data )) {
				
				//管理员邮件：
				//send_mail();
				$to=C('config.WEB_SITE_COPYRIGHT');
				$subject='[waifood]new wishlist '.get_username(get_userid());

				$html='';
				$html.= "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"> \n";
				$html.= "    <tr class=\"row0\">\n";
				$html.= "      <td width=\"100\" >反馈：</td>\n";
				$html.= "      <td>Wishlist </td>\n";
				$html.= "    </tr>\n";
				$html.= "     <tr class=\"row0\">\n";
				$html.= "      <td>创建时间：</td>\n";
				$html.= "      <td>".time_format()."</td>\n";
				$html.= "    </tr>\n";
				$html.= "     <tr class=\"row0\">\n";
				$html.= "      <td>姓名：</td>\n";
				$html.= "      <td>".$data['ext1']."</td>\n";
				$html.= "    </tr>\n";
				$html.= "    <tr class=\"row0\">\n";
				$html.= "      <td>电话：</td>\n";
				$html.= "      <td>".$data['ext2']."</td>\n";
				$html.= "    </tr>\n";
				$html.= "    <tr class=\"row0\">\n";
				$html.= "      <td>Email：</td>\n";
				$html.= "      <td>".$data['ext3']."</td>\n";
				$html.= "    </tr> \n";
				$html.= "    <tr class=\"row0\">\n";
				$html.= "      <td>主题：</td>\n";
				$html.= "      <td>".$data['ext5']."</td>\n";
				$html.= "    </tr>\n";
				$html.= "    <tr class=\"row0\">\n";
				$html.= "      <td>备注：</td>\n";
				$html.= "      <td>".$data['ext6']."</td>\n";
				$html.= "    </tr>\n";
				$html.= "</table>\n";

				$body=$html; 
				if(send_mail($to,$subject,$body)){
				} 
				
				$this->success ( "Congratulations, submitted successfully!" );
			} else {
				$this->error ( 'Sorry, submission failed!' );
			}
		} else {
			$this->display ();
		}
    
    }
    
    
    
}
?>