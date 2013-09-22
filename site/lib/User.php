<?php 

UCMS::load_cms('users/Standard.php');
UCMS::load_site_once('lib/Base.php');


class Lib_User extends UCMS_Users_Standard  
{
	private $base;
	private $config;
	
	public function __construct(UCMS $cms, common $common) 
	{
		parent::__construct($cms, $common->sess(), $common->db());
		$this->config = $common->config();
	}
	
	public function init(common $common, $id = 0, $flags = 0, $is_admin_sess = null)
	{
		$status = parent::init($common, $id, $flags, $is_admin_sess);
		$this->base = new Lib_Base($common, $this->get_id(), 'user');
		
		return $status;
	}
	
	public function add_comment($title, $contents)
	{
		$this->base->add_comment($title, $contents);
	}
	
	public function edit_comment($id, $title, $contents)
	{
		$this->base->edit_comment($id, $title, $contents);
	}
	
	public function delete_comment($id)
	{
		$this->base->delete_comment($id);
	}
	
	public function update_full_name($first_name, $last_name) 
	{
		$this->update_field('user_first_name', $first_name);		
		$this->update_field('user_last_name', $last_name);
		
	}
	
	public function update_is_active($is_active) 
	{
		$this->update_field('user_active', $is_active);
	}
	
	
	public function update_email($email) 
	{
		$this->update_field('user_email', $email);
	}

	public function update_birth_date($birth_date_year, $birth_date_month, $birth_date_day) 
	{		
		$this->update_field('user_birth_date', mktime(0, 0, 0, $birth_date_month, $birth_date_day, $birth_date_year));
	}

	public function update_sex($sex) 
	{
		$this->update_field('user_sex', $sex);
	}

	public function update_interests($interests) 
	{
		$this->update_field('user_interests', $interests);	
	}

	public function update_avatar(UCMS_Forms_File $file) 
	{
		$path = $this->avatar_path($this->get_id(), $file->ext());
		$file->move($path);
		
		UCMS::load_cms_once('images/Thumbnail.php');
		$thumb = new UCMS_Images_Thumbnail();
		$thumb->setSourceFilename($path);
		$thumb->setParameter('w', 80);
		$thumb->GenerateThumbnail();
		
		$thumb->RenderToFile($path);
		$this->update_field('user_avatar', $file->ext());
		
	}
	
	public function delete_avatar()
	{
		$path = $this->avatar_path($this->get_id(), $this->data['user_avatar']);
		@unlink($path);
		$this->update_field('user_avatar', '');
	}
	
	public function get_avatar($id = null, $ext = null)
	{
		$id = !is_null($id) ? $id : $this->data['user_id'];
		$ext = !is_null($ext) ? $ext : $this->data['user_avatar'];
		
		if(!$id || !$ext)
		{
			$id = 0;
			$ext = 'jpg';
		}
		
		$url = $this->config->get('static_url', '.') . '/images/avatars';
		return $url.'/'.$id.'.'.$ext;
	}
	
	public function email_exists($email, $skip = 0)
	{
		if($skip && is_numeric($skip))
		{
			$stm = $this->db->select('users', '1', 'user_email = ? AND user_id != ?', array($email, $skip));
		}
		else
		{
			$stm = $this->db->select('users', '1', 'user_email = ?', $email);
		}
		
		
		if ($stm->fetch(PDO::FETCH_ASSOC))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function login_exists($login)
	{
		$stm = $this->db->select('users', '1', 'user_login = ?', $login);
		
		if ($stm->fetch(PDO::FETCH_ASSOC))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function get_name()
	{
		$name = '';
		
		if($this->data['user_first_name'])
		{
			$name= $this->data['user_first_name'];
			
			if($this->data['user_last_name'])
			{
				$name.= ' '.$this->data['user_last_name'];
			}
		}
		else if($this->data['user_login'])
		{
			$name = $this->data['user_login'];
		}
		else
		{
			$name = "Anonymous";
		}
		
		return $name;
	}
	
	public function get_id_by_userpass($user_login, $user_pass)
	{
		$row = $this->db->select_hashrow('users', 'user_id', 'user_login = ? AND user_pass_hash = ?', array($user_login, $this->make_pass_hash($user_pass)));
		if($row)
		{
			return $row['user_id'];
		}
		
		return false;
	}
	
	private function avatar_path($id, $ext)
	{
		$path = $this->config->get('static_path', '.') . '/images/avatars';
		return $path . '/' . $id .'.'. $ext;
	}
}

?>