<?php 

UCMS::load_site('pages/common_site.php');

class common_site_notlogged extends common_site 
{
	
	public function __construct(UCMS $cms)
	{
		parent::__construct($cms);
		
		if ($this->user->is_logged())
		{
			$this->page_redirect_default();
		}
		
	}
}
?>