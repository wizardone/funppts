<?php 

UCMS::load_site('pages/admin/common.php');

class admin_edituser extends admin_common 
{
	public function process() 
	{
		parent::process(); 	
		$this->assign_copy('page_name', 'Admin Edit Menu');
		
		if (isset($_GET['del']))
		{
			$row = $this->db->select_hashrow('users', 'COUNT(*) AS cnt');
			
			if ($row['cnt'] > 1)
			{
				$this->db->delete('users', 'user_id = ?', $_GET['del']);
			}			
		}
	
		$stmt = $this->db->select('users', '*');
		$users = array();	
		
		while ($row = $stmt->fetch()) 
		{
			$users[] = $row;
		}
		
		$this->assign('users', $users);			
	}
	
}

?>