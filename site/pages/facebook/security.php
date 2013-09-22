<?php

UCMS::load_site('pages/common_site.php');
UCMS::load_cms('images/Security.php');

class facebook_security extends common
{
	
	function process()
	{
		
		parent::process();
		$image = new UCMS_Images_Security($this->sess);
		$image->show();		
	}
	
}

?>