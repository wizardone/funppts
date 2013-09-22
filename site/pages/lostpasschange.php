<?php 

UCMS::load_site('pages/common_site_notlogged.php');
UCMS::load_cms('Form.php');
UCMS::load_site('lib/Form.php');

class lostpasschange extends common_site_notlogged 
{

	private $user_id;
	
	public function __construct(UCMS $cms)
	{
		parent::__construct($cms);
		
		$hash = & $this->get('key');
		
		if (!$this->user_id = $this->user->lost_pass_hash_valid($hash))	
		{
			$this->page_redirect_default();
		}
		
	}
	
	public function process() 
	{
		$len = array(3, 20);
		
		$fields = array
		(
			'user_pass'			=>	array
			(
				'len'	 => $len, 
			),
			'user_passcheck'	=>	array
			(
				'len'	 => $len,
				'equals' => 'user_pass'
			),
			'user_security'		=>	array
			(
				'code'	 =>	true
			)
		);
		
		$form = new Lib_Form($this, $fields);
		
		if ($form->valid())
		{
			$user = new Lib_User($this->cms, $this);
			$user->init($this, $this->user_id, UCMS_Users_Simple::NO_LOGIN|UCMS_Users_Simple::NO_DATA);					
			$user->update_password($fields['user_pass']['value']);
			$this->page_redirect_post('index', array
			(
				'new_pass'	=>	1
			));
			
		}

		parent::process();
		
		$this->assign_copy('user_security_image', $this->page_url('security'));
		$this->assign_copy('page_name', 'Confirmation of User password');
	}
	
}