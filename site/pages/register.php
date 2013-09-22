<?php 

UCMS::load_site('pages/common_site.php');
UCMS::load_site('lib/Form.php');

class register extends common_site
{
	
	function process()
	{
		parent::process();
		$this->assign_copy('page_name', 'Register User');
		
		$len = array(3, 20);
		
		$fields = array
		(
			
			'user_login'				=>		array
			(
				'len'		=> 	$len,
				'exists'	=>	false
			),
			'user_email'				=>		array
			(
				'mail'		=>	true,
				'exists'	=>	false
			),
			'user_pass'					=>		array
			(
				'len'		=> 	$len
			),
			'user_passcheck'			=>		array
			(
				'len'		=> 	$len,
				'equals'	=>	'user_pass'
			),
			'user_security'				=>		array
			(
				'code'		=>	true
			)
		);
		
		$form = new Lib_Form($this, $fields);
		
		if ($form->valid())
		{
			
			$libuser = new Lib_User($this->cms, $this);
			
			if ($hash = $libuser->register_activate($fields['user_login']['value'], $fields['user_pass']['value'], array
				(	
					'user_email'	=>	$fields['user_email']['value']
				)))
			{
				UCMS::load_cms('Mail.php');
				$mail = new UCMS_Mail($this->cms, $this->config);
				
				$mail->ContentType = 'text/html';
				$mail->From = 'registration@funpowerpoints.com';
				$mail->FromName = 'Fun Powerpoints';
				$mail->Subject = 'Fun Powerpoints registration';
				
				
				
				$message = 'Hi, <br /><br /> Your Registration was successful.<br /><br />';
				$message .= 'Click on the following link to activate your account.<br />';
				
				$url = $this->page_url_full('activate', array
				(
					'key'		=>	$hash
				), true);
				
				
				$message .= '<a href="' . $url . '">' . $url . '</a>';
				
				$mail->Body =& $message;
				$mail->AddAddress($fields['user_email']['value']);
				$mail->Send();
				
				$this->page_redirect_post('index', array
				(
					'reg'	=>	1
				));
			}
			else 
			{
				$form->set_global_error('register');
			}
			
		}
		
		$this->assign_copy('user_security_image', $this->page_url('security'));
		
	}
	
}

?>