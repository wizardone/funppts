<?php 

UCMS::load_site('pages/common.php');

abstract class common_site extends common 
{
    protected $login_enabled;
    
	public function process() 
	{
		parent::process();
		
		$this->login_enabled = $this->cms_config->get('login_enabled');
		$this->assign('login_enabled', $this->login_enabled);
		
		$this->set_tags();
		$this->set_login_url();
		$this->add_js('jquery/jquery-1.2.6.min.js');
		$this->add_js('jquery/ifixpng.js');
		$this->add_js('swfobject.js');
		$this->add_css('style.css');
		
		$this->assign('url_upload', $this->page_url_must_login('upload'));
		
		$this->assign('url_download', $this->page_url_must_login('download'));
		
	}
	
	public function check()
	{
		if (!$this->user->is_logged())
		{
			if ($this->get('reg'))
			{	
				$this->status_window('', '<b>Thanks for registering .</b>
										  Please <u>check your email to activate your new account </u>.');
			}
			
			if ($this->get('lost'))
			{	
				$this->status_window('', '<b>You have forgotten password .</b>
										  Please check your email to get a new password.');
			}
			
			if ($this->get('new_pass'))
			{	
				$this->status_window('', '<b>You have successfully changed your password .</b>');
			}
			
			if ($this->get('act'))
			{	
				$this->status_window('', '<b>You have successfully activated your account .</b>');
			}
		}
		else 
		{
			if ($this->get('loggedin'))
			{
				$this->status_window('', '<b>You have successfully logged in.</b>');
			}
			if ($this->get('uploaded'))
			{
				$this->status_window('', '<b>You have successfully uploaded your presentation.<br /> We need some time to convert it so be patient.</b>');
			}
			if ($this->get('shared'))
			{
				$this->status_window('', 'You have successfully shared a presentation');
			}
		}
	}
	
	
	public function status_window($title, $message)
	{
		$this->assign('status_title', $title);
		$this->assign('status_message', $message);
	}
	
	protected function set_login_url() 
	{
		$this->assign('url_login', $this->page_url_return('login', null, $this->secure_controler_url()));
	}
	
	public function set_tags()
	{
		$st = $this->db->select('tags',
		 	'tag_word, tag_count',
		 	'tag_count > ?',
			1,
			'tag_word'
		 );
		 
		 $tags = array();
		 $max_tagcount = null;
		 $min_tagcount = null;
		 
		 while($row = $st->fetch(PDO::FETCH_ASSOC))
		 {
		 	if(is_null($max_tagcount) || $max_tagcount < $row['tag_count'])
		 	{
		 		$max_tagcount = $row['tag_count'];
		 	}
		 	if(is_null($min_tagcount) || $min_tagcount > $row['tag_count'])
		 	{
		 		$min_tagcount = $row['tag_count'];
		 	}
		 	$tags[] = $row;
		 }
		 
		$this->assign('popular_tags', $tags);
		$this->assign('max_tagcount', $max_tagcount);
		$this->assign('min_tagcount', $min_tagcount);
		$this->assign_copy('tag_countdiff', $max_tagcount - $min_tagcount);
	}
	
	
	
}
?>