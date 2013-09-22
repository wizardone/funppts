<?php
UCMS::load_site('lib/UserFacebook.php');
UCMS::load_site('pages/common.php');
UCMS::load_site('facebook/facebook.php');


class facebook_common extends common
{
	protected $fbook;
	
	public function __construct(UCMS $cms)
	{
		parent::__construct($cms);
	}
	
	protected function init() 
	{
		parent::init();
		
		if(!$this->user->is_logged())
		{
			$this->assign_copy('fb_status', 1);		
		}
	}
	
	protected function init_user()
	{
		$this->fbook = new Facebook($this->cms_config->get('facebook_apikey'), $this->cms_config->get('facebook_secret'));
		$this->fbook->require_frame();
		$user_id = $this->fbook->require_login();
		
		$this->user = new Lib_UserFacebook($this->cms, $this, $this->fbook, $user_id);
		
		$this->user->init($this);
		
	}
	
}