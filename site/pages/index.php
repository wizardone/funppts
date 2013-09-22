<?php 

//UCMS::load_site('lib/PPT.php');
UCMS::load_site('pages/common_site.php');

class index extends common_site
{
	const WEEK = 604800;
	
	function process() 
	{
		$per_page = 10;
		
		parent::process();
		$this->check();
		
		$page_data = $this->calc_page();
		
		$page = $page_data[0];
		$per_page = $page_data[1];
		$offset = $page_data[2];
		
		if($this->get('tag'))
		{
			$tag = $this->get('tag');
			$type_id = $this->get_object_type_id('ppt');
			$row = $this->db->select_arrayrow(array(array('ppts', 'p'), array('tags', 't'), array('tags_refs', 'tr')), 'COUNT(*)', 't.tag_word = ? AND t.tag_id = tr.ref_word_id AND tr.ref_object_type_id = ? AND tr.ref_object_id = p.ppt_id AND p.ppt_converted = ?' , array($tag, $type_id, 1));
			if(!($pages = ceil($row[0] / $per_page)))
			{
				$pages = 1;
			}
			
			$st = $this->db->select
				(array(array('ppts', 'p'), array('users', 'u'), array('tags', 't'), array('tags_refs', 'tr')),
				'p.*, u.user_first_name, u.user_last_name, u.user_login', ' p.ppt_user_id = u.user_id AND t.tag_word = ? AND t.tag_id = tr.ref_word_id AND tr.ref_object_type_id = ? AND tr.ref_object_id = p.ppt_id AND p.ppt_converted = ?',
				array($tag, $type_id, 1),
				'p.ppt_id DESC', 
				$per_page,
				$offset
			);
			
		}
		elseif($this->get('my') && $this->user->is_logged())
		{
			$this->assign_copy('myppt', 1);
			
			$row = $this->db->select_arrayrow('ppts', 'COUNT(*)', 'ppt_user_id = ?',$this->user->get_id());
			if(!($pages = ceil($row[0] / $per_page)))
			{
				$pages = 1;
			}
				
			$st = $this->db->select
				(array(array('ppts', 'p'),array('users', 'u')),
				'p.*, u.user_first_name, u.user_last_name, u.user_login', 'p.ppt_user_id = u.user_id AND p.ppt_user_id = ?',
				array($this->user->get_id()),
				'ppt_id DESC',
				$per_page,
				$offset
			);
			
		}
		elseif($this->get('fav') && $this->user->is_logged())
		{
			$this->assign_copy('favppt', 1);
			
			$row = $this->db->select_arrayrow('favourites', 'COUNT(*)', 'fav_user_id = ?', $this->user->get_id());
			if(!($pages = ceil($row[0] / $per_page)))
			{
				$pages = 1;
			}
			
			$st = $this->db->select
				(array(array('favourites', 'f'),array('ppts', 'p'), array('users', 'u')),
				'p.*, u.user_first_name, u.user_last_name, u.user_login', 
				' p.ppt_converted = ? AND p.ppt_user_id = u.user_id AND f.fav_user_id = ? AND f.fav_object_id = p.ppt_id AND f.fav_object_type_id = ?',
				array( 1, $this->user->get_id(), $this->get_object_type_id('ppt')),
				'f.fav_id DESC', 
				$per_page,
				$offset
					
			);
				
		}
		elseif($this->get('top'))
		{
			$this->assign_copy('topppt', 1);
			$data = $this->get_popular();
			$st = $data[0];
			$pages = $data[1];
		}
		elseif($this->get('downloaded'))
		{
			$this->assign_copy('downppt', 1);
			$data = $this->get_downloads();
			$st = $data[0];
			$pages = $data[1];
		}
		else
		{
			$data = $this->get_newest();
			$this->assign_copy('is_home', 1);
			$st = $data[0];
			$pages = $data[1];
		}
		
		$ids = array();
		$ppts = array();
		
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			$ppt = new LIB_PPT($this, $row);
			$ppts[$ppt->get_obj_id()] = $ppt;
			$ids[] = $ppt->get_obj_id();
		}
		
		if($ids && $this->user->is_logged())
		{
			$st = $this->db->select('favourites', 'fav_object_id', 'fav_user_id = ? AND fav_object_type_id = ? AND fav_object_id IN('.implode(',', $ids).')', array($this->user->get_id(), $this->get_object_type_id('ppt')) );
			
			while($row = $st->fetch(PDO::FETCH_ASSOC))
			{
				$ppt = $ppts[$row['fav_object_id']];
				$ppt->set_favourite();
			}
		}
		
		$this->assign('ppts', $ppts);
		$this->assign('pages', $pages);
		$this->assign('page', $page);
		
		$pager_url = $this->page_url_this(array('page' => null, 'uploaded' => null, 'reg' => null, 'lost' => null, 'new_pass' => null, 'act' => null, 'loggedin' => null, 'share' => null, 'sent' => null));
		$this->assign('pager_url', $pager_url);
		
	}
	
}

?>