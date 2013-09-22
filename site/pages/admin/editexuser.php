<?php 

UCMS::load_site('pages/admin/common.php');
UCMS::load_site('lib/User.php');
UCMS::load_cms('Form.php');
UCMS::load_site('lib/Form.php');

class admin_editexuser extends admin_common 
{
	
	public function process() 
	{
		parent::process(); 	
		$this->assign_copy('page_name', 'Admin Edit Selected User');
		
		if (!isset($_GET['id'])) 
		{
			$this->page_redirect('admin_edituser');
		}
		else 
		{
			$len = array(3, 20);
			
			$fields = array
			(	
				'user_login'			=>	array
				(
					'len'		=> $len,
					'exists'	=> false
				),
				'user_pass'				=>	array
				(	
					'required'	=> false,
				),
				'user_passcheck'		=>	array
				(
					'required'	=> false,
					'equals'	=> 'user_pass',
				),
				'user_admin'			=>	array
				(
					'required'	=> false
				),
				'user_active'			=>	array
				(
					'required'	=> false
				)
			);
	
			$form = new Lib_Form($this, $fields);
			
			if ($form->valid())
			{
				
				$libuser = new Lib_User($this->cms, $this);
				$libuser->init($this, $_GET['id'], UCMS_Users_Simple::NO_LOGIN | UCMS_Users_Simple::NO_DATA);
				$libuser->update_field('user_login', $fields['user_login']['value']);
				
				if ($fields['user_pass']['value'])
				{
					$libuser->update_password($fields['user_pass']['value']);
				}
			
				$libuser->update_field('user_admin', $fields['user_admin']['value'] ? 1 : 0);
				$libuser->update_field('user_active', $fields['user_active']['value'] ? 1 : 0);
				$this->page_redirect_post('admin_edituser');
				
					
			}
			else 
			{
				$libuser = new Lib_User($this->cms, $this);
				$userdata = $libuser->get_user_data($_GET['id']); 
				$form->set_values($userdata);
			}
		}	
	}
	
}

?>