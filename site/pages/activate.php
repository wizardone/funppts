<?php 

UCMS::load_site('pages/common_site.php');

class activate extends common_site 
{
	function process()
	{
		parent::process();
		$this->assign_copy('page_name', 'Activate User');
		
		if ($this->get('key')) 
		{
			$hash =& $this->get('key');
			$libuser = new Lib_User($this->cms, $this);
			if ($libuser->activate($hash))
			{
				$this->page_redirect('index', array
				(
					'act'	=>	1
				));				
			}
			
		}
	}
	
}

?>