<?php
UCMS::load_site('pages/common_site.php');
//UCMS::load_site('lib/PPT.php');

class download extends common_site
{
	public function process()
	{
		parent::process();
		$this->get_download_page();
	}
	
}

?>