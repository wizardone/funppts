<?php
UCMS::load_site('pages/common_site.php');
//UCMS::load_site('lib/PPT.php');

	class tags extends common_site
	{
		public function process()
		{
			parent::process();
			
			$st = $this->db->select('tags', 'tag_word, tag_count', 'tag_count > ?', 0, 'tag_word');
			
			$all_tags = array();
			
			while($row = $st->fetch(PDO::FETCH_ASSOC))
			{
				
				$all_tags[] = $row;
			}
			
			$this->assign('all_tags', $all_tags);
		}	
			
		
			
		
		
	}


?>