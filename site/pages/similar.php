<?php
UCMS::load_site('pages/common_site.php');
//UCMS::load_site('lib/PPT.php');

class similar extends common_site
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
			$xml .= "<url>".$this->escape($this->page_url_full_f('view', array('id' => $p->get_pimp_url())))."</url>\n";
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
