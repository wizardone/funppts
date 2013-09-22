<?php 

UCMS::load_site('pages/common_site.php');
UCMS::load_site('lib/PPT.php');

class share extends common_site
{
	public function process()
	{
		$per_page = 10;
		parent::process();
		
		
		$page = intval($this->get('page', 1));
		if($page <= 0) $page = 1;
		
		$offset = ($page - 1) * $per_page;
		
		
		$id = intval($this->get('id'));
		if($id <= 0)
		{
			$this->page_redirect('index');
			exit;
		}
		
		$ppt = new LIB_PPT($this, $id);
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' )
		{
			$comment = $this->post('comment');
			if($comment)
			{
				$ppt->add_comment('', $comment);
			}
		}
		
		
		
		$ppt->load_user();
		$st = $this->db->select(array(array('tags', 't'),array('tags_refs', 'tr')), 
		't.tag_word',
		'tr.ref_object_id = ? AND tr.ref_object_type_id = ? AND tr.ref_word_id = t.tag_id',
		array($ppt->get_obj_id(), $this->get_object_type_id('ppt')),
		't.tag_word'
		
		);
		
		$tags = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			$tags[] = $row['tag_word'];
		}
	
		$row = $this->db->select_arrayrow('comments', 'COUNT(*)', 'comment_obj_id = ? AND comment_obj_type_id = ?',
		array($ppt->get_obj_id(), $this->get_object_type_id('ppt')));
		if(!($pages = ceil($row[0] / $per_page)))
		{
			$pages = 1;
		}
		$this->assign('total_comments', $row[0]);	
			
		$pt = $this->db->select(array(array('comments', 'c'), array('users', 'u')),
		'c.comment_contents,c.comment_date, u.user_first_name,u.user_last_name, u.user_avatar, u.user_id',
		'c.comment_user_id = u.user_id AND c.comment_obj_id = ? AND c.comment_obj_type_id = ?',
		array($ppt->get_obj_id(), $this->get_object_type_id('ppt')),
		'c.comment_id ASC',
		$per_page,
		$offset
		);
		
		
		$info = array();
		while ($row = $pt->fetch(PDO::FETCH_ASSOC))
		{
			$info[] = $row;
		}
		
		$pager_url = $this->page_url_this(array('page' => null, 'uploaded' => null, 'reg' => null, 'lost' => null, 'new_pass' => null, 'act' => null, 'loggedin' => null, 'sent' =>null));
		$this->assign('pager_url', $pager_url);

		$this->assign('tags', $tags);
		$this->assign('ppt', $ppt);
		$this->assign('info', $info);
		$this->assign('pages', $pages);
		$this->assign('page', $page);
		
		$this->assign_copy('user_security_image', $this->page_url('security'));
	}
	
}

?>