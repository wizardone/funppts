<?php
UCMS::load_site('lib/Form.php');
UCMS::load_site('pages/facebook/common.php');

class facebook_view extends facebook_common
{
	public function process()
	{
		parent::process();
		$this->add_css('face_style.css');
		$this->add_js('swfobject.js');
		$this->get_view();
	}
}

?>