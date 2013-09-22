<?php
UCMS::load_site('pages/facebook/common.php');

class facebook_register extends facebook_common
{
	public function process()
	{
		parent::process();
		$this->use_template('');
		
		$result = $this->user->create_face_user();
		
		if($result)
		{
			print implode(',', $result);
		}
		
	}
}


