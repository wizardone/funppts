<?php 

UCMS::load_site('pages/common.php');

abstract class admin_common extends common
{

	public function __construct(UCMS $cms) 
	{
		parent::__construct($cms);
		
		if (!$this->user->is_logged() && $this->page_name != 'admin_login')
		{
			$this->page_redirect('admin_login');
		}
		
	}
	
	protected function init() 
	{
		$this->sess->init($this, true);
		$this->config->init();
		$this->user = new Lib_User($this->cms, $this);
		$this->user->init($this);	
	}
	
	public function process() 
	{
		parent::process();
		$this->assign_copy('menu_adduser', $this->page_url('admin_adduser'));
		$this->assign_copy('menu_edituser', $this->page_url('admin_edituser'));
		$this->assign_copy('menu_editexuser', $this->page_url('admin_editexuser'));
		$this->assign_copy('menu_delexuser', $this->page_url('admin_delexuser'));
		$this->assign_copy('menu_login', $this->page_url('admin_login'));
		$this->assign_copy('menu_logout', $this->page_url('admin_login', array
		(	
			'logout' => 1
		)));
	}
	
} 

?>