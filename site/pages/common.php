<?php 

UCMS::load_cms('pages/Standard.php');
UCMS::load_site_once('lib/User.php');
UCMS::load_site('lib/PPT.php');

abstract class common extends UCMS_Pages_Standard 
{
	const LIVE_HOST = 'funppsfun.com';
	const WEEK = 604800;
	
	
	protected $user;
	protected $lang_obj;
	protected $lang;
	protected $is_live = false;

	private $object_type_ids = null;
	
	public function __construct(UCMS $cms) 
	{
		parent::__construct($cms);
		
		$current_url = $this->page_url_full($this->page_name, $this->get_page_params());
		$parsed_url = parse_url($current_url);
		
		$host = strtolower($parsed_url['host']);
		switch($host)
		{
			case 'funpowerpoints.com':
			case 'www.funpowerpoints.com':
			case 'www.funppsfun.com':
				$current_url = str_replace($parsed_url['host'], 'funppsfun.com', $current_url);
				$this->redirect($current_url, 301);
				exit;
			break;
			
			case self::LIVE_HOST:
				$this->is_live = true;
			break;
		}
		
		$this->init();
		$this->init_lang();
	}
	
	public function get_user()
	{
		return $this->user;
	}
	
	public function get_lang_obj()
	{
		return $this->lang_obj;	
	}
	
	public function process() 
	{
		parent::process();
		$this->assign_copy('this_page', $this->this_page_url());
		
		$static_url = $this->config->get('static_url', '.');
		$this->url_img = $static_url . '/images';
		$this->url_js = $static_url . '/js';
		$this->url_css = $static_url . '/css';
		
		$this->assign_copy('url_img', $this->url_img);
		$this->assign_copy('url_js', $this->url_js);
		$this->assign_copy('url_css', $this->url_css);
		$this->assign_copy('url_rss', $static_url . '/rss');
		$this->assign_copy('url_avatars', $static_url . '/avatars');
		
		$this->assign_copy('menu_profile', $this->page_url('profile'));
		$this->assign_copy('menu_editprofile', $this->page_url('editprofile'));
		$this->assign_copy('menu_register', $this->page_url('register', null, $this->secure_controler_url()));
		$this->assign_copy('menu_forgetpassword', $this->page_url('lostpass', null, $this->secure_controler_url()));
		$this->assign_copy('menu_login', $this->page_url('login'));
		$this->assign_copy('menu_logout', $this->page_url('login', array
		(
			'logout' => 1
		)));
		
		$this->assign('user', $this->user);
		$this->assign('lang', $this->lang);
		
		$this->add_js('main.js');
		
	}
	
	public function get_object_type_id($name)
	{
		if ($this->object_type_ids == null) 
		{
			$stm = $this->db->select('object_type_ids', 'obj_type_id, obj_type_name');
			$this->object_type_ids = array();
			while ($row = $stm->fetch(PDO::FETCH_ASSOC))
			{
				$this->object_type_ids[$row['obj_type_name']] = $row['obj_type_id'];
			}
		}
		
		if (isset($this->object_type_ids[$name]))
		{
			return $this->object_type_ids[$name];
		}
		else 
		{
			$this->db->insert('object_type_ids', array
			(	
				'obj_type_name'		=>	$name
			));
			
			$id = $this->db->lastInsertId();
			$this->object_type_ids[$name] = $id;
			return $id;
		}

	}
	
	public function &page_url_must_login($page, $params = null, $controler_url = null, $no_sid = false)
	{
		if($this->user->is_logged())
		{
			return $this->page_url($page, $params, $controler_url, $no_sid);
		}
	
		$new_params = array(
			'rp' => $page
		);
	
		if($params != null)
		{
			$new_params['rpp'] =& $this->prepare_params($params);
		}
		
		return $this->page_url('login', $new_params, $this->secure_controler_url(), $no_sid);
	}
	
	public function get_download_page()
	{
		$id = intval($this->get('id'));
		if($id <= 0)
		{
			$this->page_redirect('index');
			return false;
		}
		$ppt = new LIB_PPT($this, $id);
		
		$ppt->load_user();
		$this->assign('ppt', $ppt);
		$this->assign_copy('meta_refresh', 1);
		
		if($this->get('do_download'))
		{
			header('Content-Type: ' . $ppt->get_mime());
			header('Content-Length: '. filesize($ppt->get_path()));
			header('Content-Disposition: attachment;filename="'.$ppt->get_content_name().'"');
			
			readfile($ppt->get_path());
			return false;
		}
		else
		{
			$ppt->add_download();
		}
		
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
		
		$md = $this->db->select('ppts',
		 'ppt_description',
		 'ppt_id = ? AND ppt_converted = ?',
		 array($ppt->get_obj_id(), 1));
		 
	    $row = $md->fetch(PDO::FETCH_ASSOC);
		 
 		$meta_desc = $row['ppt_description'];
		
		$this->assign('tags', $tags);
		$this->assign('meta_desc', $meta_desc);
	}
	
