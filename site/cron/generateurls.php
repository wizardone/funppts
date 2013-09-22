<?php 
UCMS::load_cms('cron/Simple.php');

class generateurls extends UCMS_Cron_Simple
{

	public function process()
	{
		parent::process();

		$st = $this->db->select('ppts', 'ppt_id, ppt_title');
		
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			$name = $this->generate_ppt_url($row['ppt_title'], $add_id);
			
			if($add_id)
			{
				$name .= '_'.$row['ppt_id'];
			}
			
			$this->db->update('ppts', array('ppt_url' => $name), 'ppt_id = ?', $row['ppt_id'] );
		}
	}
	
	protected function generate_ppt_url($name, &$add_id)
	{
		$name = strtolower($name);
		$name = preg_replace('/[\s~!@#$%\^&*()+"\':;\/\\?=|\]\[{}<>`]/', '_', $name);
		
		$name = preg_replace('/[_]{2,}/', '', $name);
		$name = preg_replace('/^[_]/', '', $name);
		$name = preg_replace('/[_]$/', '', $name);
		
		$st = $this->db->select('ppts', '1', 'ppt_url = ?', $name);
		
		if($st->fetch(PDO::FETCH_ASSOC))
		{
			$add_id = true;
		}
		else 
		{
			$add_id = false;
		}
		
		return $name;
	}
	
}
