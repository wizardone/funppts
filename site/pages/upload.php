<?php 

UCMS::load_site('pages/common_site_logged.php');
UCMS::load_site('lib/Form.php');

class upload extends common_site_logged
{
	public function process()
	{
		parent::process();
		
		$id = $this->user->get_id();
		$fields = array('ppt_file' => array('file' => true, 'mime' => array('application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation')), 'ppt_title' => '', 'ppt_description' => '', 'ppt_tags' => '' );
		
		$form = new Lib_Form($this, $fields);
	
		if ($form->valid())
		{
			$ppt = new LIB_PPT($this, 0);
			
			
			$title = $fields['ppt_title']['value'];
			
			$title = str_replace("\r", '', $title);
			$title = str_replace("\n", '', $title);
			$title = str_replace("\0", '', $title);
			
			$name = $title;
			$name = $this->generate_ppt_url($name, $add_id);
			
			$ppt->register(array('ppt_title' => $title, 'ppt_description' => $fields['ppt_description']['value']), $id, $fields['ppt_file']['value'], $fields['ppt_tags']['value'], $name, $add_id);
			
			$this->page_redirect_post('index', array('my' => 1, 'uploaded' => 1));
		}
	}
}

?>