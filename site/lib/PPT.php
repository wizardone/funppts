<?php 

UCMS::load_site('lib/Tags.php');
UCMS::load_cms_once('forms/File.php');

class Lib_PPT extends Lib_Tags
{
	private $data = array();
	private $static_url;
	private $favourite = false;
	private $user;
	
	public function __construct(common $common, $obj_data)
	{
		$this->user = $common->get_user();
		
		if (is_array($obj_data))
		{
			parent::__construct($common, $obj_data['ppt_id'], 'ppt');
			$this->init_from_array($obj_data);
		}
		else 
		{
			if(is_numeric($obj_data))
			{
				parent::__construct($common, $obj_data, 'ppt');
				if ($obj_data > 0)
				{
					$this->init_from_id();
				}
			}
			else if($obj_data)
			{
				$row = $common->db()->select_hashrow('ppts', '*', 'ppt_url = ?', $obj_data);
				$row['ppt_id'] = ($row) ? $row['ppt_id'] : 0;
				
				parent::__construct($common, $row['ppt_id'], 'ppt');
				$this->init_from_array($row);
			}	
			
		}
		
		$config = $common->config();
		$this->static_url = $config->get('static_url', '.');
	}
	
	public function get_pimp_url()
	{
		return isset($this->data['ppt_url']) ? $this->data['ppt_url'] : "";
	}
	
	public function add_view()
	{
		$this->db->query('UPDATE ' . $this->db->quote_table('ppts') . 'SET ppt_views = ppt_views + 1 WHERE ppt_id = ?', array
		(
			$this->obj_id
		));
	}
	
	public function add_download()
	{
		$this->db->query('UPDATE ' . $this->db->quote_table('ppts') . 'SET ppt_downloads = ppt_downloads + 1 WHERE ppt_id = ?', array
		(
			$this->obj_id
		));
	}
	
	public function get_path()
	{
		return $this->get_ppt_path($this->obj_id, $this->data['ppt_file']);
	}
	
	public function set_favourite()
	{
		$this->favourite = true;
	}
	
	public function get_favourite()
	{
		return $this->favourite;
	}
	
	public function get_slides_count()
	{
		return isset($this->data['ppt_slides_count']) ? $this->data['ppt_slides_count'] : 0;
	}
	
	public function get_pic_url()
	{
		return $this->static_url. '/ppts/pics/' . $this->data['ppt_id']. '/thumb.' . $this->data['ppt_pic'];	
	}
	
	public function get_pics_url()
	{
		return $this->static_url. '/ppts/pics';
	}
	
	public function get_swf_url()
	{
		return $this->static_url. '/ppts/' . $this->data['ppt_id']. '.swf';
	}
	
	public function get_title()
	{
		return isset($this->data['ppt_title']) ? $this->data['ppt_title'] : "";
	}
	
	public function get_converted()
	{
		return isset($this->data['ppt_converted']) ? $this->data['ppt_converted'] : 0;
	}
	
	public function get_desc()
	{
		return isset($this->data['ppt_description']) ? $this->data['ppt_description'] : "";	
	}
	
	public function get_views()
	{
		return isset($this->data['ppt_views']) ? $this->data['ppt_views'] : "";
	}
	
	public function get_download()
	{
		return isset($this->data['ppt_downloads']) ? $this->data['ppt_downloads'] : "0";
	}
	
	public function get_mime()
	{	
		return isset($this->data['ppt_content_type']) ? $this->data['ppt_content_type'] : "";	
	}
	
	public function get_content_name()
	{	
		return isset($this->data['ppt_content_name']) ? $this->data['ppt_content_name'] : "";	
	}
	
	public function get_user_id()
	{
		return isset($this->data['ppt_user_id']) ? $this->data['ppt_user_id'] : "";
	}
	
	public function get_user()
	{
		if($this->data['user_first_name'])
		{
			$user = $this->data['user_first_name'];
			
			if($this->data['user_last_name'])
			{
				$user .= ' '.$this->data['user_last_name'];
			}
		}
		else 
		{
			$user = $this->data['user_login'];
		}
	
		return $user;
	}
	
