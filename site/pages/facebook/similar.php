<?php
UCMS::load_site('pages/facebook/common.php');

class facebook_similar extends facebook_common
{
	public function process()
	{
		parent::process();
		$this->use_template('');
		
		$id = $this->get('id');
		if(!$id) die('No id received');
		
		$ppt = new LIB_PPT($this, $id);
		
		header('Content-Type: text/xml');
		$xml  = "<?xml version=\"1.0\"?>\n";
		$xml .= "<presentations>\n";
		
		if(!$ppt->get_obj_id())
		{	
			$xml .= "</presentations>";
			echo $xml;
			
			return false;
		}
		
		$presentations = $ppt->get_similar();
		
		foreach($presentations as $p)
		{
			$xml .= "<presentation>\n";
			$xml .= "<title>".$this->escape($p->get_title())."</title>\n";
			$xml .= "<url>".$this->escape($this->page_url_this_custom('view', array('id' => $p->get_obj_id()), $this->contoler_url_full))."</url>\n";
			$xml .= "<thumb>http://funppsfun.com".$this->escape($p->get_pic_url())."</thumb>\n";
			$xml .= "<description>".$this->escape($p->get_desc())."</description>\n";
			$xml .= "</presentation>\n";
		}
		$xml .= "</presentations>";
		
		echo $xml;
	}
	
	private function escape($str)
	{
		return htmlentities($str, ENT_QUOTES, 'UTF-8');
	}
}
