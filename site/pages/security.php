<?php

UCMS::load_site('pages/common_site.php');
UCMS::load_cms('images/Security.php');

class security extends common_site
{
	
	function process()
	{
		
		parent::process();
		$image = new UCMS_Images_Security($this->sess);
		$image->show();		
	}
	
}

?>