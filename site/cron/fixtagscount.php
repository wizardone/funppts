<?php 
UCMS::load_cms('cron/Simple.php');

class fixtagscount extends UCMS_Cron_Simple
{

	public function process()
	{
		parent::process();

		$st = $this->db->select('tags', 'tag_id');
		
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			$row2 = $this->db->select_numrow('tags_refs', 'COUNT(*)', 'ref_word_id = ?', $row['tag_id']);
			$this->db->update('tags', array('tag_count' => $row2[0]), 'tag_id = ?', $row['tag_id']);
		}
	}
	
}