	public function get_newest()
	{
		$page_data = $this->calc_page();
		
		$row = $this->db->select_arrayrow('ppts', 'COUNT(*)', 'ppt_converted = ?', 1);
		if(!($pages = ceil($row[0] / $page_data[1])))
		{
			$pages = 1;
		}
		
		$st = $this->db->select
			(array(array('ppts', 'p'),array('users', 'u')),
			'p.*, u.user_first_name, u.user_last_name, u.user_login', 'ppt_converted = ? AND p.ppt_user_id = u.user_id',
			1,
			'ppt_id DESC', 
			$page_data[1],
			$page_data[2]		
		);
			
		return array($st, $pages);
	}
	
	public function get_popular()
	{
		$page_data = $this->calc_page();
		
		$row = $this->db->select_arrayrow('ppts', 'COUNT(*)', 'ppt_converted = ? AND ppt_time > ? AND ppt_views > ? ',
			 array(1, time() - self::WEEK, 0)
			 );
			 
		if(!($pages = ceil($row[0] / $page_data[1])))
		{
			$pages = 1;
		}
		
		$st = $this->db->select(array(array('ppts', 'p'),array('users', 'u')),
			'p.*, u.user_first_name, u.user_last_name, u.user_login',
			'ppt_converted = ? AND p.ppt_user_id = u.user_id AND ppt_time > ? AND ppt_views > ? ',
			array(1, time() - self::WEEK, 0),
			'ppt_views DESC',
			$page_data[1],
			$page_data[2]
		);
		
		return array($st, $pages);
	}
	
	public function get_downloads()
	{
		$page_data = $this->calc_page();
		
		$row = $this->db->select_arrayrow('ppts', 'COUNT(*)', 'ppt_converted = ?  AND ppt_time > ? AND ppt_downloads > ?',
			 array(1, time() - self::WEEK, 0)
			 );
			 
		if(!($pages = ceil($row[0] / $page_data[1])))
		{
			$pages = 1;
		}
		
		$st = $this->db->select(array(array('ppts', 'p'),array('users', 'u')),
			'p.*, u.user_first_name, u.user_last_name, u.user_login',
			'ppt_converted = ? AND p.ppt_user_id = u.user_id AND ppt_time > ? AND ppt_downloads > ? ',
			array(1, time() - self::WEEK, 0),
			'ppt_downloads DESC',
			$page_data[1],
			$page_data[2]
		);
		
		return array($st, $pages);
	}
	
