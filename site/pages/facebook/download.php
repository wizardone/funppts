<?php
UCMS::load_site('pages/facebook/common.php');

class facebook_download extends facebook_common
{
	public function process()
	{
		parent::process();
		$this->add_css('face_style.css');
		$this->get_download_page();
	}
	
}

?>