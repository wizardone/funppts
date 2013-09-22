<?php 
UCMS::load_site('lib/PPT.php');
UCMS::load_site('pages/common_site.php');

class editfav extends common_site 
{	
	
	public function process()
	{
		parent::process();
		
		if ($this->user->is_logged() && $this->post('obj_id'))
		{
			$id = $this->user->get_id();
			if($this->post('remove'))
			{
				$this->db->delete('favourites', 'fav_user_id = ? AND fav_object_id = ? AND fav_object_type_id = ?', array($id, $this->post('obj_id'), $this->get_object_type_id('ppt')));	
			}
			else 
			{	
				$this->db->insert('favourites', array( 'fav_user_id' => $id, 'fav_object_id' => $this->post('obj_id'), 'fav_object_type_id' => $this->get_object_type_id('ppt')));
			}
		}
	}
	
}

?>