	public function get_comments()
	{
		$row = $this->db->select_arrayrow('comments', 'COUNT(*)', 'comment_obj_id = ?',
		array($this->obj_id));

		$comments = $row[0];
		
		return $comments;
	}
	
	public function load_user()
	{
		$row = $this->db->select_hashrow('users','user_first_name, user_last_name, user_login', 'user_id = ?', $this->data['ppt_user_id'] );
		if($row)
		{
			$this->data['user_first_name'] = $row['user_first_name'];
			$this->data['user_last_name'] = $row['user_last_name'];
			$this->data['user_login'] = $row['user_login'];
		}
	}
	
	public function get_url()
	{
		/*$config = $this->common->config();
		$url = $config->get('static_url', '.') . '/ppts/' . $this->obj_id . '.' . $this->file->ext();
		return $url;*/
	}
	
	public function register($fields, $user_id, UCMS_Forms_File $file, $tags, $ppt_url, $add_id)
	{
		if ($this->obj_id) return false;
		
		if (isset($fields['ppt_title']) && $user_id)
		{
		
			$features = array
			(
				'ppt_user_id'		=>	$user_id,
				'ppt_title'			=>	$fields['ppt_title'],
				'ppt_description'	=>	$fields['ppt_description'] ? $fields['ppt_description'] : '',
				'ppt_file'			=>	$file->ext(),
				'ppt_time'			=> 	time(),
				'ppt_content_type'	=>  $file->mime(),
				'ppt_content_name'	=> 	$file->name(),
			);
			
			$this->db->insert('ppts', $features);
			$this->obj_id = $this->db->lastInsertId();
			
			if($add_id)
			{
				$ppt_url.= '_'.$this->obj_id;
			}
			
			$this->db->update('ppts', array('ppt_url' => $ppt_url), 'ppt_id = ?', $this->obj_id);
			
			$path = $this->get_ppt_path($this->get_obj_id(), $file->ext());
			$file->move($path);
			
			$this->add_tags($tags);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function get_similar($limit = 10)
	{
		$st = $this->db->select('tags_refs', 'ref_word_id', 'ref_object_id = ? AND ref_object_type_id = ?', array($this->obj_id, $this->obj_type_id));
		
		$tags = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			$tags[$row['ref_word_id']] = true;
		}
		
		if(!$tags)
		{
			return array();
		}
		
		$ids = array($this->obj_id, $this->obj_type_id);
		$ids = array_merge($ids, array_keys($tags));
		$st = $this->db->select('tags_refs', 'ref_object_id, ref_word_id', 'ref_object_id != ? AND ref_object_type_id = ? AND ref_word_id IN ('.$this->db->get_questions(count($tags)).')', $ids);
		
		$refs = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			if(!isset($tags[$row['ref_word_id']]))
			{
				continue;
			}
			
			if(isset($refs[$row['ref_object_id']]))
			{
				$refs[$row['ref_object_id']]++;
			}
			else
			{
				$refs[$row['ref_object_id']] = 1;
			}
		}
		
		if(!$refs)
		{
			return array();
		}
		
		arsort($refs, SORT_NUMERIC);
		$refs = array_slice($refs, 0, $limit, true);
		
		$st = $this->db->select('ppts', '*', 'ppt_id IN('.$this->db->get_questions(count($refs)).')', array_keys($refs));
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			$refs[$row['ppt_id']] = new Lib_PPT(UCMS_Pages_Base::$current_page, $row);
		}
		
		return $refs;
	}
	
	
	private function init_from_array(array $ppt_data)
	{
		$this->data			= $ppt_data;
	}
	
	private function init_from_id()
	{
		$stm = $this->db->select('ppts', '*', 'ppt_id = ?',array
		(
			$this->obj_id
		));
		
		$row = $stm->fetch(PDO::FETCH_ASSOC);
		$this->init_from_array($row);
	}
	
	private function get_ppt_path($id, $ext)
	{
		$path = UCMS::get_site_path().'/ppts/'. $id.'.'.$ext;
		return $path;
	}
}

?>