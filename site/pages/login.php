<?php 

UCMS::load_site('pages/common_site.php');
UCMS::load_cms('Form.php');
UCMS::load_site('lib/Form.php');

class login extends common_site
{
	
	public function __construct(UCMS $cms) 
	{
		parent::__construct($cms);
		
		if ($this->user->is_logged())
		{
			if ($this->get('logout'))
			{
				$this->user->logout($this);
			}	
		
			$this->redirect($this->page_url_get_return());
		}
	}
	
	public function process()
	{
		$len = array(3, 20);
		
		$fields = array
		(
			'user_login'	=>	array
			(
				'len'		=> 	array(3, 20),
			),
			'user_pass'		=>	array
			(	
				'len'		=>	array(3, 20)
			),
			'remember'		=>	array
			(
				'required'	=>	false
			)
		);
		
		$form = new Lib_Form($this, $fields);
		
		
		if ($form->valid())
		{
			if ($this->user->login($this, $fields['user_login']['value'], $fields['user_pass']['value'], isset($fields['remember']['value'])))
			{
				$this->redirect_post($this->page_url_get_return(), array
				(
					'loggedin'	=>	1
				));
			
			}
			else 
			{
				$form->set_global_error('login');
			}
		
		}
		
		parent::process();
		
		$this->assign_copy('page_name', 'User Login');
		
		if($this->get('rp') == 'download')
		{
			$this->assign_copy('download', 1);
		}
		
		if($this->get('rp') == 'upload')
		{
			$this->assign_copy('upload', 1);
		}
		
	}
	
	protected function set_login_url() 
	{
		$this->assign('url_login', $this->page_url_keep_return('login'));
	}
	
}

?>