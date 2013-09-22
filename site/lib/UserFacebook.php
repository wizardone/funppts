<?php
UCMS::load_site_once('lib/User.php');

class LIB_UserFacebook extends LIB_User
{
	protected $fbook;
	protected $fb_user_id;
	
	public function __construct(UCMS $cms, common $common, $fbook, $fb_user_id)
	{
		$this->fbook		= $fbook;
		$this->fb_user_id	= $fb_user_id;
		
		parent::__construct($cms, $common);
	}
	
	public function init(common $common, $id = 0, $flags = 0, $is_admin_sess = null)
	{
		$row = $this->db->select_hashrow('users', '*', 'user_fb_id = ?', $this->fb_user_id);
		if($row)
		{
			$status = parent::init($common, $row['user_id'], self::NO_DATA | self::NO_LOGIN, $is_admin_sess);
			$this->data = $row;
		}
		else
		{
			$status = false;
		}
		return $status;
	}
	
	public function set_fb_id($user_id)
	{
		$this->db->update('users', array('user_fb_id' => $this->fb_user_id), 'user_id = ?', $user_id);
	}
	
	public function create_face_user()
	{
		if($this->db->select_arrayrow('users', '1', 'user_fb_id = ?', $this->fb_user_id))
		{
			return false;
		}
		
		$pass = abs(crc32(md5(microtime(). $_SERVER['REMOTE_ADDR'])));
		$this->db->insert('users', array
		(
			'user_login'		=> 'fb_'. md5(microtime(). $_SERVER['REMOTE_ADDR']),
			'user_pass_hash'	=> $this->make_pass_hash($pass),
			'user_fb_pass'		=> $pass,
			'user_active'		=> 1,
			'user_reg_time'		=> time(),
			'user_reg_ip'		=> $_SERVER['REMOTE_ADDR'],
			'user_fb_id'		=> $this->fb_user_id
		));
		
		$user_id = $this->db->lastInsertId();
		$user_name = 'fb_'.$user_id;
		
		$this->db->update('users', array('user_login' => $user_name), 'user_id = ?', $user_id);
		
		return array($user_name, $pass);
	}
}