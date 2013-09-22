<?php 

UCMS::load_site('pages/common_site_notlogged.php');
UCMS::load_cms('Form.php');
UCMS::load_site('lib/Form.php');

class lostpass extends common_site_notlogged 
{
	
	public function process() 
	{	
		$fields = array
		(
			'user_email'			=>	array
			(
				'mail'	 => true,
				'exists' => true
			),
			'user_security'			=>	array
			(
				'code'	=> true
			)
		);
		
		$form = new Lib_Form($this, $fields);
		
		if ($form->valid())
		{

			$libuser = new Lib_User($this->cms, $this);
			
			if ($hash_id = $libuser->get_lost_pass_hash($fields['user_email']['value'], 'user_email'))
			{
				
				UCMS::load_cms('Mail.php');
				$mail = new UCMS_Mail($this->cms, $this->config);
				
				$mail->ContentType = 'text/html';
				$mail->From = 'lostpassword@funppsfun.com';
				$mail->FromName = 'Fun, PPS!Fun!';
				$mail->Subject = 'Fun, PPS!Fun! lost password';
				
				$message = 'Hi, <br /><br /> You have forgotten your password.<br /><br />';
				$message .= 'Click on the following link to change your password.<br />';
				
				$url = $this->page_url_full('lostpasschange', array
				(
					'key'	=>	$hash_id 
				), true);
				
				$message .= '<a href="' . $url . '">' . $url . '</a>';
				
				$mail->Body =& $message;
				$mail->AddAddress($fields['user_email']['value']);
				$mail->Send();
				
				$this->page_redirect_post('index', array
				(
					'lost'	=>	1
				));	
				
				
			}
			else
			{
				
				$form->set_global_error('lostpass');
			}
				
		}
		$this->assign_copy('user_security_image', $this->page_url('security'));
		$this->assign_copy('page_name', 'Forgotten password');
		parent::process();
					
	}
	
	
}

?>