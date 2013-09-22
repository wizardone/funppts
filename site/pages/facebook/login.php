<?php
UCMS::load_site('pages/facebook/common.php');

class facebook_login extends facebook_common
{
	public function process()
	{
		parent::process();
		$this->use_template('');
		
		$user_id = $this->user->get_id_by_userpass($this->post('user_login'), $this->post('user_pass'));
		
		if($user_id)
		{
			$this->user->set_fb_id($user_id);
		}
		else 
		{
			print $this->lang['facebook_no_user'];
		}
	}
}

?>