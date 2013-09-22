<?php 

UCMS::load_site('pages/common_site.php');

class common_site_logged extends common_site 
{
	public function __construct(UCMS $cms)
	{
		parent::__construct($cms);
		if (!$this->user->is_logged())
		{
			$this->redirect($this->page_url_must_login($this->page_name, $this->get_page_params()));	
		}	
		
	}
	
}
?>