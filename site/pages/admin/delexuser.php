<?php 

UCMS::load_site('pages/admin/common.php');

class admin_delexuser extends admin_common 
{
	
	public function process() 
	{
		parent::process(); 	
		
		if (!isset($_GET['id']))
		{
			$this->page_redirect('admin_edituser');
		}
		else 
		{
			$where = 'user_id = ?';
			$values = $_GET['id'];
			$this->db->delete('users', $where, $values);
			$this->page_redirect('admin_editexuser');
		}
	}
	
}

?>