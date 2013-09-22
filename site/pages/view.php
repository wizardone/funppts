<?php 
UCMS::load_site('pages/common_site.php');
//UCMS::load_site('lib/PPT.php');
UCMS::load_site('lib/Form.php');

class view extends common_site
{
	public function process()
	{
		parent::process();
		$this->get_view();
	}
}

?>