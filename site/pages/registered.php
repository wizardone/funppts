<?php 

UCMS::load_site('pages/common.php');

class registered extends common 
{
	
	function process()
	{
		parent::process();
		$this->assign_copy('page_name', 'User Registration Info');
	}
	
}