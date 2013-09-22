<?php 

UCMS::load_site('pages/admin/common.php');

class admin_index extends admin_common 
{
	
	function process()
	{
		parent::process();
		$this->assign_copy('page_name', 'Admin Index');		
	}	
	
}

?>