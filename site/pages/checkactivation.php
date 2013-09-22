<?php 

UCMS::load_site('pages/common_site.php');

class checkactivation extends common_site 
{
	function process()
	{
		parent::process();
		$this->assign_copy('page_name', 'Activation Info');
		
		if ($this->get('reg'))
		{
			$message = 'You have successfully activated your account.';
		}
		else 
		{
			$message = 'You have not successfully activated your account.';
		}
		
		$this->assign('activation_info', $message);
	}
	
}

?>