<?php
UCMS::load_site('pages/facebook/common.php');

class facebook_index extends facebook_common
{
	
	public function process()
	{
		parent::process();
		$this->add_css('face_style.css');
		$this->add_js('swfobject.js');
		$this->add_js('jquery/jquery-1.2.6.min.js');
		
		
		if($this->get('top') == 'popular')
		{
			$data = $this->get_popular();
			$st = $data[0];
			$pages = $data[1];
		}
		else if($this->get('top') == 'downloaded')
		{
			$data = $this->get_downloads();
			$st = $data[0];
			$pages = $data[1];
		}
		else
		{
			$data = $this->get_newest();
			$st = $data[0];
			$pages = $data[1];
		}
		
		$ids = array();
		$ppts = array();
		
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			$ppt = new LIB_PPT($this, $row);
			$ppts[$ppt->get_obj_id()] = $ppt;
			$ids[] = $ppt->get_obj_id();
		}
		
		$this->assign('ppts', $ppts);
		
	}	
}