	public function get_view()
	{
		$per_page = 10;
		
	
		$page = intval($this->get('page', 1));
		if($page <= 0) $page = 1;
		
		$offset = ($page - 1) * $per_page;
		
		
		$id = $this->get('id');
		$ppt = new LIB_PPT($this, $id);
		
		if(!$ppt->get_obj_id())
		{
			$this->page_redirect_default();
			
			return false;
		}
		
		if($this->get('show_id'))
		{
			print $ppt->get_obj_id();
		}
		
		$fields_comments = array('comment' => '');
		$comments_form = new Lib_Form($this, $fields_comments, false, 'comments');
		
		if($comments_form->valid())
		{
			$ppt->add_comment('', $fields_comments['comment']['value']);
		}
		
		$fields_share = array
		(
			'share_from' 	=> '',
			'share_to'		=> array('mail' => true),
			'share_comment'	=> array('required' => false),
			'user_security' => array('code' => true)
		);
		
		if($this->get('share'))
		{
			$this->assign_copy('share', 1);
		}
		
		$share_form = new Lib_Form($this, $fields_share, false, 'shares');
		
		if($share_form->valid())
		{
			UCMS::load_cms('Mail.php');
			$mail = new UCMS_Mail($this->cms, $this->config);
			
			$mail->ContentType = 'text/html';
			$mail->From = 'sharing@funppsfun.com';
			$mail->Subject = 'Your friend '.$fields_share['share_from']['value']. ' wants you to see this presentation.';
			
			$message = 'powerpoints message from: ' . $fields_share['share_from']['value']. '<br /><br />';
			
			if(isset($fields_share['share_comment']['value']) && $fields_share['share_comment']['value'])
			{
				$message .= $fields_share['share_comment']['value']. '<br />';
			}
			$message .= $this->page_url_full('view', array('id' => $id));
			
			$mail->Body = $message;
			$mail->AddAddress($fields_share['share_to']['value']);
			$mail->Send();
			
			$this->page_redirect_post('view', array('id' => $id, 'sent' => 1));
		}
		
		if($this->get('sent'))
		{
			$this->status_window('', '<b>Mail sent successfully.</b>');
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
		
		
		$mt = $this->db->select(array(array('tags', 't'),array('tags_refs', 'tr')), 
		't.tag_word',
		'tr.ref_object_id = ? AND tr.ref_object_type_id = ? AND tr.ref_word_id = t.tag_id',
		array($ppt->get_obj_id(), $this->get_object_type_id('ppt')),
		't.tag_word'
		);
		
		$meta_key = array();
		while($row = $mt->fetch(PDO::FETCH_ASSOC))
		{
			$meta_key[] = $row['tag_word'];
		}
		
		$meta_key = implode(',', $meta_key);
		
		
		$md = $this->db->select('ppts',
		 'ppt_description, ppt_title',
		 'ppt_id = ? AND ppt_converted = ?',
		 array($ppt->get_obj_id(), 1));
		
	    $row = $md->fetch(PDO::FETCH_ASSOC);
		 
 		$meta_desc = $row['ppt_description'];
		$ppt_title = $row['ppt_title'];
		
		 
		
		$row = $this->db->select_arrayrow('comments', 'COUNT(*)', 'comment_obj_id = ? AND comment_obj_type_id = ?',
		array($ppt->get_obj_id(), $this->get_object_type_id('ppt')));
		if(!($pages = ceil($row[0] / $per_page)))
		{
			$pages = 1;
		}

		$this->assign('total_comments', $row[0]);
		
		$no_comments = 'No comments added yet.<br />';
		$this->assign('no_comments', $no_comments);

				
		$pt = $this->db->select(array(array('comments', 'c'), array('users', 'u')),
		'c.comment_contents,c.comment_date, u.user_first_name,u.user_last_name, u.user_avatar, u.user_id, u.user_login',
		'c.comment_user_id = u.user_id AND c.comment_obj_id = ? AND c.comment_obj_type_id = ?',
		array($ppt->get_obj_id(), $this->get_object_type_id('ppt')),
		'c.comment_id DESC',
		$per_page,
		$offset
		);
		
		$comments_items = array();
		
		while($row = $pt->fetch(PDO::FETCH_ASSOC))
		{
			$comments_items[] = $row;
			
			if(empty($row['user_first_name']) && empty($row['user_last_name']))
			{
				$this->assign('user_login', $row['user_login']);
			}
		}
		/*// to be continued - more pps from one user
		$st = $this->db->select('ppts', '*', 'ppt_user_id = ?', $ppt->get_user_id());
		
		$user_pps = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			//$user_pps[] = $row;
		}
		*/
		$this->assign('url_comment', $this->page_url_must_login('view', array('id' => $id)));
		
		$ppt->add_view();
		
		$pager_url = $this->page_url_this(array('page' => null, 'uploaded' => null, 'reg' => null, 'lost' => null, 'new_pass' => null, 'act' => null, 'loggedin' => null, 'share' => null, 'sent' => null));
		$this->assign('pager_url', $pager_url);
		
		$this->assign_copy('heading', 1);
		$this->assign('ppt_title', $ppt_title);
		$this->assign('meta_desc', $meta_desc);
		$this->assign('meta_key', $meta_key);
		$this->assign('tags', $tags);
		$this->assign('ppt', $ppt);
		$this->assign('comments_items', $comments_items);
		$this->assign('pages', $pages);
		$this->assign('page', $page);
		$this->assign('errors', $errors);
		$this->assign_copy('user_security_image', $this->page_url('security'));
		
		$this->assign('similar', $ppt->get_similar());
	}

	public function &page_url_this_custom($page, $params = null, $controler_url = null)
	{
		$page_params =& $this->get_page_params();
		if($params != null)
		{
			foreach($params as $k => &$v)
			{
				if(is_null($v))
				{
					unset($page_params[$k]);
				}
				else
				{
					$page_params[$k] = $v;
				}
			}
		}
		
		return $this->page_url($page, $page_params);
	}
	
	protected function init() 
	{
		$this->db->query("SET NAMES 'UTF8'");
		
		$this->sess->init($this);
		$this->config->init();
		$this->init_user();
		
		$this->tpl->add_plugins_dir(UCMS::get_site_path() . '/lib/template');
	}
	
	protected function init_lang() 
	{
		$this->lang_obj = new UCMS_Lang($this->cms, $this->cache(), $this->db);
		$this->lang_obj->init('en');
		$this->lang =& $this->lang_obj->get_data();
	}
	
	protected function init_user()
	{
		$this->user = new Lib_User($this->cms, $this);
		$this->user->init($this);
	}
	
	protected function generate_ppt_url($name, &$add_id)
	{
		$name = preg_replace('/[^a-z0-9`~!@#$%\^&*()_+\-={}|\[\]\\:";\'<>?,.\/\s]/i', '', $name);
		$name = strtolower($name);
		$name = preg_replace('/[\s~!@#$%\^&*()+"\':;\/\\?=|\]\[{}<>`,]/', '_', $name);
		
		$name = preg_replace('/[_]{2,}/', '_', $name);
		$name = preg_replace('/^[_]*/', '', $name);
		$name = preg_replace('/[_]*$/', '', $name);
		
		if($name)
		{
			$st = $this->db->select('ppts', '1', 'ppt_url = ?', $name);
			
			if($st->fetch(PDO::FETCH_ASSOC))
			{
				$add_id = true;
			}
		}
		else
		{
			$add_id = true;
		}
		
		return $name;
	}
	
	protected function secure_controler_url()
	{
		if($this->is_live)
		{
			return 'https://' . self::LIVE_HOST . '/index.php';
		}
		
		return null;
	}
	
	protected function calc_page()
	{
		$page = intval($this->get('page', 1));
		$per_page = 10;
		$offset = ($page - 1) * $per_page;
		
		return array($page, $per_page, $offset);
	}
}

?>