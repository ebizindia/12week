<?php
$page='login';
require_once 'inc.php';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$template_type='login';
$page_title='Login'.CONST_TITLE_AFX;
$page_description='Backend Login Screen';

$body_template_file=CONST_THEMES_TEMPLATE_INCLUDE_PATH.'login.tpl';
$body_template_data=array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);

$additional_base_template_data=array(
		'page_title'=>$page_title,
		'page_description'=>$page_description,
		'template_type'=>$template_type,
		'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page),
			'other_js_code'=>"var testvar=\"Testing\";\n"
);

$navbar_template_data=array('navbar'=>array(
	'template_type'=>$template_type
    )
);

if(isset($_POST['mode']) && $_POST['mode']=="login"){
	// CSRF Protection
	if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		$response = array();
		$response['msg'] = 'Security token mismatch. Please refresh the page and try again.';
		$response['errorcode'] = 1;
		// Debug info (remove in production)
		$response['debug'] = [
			'post_token_exists' => isset($_POST['csrf_token']),
			'session_token_exists' => isset($_SESSION['csrf_token']),
			'post_token' => $_POST['csrf_token'] ?? 'not set',
			'session_token' => $_SESSION['csrf_token'] ?? 'not set'
		];
		echo json_encode($response);
		exit;
	}

	$tm=time();
	$data=array();
	$data['account']=$_POST['login_username'];
	$data['login_datetime']=date('Y-m-d H:i:s',$tm);
	$data['login_from']=$_SERVER['REMOTE_ADDR'];

	$response=array();

	// Input validation
	$username = trim($_POST['login_username'] ?? '');
	$password = $_POST['login_password'] ?? '';

	if(empty($username)){
		$response['msg']='Please provide your email address.';
		$response['errorcode']=2;
		$data['login_status']='0';
		$data['reason']='Enter Email ID';

	} elseif(!filter_var($username, FILTER_VALIDATE_EMAIL)){
		$response['msg']='Please provide a valid email address.';
		$response['errorcode']=2;
		$data['login_status']='0';
		$data['reason']='Invalid Email Format';

	} elseif(empty($password)){
		$response['msg']='Please provide the password.';
		$response['errorcode']=3;
		$data['login_status']='0';
		$data['reason']='Enter password';

	} else {
		if($_SESSION['s_started'] != 1)
			$_SESSION['s_started'] =1;
		
		$res=$usercls->login($username,$password,$tm,$_POST['login_remember'],CONST_APP_PATH_FROM_ROOT, CONST_RESTRICTED_TO_ROLES);
		$data['login_as'] = $res[1]['login_as']??'';
		$data['user_id']=(isset($res[1]['user_id']))?$res[1]['user_id']:'';
		
		if($res[0]===true){
			// Regenerate session ID after successful login
			session_regenerate_id(true);
			
			$data['login_status']='1';
			$data['reason']='';
			$_SESSION['userobj']=serialize($usercls);
			
			$response['msg']='Login successful. Redirecting... ';
			$response['errorcode']=0;

			$response['location']=CONST_APP_ABSURL;
			if(!empty($_POST['referurl'])){
				$url_parts = parse_url($_POST['referurl']);
				// Validate redirect URL to prevent open redirect attacks
				if(isset($url_parts['host']) && $url_parts['host'] !== $_SERVER['HTTP_HOST']){
					// External redirect not allowed
					$response['location']=CONST_APP_ABSURL;
				} elseif(!in_array($url_parts['path'], ['/', '/index.php'])) {
					$response['location']=$_POST['referurl']; 
				}
			}


		} else {
			$data['login_status']='0';
			switch($res[1]['errorcode']){
				case 1: 
					$data['reason']='DB Error';
					$response['msg']='Server error. Please try again later.';
					break;
				case 2: 
				case 3: 
					// Use consistent error message to prevent username enumeration
					$data['reason']='Invalid credentials';
					$response['msg']='Invalid email address or password.';
					break;
				case 4: 
				case 7:
					$data['reason']='Account suspended';
					$response['msg']='Your account has been suspended. Please contact support.';
					break;
				case 5: 
				case 6:
					$data['reason']='Authentication error';
					$response['msg']='Authentication failed. Please try again.';
					break;
				case 8: 
					$data['reason']='Access forbidden';
					$response['msg']='Access is temporarily forbidden.';
					break;
				default: 
					$data['reason']='Unknown error';
					$response['msg']='Login failed. Please try again.';
			}

			$response['errorcode']=4;
			// Don't echo back the username to prevent information leakage
		}


	}
	$usercls->saveLoginData($data);
	echo json_encode($response);
	exit;

}elseif(isset($_GET['mode']) && $_GET['mode']=="logout"  || ( isset($_COOKIE['expired']) && $_COOKIE['expired']==1)){
	
	if($_COOKIE['expired']==1){
		$logout_resp=array('errorcode'=>1,'msg'=>'Your session has expired due to inactivity.');
	}else{
		$logout_resp=array('errorcode'=>0,'msg'=>'You have successfully logged out.');
	}
	
	\eBizIndia\logoutSession();

	$additional_base_template_data['dom_ready_code'] .="login_funcs.showLogoutResult(".json_encode($logout_resp).");";


}elseif(isset($_POST['mode']) && $_POST['mode']=='sendpasswordresetlink'){
	// CSRF Protection for password reset
	if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		$response = array();
		$response['errorcode'] = 1;
		$response['msg'] = 'Security token mismatch. Please refresh the page and try again.';
		echo json_encode($response);
		exit;
	}

	$tm=time();
	$curr_datetime=date('Y-m-d H:i:s');
	$response['errorcode']=0; // no error
	$response['msg']=''; // no error

	$email_id=trim($_POST['pretrieve_email'] ?? '');
	
	// Better email validation
	if(empty($email_id) || !filter_var($email_id, FILTER_VALIDATE_EMAIL)){
		$response['errorcode']=1; // invalid email id
		$response['msg']='Please provide a valid email address.';
	}else{

		$options=[];
		$options['filters']=[];
		$options['filters'][]=array('field'=>'username','type'=>'EQUAL','value'=>$email_id);
		$options['filters'][]=array('field'=>'status','type'=>'EQUAL','value'=>'1');
		$options['fieldstofetch']=['id','profile_type','pswdResetRequestedOn'];
		$res=$usercls->getList($options);

		if($res===false){
			$response['errorcode']=2; // DB error
			$response['msg']='Internal server error.';
		}elseif(empty($res)){
			$response['errorcode']=3;
			// Use generic message to prevent email enumeration
			$response['msg']='If this email address is registered with us, you will receive a password reset link shortly.';
		}else{
			if($res[0]['pswdResetRequestedOn']=='' || ($tm-strtotime($res[0]['pswdResetRequestedOn']))>CONST_PSWD_RESET_REQ_INTERVAL_IN_SEC){
				$profile_details = $usercls->getProfileForUser($res[0]['id'], $res[0]['profile_type']);
				if($profile_details===false){
					$response['errorcode']=8; // DB error
					$response['msg']='Internal server error.';
				}else if(empty($profile_details)){
					$response['errorcode']=9;
					// Use generic message to prevent email enumeration
					$response['msg']='If this email address is registered with us, you will receive a password reset link shortly.';
				}else if($profile_details[0]['active']!='y'){
					// connected profile apparently deactivated
					$response['errorcode']=10;
					// Use generic message to prevent email enumeration
					$response['msg']='If this email address is registered with us, you will receive a password reset link shortly.';
				}else{
					$data['pswdResetRequestedOn']=$curr_datetime;
					$conn = \eBizIndia\PDOConn::getInstance();
					$conn->beginTransaction();
					$user_ids = array();
					foreach($res as $u){
						$user_ids[] = $u['id'];
					}
					if($usercls->saveUserDetails($data,$user_ids)){

						$use_servers_default_smtp=(defined('CONST_USE_SERVERS_DEFAULT_SMTP'))? CONST_USE_SERVERS_DEFAULT_SMTP:1;
						$hash_of='resetpassword' . $email_id . $curr_datetime;
						$hash=hash(CONST_HASH_FUNCTION,$hash_of.CONST_SECRET_ACCESS_KEY);

						$pswd_reset_url = CONST_APP_ABSURL."/login.php?mode=resetpassword&e=".urlencode($email_id)."&k=$hash";
						$subject = CONST_MAIL_SUBJECT_PREFIX. ' Reset your password';
						$html_msg="<p>Hello,</p><p>You or someone else has submitted a password reset request for your account.</p><p>If you wish to reset your password please visit the link given below:<br><br><a href=\"".$pswd_reset_url."\" >".\eBizIndia\_esc($pswd_reset_url, true)."</a></p><p>Regards<br>".CONST_MAIL_SENDERS_NAME."</p>";
						$text_msg='To view the message, please use an HTML compatible email viewer!';
						
						$extra_data = [];
						$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
						$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;
						
						// if(!empty(CONST_EMAIL_OVERRIDE))
						// 	$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
						// else{
						// 	$extra_data['recp'] = array($email_id);
						// }
						$extra_data['recp'] = array($email_id);

						$data = [
							'subject' => $subject,
							'html_message' => $html_msg,
							'text_message' => $text_msg,
						];

						$mail = new \eBizIndia\Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
						$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
						if(!$mail->sendEmail($data, $extra_data)){
							$response['errorcode']=7; // Mail sending failed
							$response['msg']='Internal server error.';
						}else{
							$response['errorcode']=0; // no error
							$response['msg']='The password reset link has been mailed to your email address. Visit the link to reset your password.';
							$conn->commit();
						}
						
					}else{
						$conn->rollBack();
						$response['errorcode']=4; // DB error
						$response['msg']='Internal server error.';
					}

				}


			}else{
				$response['errorcode']=5; // just requested as password reset
				$response['msg']='You have just requested a password reset. Try again after sometime.';
			}
		}



	}

	echo json_encode($response);
	exit;

}elseif(isset($_GET['mode']) && $_GET['mode']=='activate'){
	$tm=time();
	$response=array('errorcode'=>0,'msg'=>'');
	$email=($_GET['e']);
	$key=trim($_GET['k']);

	$response['get']=$_GET;
	$response['email_id']=$email;
	$response['k']=$key;

	$options=[];
	$options['filters']=[];
	$options['filters'][]=array('field'=>'email','type'=>'EQUAL','value'=>$email);
	$options['filters'][]=array('field'=>'status','type'=>'EQUAL','value'=>'1');


	$res=$usercls->getList($options);

	if($res===false){
		$response['errorcode']=1; // DB error
		$response['msg']="The account activation process failed due to internal error"; // DB error

	}elseif(empty($res)){
		$response['errorcode']=2; // error
		$response['msg']="The account does not exist or has been suspended for administrative reasons."; //email id does not exist

	}else{
		$response['res']=$res[0];
		$hash_of='activate' . $email ;
		$hash=hash(CONST_HASH_FUNCTION,$hash_of.CONST_SECRET_ACCESS_KEY);
		$response['hash']=$hash;
		if($hash!=$key){
			$response['errorcode']=3; // error
			$response['msg']="The account activation process failed. Please use a valid password reset link."; //hash did not match
		}elseif($res[0]['activated']==1){
			$response['flag']=4; // error
			$response['msg']="The account activation process failed. The activation link is not valid any more."; //hash does not exist
		}
	}

	$additional_base_template_data['dom_ready_code'] .="login_funcs.showActivationForm(".json_encode($response).");\n";

}elseif(isset($_POST['mode']) && $_POST['mode']=='setpassword'){
	// CSRF Protection for password setting
	if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		$response = array();
		$response['errorcode'] = 1;
		$response['msg'] = 'Security token mismatch. Please refresh the page and try again.';
		echo json_encode($response);
		exit;
	}

	$response=array('errorcode'=>0,'msg'=>'');
	$email = trim($_POST['resetpswd_email_id'] ?? '');
	$key = trim($_POST['resetpswd_key'] ?? '');
	$new_password = $_POST['resetpswd_password'] ?? '';
	$new_passwordre = $_POST['resetpswd_passwordre'] ?? '';

	// Don't echo back email for security
	if(empty($new_password)){
		$response['errorcode']=6;
		$response['msg']="Please enter the new password you wish to set.";
	}else if(empty($new_passwordre)){
		$response['errorcode']=61;
		$response['msg']="Please re-enter the new password to confirm.";
	}else if(strlen($new_password) < 8){
		$response['errorcode']=62;
		$response['msg']="Password must be at least 8 characters long.";
	}else if($new_password === $new_passwordre){

		$options=[];
		$options['filters']=[];
		$options['filters'][]=array('field'=>'email','type'=>'EQUAL','value'=>$email);
		$options['filters'][]=array('field'=>'status','type'=>'EQUAL','value'=>'1');
		$res=$usercls->getList($options);

		if($res===false){
			$response['errorcode']=1; // error
			$response['msg']="The password could not be set due to internal error"; // DB error

		}elseif(empty($res)){
			$response['errorcode']=2; // error
			$response['msg']="Password could not be set as the account for this email address does not exist."; //email id does not exist

		}else{

			$hashof='activate' . $email;// . $res[0]['pswdResetRequestedOn']; // here pswdResetRequestedOn is apparently not required so commented out

			$hash=hash(CONST_HASH_FUNCTION,$hashof.CONST_SECRET_ACCESS_KEY);
			if($hash!=$key){
				$response['errorcode']=3; // error
				$response['msg']="The account activation process failed."; //hash does not exist

			}else{
				// set the new password in the DB
				$data['password_gen_key'] = $res[0]['password_gen_key'];
				$data['password']=$hash=hash(CONST_HASH_FUNCTION,$new_password.$data['password_gen_key']);  //md5($_POST['pswd_new']);
				$data['pswdResetRequestedOn']='';
				$data['activated']=1;
				$user_ids = array();
				foreach($res as $u){
					$user_ids[] = $u['id'];
				}
				if(count($user_ids)==0){
					$response['errorcode']=8; // successful
					$response['msg']="Invalid account activation link."; //hash does not exist
				}else if(false===$usercls->saveUserDetails($data,$user_ids)){
					$response['errorcode']=4; // error
					$response['msg']="The account activation could not be completed due to DB error."; //hash does not exist
				}else{
					$response['errorcode']=0; // successful
					$response['msg']="The account was activated successfully."; //hash does not exist
				}

			}



		}
	}else{
		$response['errorcode']=5; // error
		$response['msg']="The given passwords do not match.";
	}

	echo json_encode($response);
	exit;

}elseif(isset($_GET['mode']) && $_GET['mode']=='resetpassword'){
	$tm=time();
	$response=array('errorcode'=>0,'msg'=>'');
	$email=($_GET['e']);
	$key=trim($_GET['k']);

	$response['get']=$_GET;
	$response['email_id']=$email;
	$response['k']=$key;

	$options=[];
	$options['filters']=[];
	$options['filters'][]=array('field'=>'username','type'=>'EQUAL','value'=>$email);
	$options['filters'][]=array('field'=>'status','type'=>'EQUAL','value'=>'1');

	$res=$usercls->getList($options);

	if($res===false){
		$response['errorcode']=1; // DB error
		$response['msg']="The password reset process failed due to internal error"; // DB error

	}elseif(empty($res) ){
		$response['errorcode']=2; // error
		$response['msg']="The account does not exist or has been suspended for administrative reasons."; //email id does not exist

	}else{
		$response['res']=$res[0];
		$hash_of='resetpassword' . $email . $res[0]['pswdResetRequestedOn'];
		$hash=hash(CONST_HASH_FUNCTION,$hash_of.CONST_SECRET_ACCESS_KEY);
		$response['hash']=$hash;
		if($hash!=$key){
			$response['errorcode']=3; // error
			$response['msg']="The password reset process failed. Please use a valid password reset link."; //hash did not match

		}elseif($tm-strtotime($res[0]['pswdResetRequestedOn'])>CONST_PSWD_RESET_LINK_VALIDITY_IN_SEC){
			$response['errorcode']=4; // error
			$response['msg']="The password reset process failed. The password reset link is not valid any more."; //hash does not exist

		}else{
			$profile_details = $usercls->getProfileForUser($res[0]['id'], $res[0]['profile_type']);
			if($profile_details===false){
				$response['errorcode']=8; // DB error
				$response['msg']='The password reset process failed due to internal error.';
			}else if(empty($profile_details)){
				$response['errorcode']=9;
				$response['msg']='The account does not exist or has been suspended for administrative reasons.';
			}else if($profile_details[0]['active']!='y'){
				// connected profile apparently deactivated
				$response['errorcode']=10;
				$response['msg']='The account has been suspended for administrative reasons.';
			}
		}

	}

	$additional_base_template_data['dom_ready_code'] .="login_funcs.showPasswordResetForm(".json_encode($response).");\n";

}elseif(isset($_POST['mode']) && $_POST['mode']=='setnewpassword'){
	// CSRF Protection for new password setting
	if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		$response = array();
		$response['errorcode'] = 1;
		$response['msg'] = 'Security token mismatch. Please refresh the page and try again.';
		echo json_encode($response);
		exit;
	}

	$response=array('errorcode'=>0,'msg'=>'');
	$email = trim($_POST['resetpswd_email_id'] ?? '');
	$key = trim($_POST['resetpswd_key'] ?? '');
	$new_password = $_POST['resetpswd_password'] ?? '';
	$new_passwordre = $_POST['resetpswd_passwordre'] ?? '';

	// Don't echo back email for security
	if(empty($new_password)){
		$response['errorcode']=6;
		$response['msg']="Please enter the new password you wish to set.";
	}else if(empty($new_passwordre)){
		$response['errorcode']=61;
		$response['msg']="Please re-enter the new password to confirm.";
	}else if(strlen($new_password) < 8){
		$response['errorcode']=62;
		$response['msg']="Password must be at least 8 characters long.";
	}elseif($new_password === $new_passwordre){

		$options=[];
		$options['filters']=[];
		$options['filters'][]=array('field'=>'username','type'=>'EQUAL','value'=>$email);
		$options['filters'][]=array('field'=>'status','type'=>'EQUAL','value'=>'1');
		$res=$usercls->getList($options);

		if($res===false){
			$response['errorcode']=1; // error
			$response['msg']="The password could not be set due to internal error"; // DB error

		}elseif(empty($res)){
			$response['errorcode']=2; // error
			$response['msg']="Password could not be set as the account for this email address does not exist."; //email id does not exist

		}else{
			$hashof='resetpassword' . $email . $res[0]['pswdResetRequestedOn'];
			$hash=hash(CONST_HASH_FUNCTION,$hashof.CONST_SECRET_ACCESS_KEY);
			if($hash!=$key){
				$response['errorcode']=3; // error
				$response['msg']="The password reset process failed."; //hash does not exist

			}else{
				
				// set the new password in the DB
				$data['password']=\password_hash($new_password, PASSWORD_BCRYPT);
				$data['pswdResetRequestedOn']='';
				$user_ids = array();
				foreach($res as $u){
					$user_ids[] = $u['id'];
				}
				if(count($user_ids)==0){
					$response['errorcode']=8; // successful
					$response['msg']="Invalid password reset link."; //hash does not exist
				}else if(!$usercls->saveUserDetails($data,$user_ids)){
					$response['errorcode']=4; // error
					$response['msg']="The new password could not be set due to DB error."; //hash does not exist
				}else{
					$response['errorcode']=0; // successful
					$response['msg']="The new password was set successfully."; //hash does not exist
				}
			}
		}
	}else{
		$response['errorcode']=5; // error
		$response['msg']="The given passwords do not match.";
	}


	echo json_encode($response);
	exit;

}

if($_GET['mode']=='logout' || $_GET['mode']=='')
	$additional_base_template_data['dom_ready_code'] .="show_box('login-box');";
$additional_body_template_data=array(
						'referurl'=>$_GET['referurl'] ?? '',
						'is_remember_me'=>(isset($_COOKIE['is_remember_me']) && $_COOKIE['is_remember_me']==1 )?1:0,
						'root_uri' => CONST_FRONTEND_ROOT_URI,
						'csrf_token' => $_SESSION['csrf_token'] ?? '', // Add CSRF token to template
					);
$page_renderer->updateBodyTemplateData($additional_body_template_data);
$page_renderer->updateBodyTemplateData($navbar_template_data);
$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();
?>
