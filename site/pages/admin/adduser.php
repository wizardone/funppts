<?php 

UCMS::load_site('pages/admin/common.php');
UCMS::load_cms('Form.php');
UCMS::load_site('lib/Form.php');

class admin_adduser extends admin_common 
{
	
	public function process()
	{
		parent::process();
		$this->assign_copy('page_name', 'Admin Add User');
		$this->use_template('admin/editexuser.html');
		
		$len = array(3, 20);
		
		$fields = array
		(
			'user_login'			=>	array
			(
				'exists'	=>	false,
				'len'		=>	$len
			),
			'user_pass'				=>	array
			(	
				'len'		=>	$len
			),
			'user_passcheck'		=>	array
			(
				'equals'	=>	'user_pass',
				'len'		=>	$len
			),
			'user_admin'			=>	array
			(
				'required'	=>	false
			),
			'user_active'			=>	array
			(
				'required'	=>	false
			)
		);
		
		$form = new Lib_Form($this, $fields);
		
	
		if ($form->valid())
		{
			
			$libuser = new Lib_User($this->cms, $this);
				
			if ($id = $libuser->register($fields['user_login']['value'], $fields['user_pass']['value'], array
			(	
				'user_admin'		=>	($fields['user_admin']['value'] ? 1 : 0),
				'user_active' 		=>	($fields['user_active']['value'] ? 1 : 0)
			))) 
			{
				$this->page_redirect_post('admin_adduser', array
				(
					'added' => ($fields['user_admin']['value'] ? 1 : 2)
				));
			}
									
		}		
					
	}
	
}

?>