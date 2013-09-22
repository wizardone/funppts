<?php 

UCMS::load_site('pages/admin/common.php');
UCMS::load_cms('Form.php');
UCMS::load_site('lib/Form.php');

class admin_login extends admin_common
{
	
	public function __construct(UCMS $cms) 
	{
		parent::__construct($cms);
		
		if ($this->user->is_logged())
		{
			if (isset($_GET['logout']))
			{
				$this->user->logout($this);
				$this->page_redirect('admin_index', null, null, true);
			}	
			else 
			{
				$this->page_redirect('admin_index');
			}
		}			
	}
	
	public function process()
	{
		parent::process();
		$this->assign_copy('page_name', 'Admin Login');
		
		$len = array(3, 20);
		$fields = array
		(	
			'user_login'	=>	array
			(
				'exists'	=> true,
				'len'		=> $len
			),
			'user_pass'		=>	array
			(	
				'len'		=> $len
			),
		);
		
		$form = new Lib_Form($this, $fields);
		
		if ($form->valid())
		{
		
			if ($this->user->login($this, $fields['user_login']['value'], $fields['user_pass']['value'], false))
			{
				$this->page_redirect_post('admin_index');
			}
		}

	}
	
}

?>