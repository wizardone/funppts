<?php 
UCMS::load_site('pages/common_site_logged.php');

class profile extends common_site_logged
{	
	public function process() 
	{
		parent::process();
		
	
		$this->assign_copy('page_name', 'User Profile');
		$this->assign('user_data', $this->user->get_data());
	}
	
}
	