<?php 
UCMS::load_cms('cron/Simple.php');

class feeds extends UCMS_Cron_Simple
{
	private $per_page = 10;
	
	const WEEK = 604800;
	
	private $tpl;
	private $tpl_file;
	private $feed_name;
	private $parse = false;
	private $url_img;
	private $url_css;
	private $url_js;
	
	public function __construct(UCMS $cms)
	{
		parent::__construct($cms);
		
		$this->init_rss();
		$this->init_tpl();
	}
	
	public function process()
	{
		parent::process();
		
		switch($this->feed_name)
		{
			case 'latest':
			case 'popular':
			case 'downloaded':
				$func = $this->feed_name;
				$this->$func();
				$this->parse = true;
			break;
			
			default:
				die('Unknown feed name: ' . $this->feed_name);
			break;
		}
		
	}
	
	function __destruct()
	{
		if($this->parse)
		{
			$data = $this->tpl->fetch($this->tpl_file);
			$path = $this->config->get('static_path');
			$path .= '/rss/' . $this->feed_name. '.rss';
			
			file_put_contents($path, $data);
		}
	}
	
	
	private function latest()
	{
		$this->assign('feed_title', 'Latest presentations');
		$this->assign('feed_desc', 'Latest powerpoint presentations from funppsfun.com');
		$this->assign('channel_url', 'http://funppsfun.com');
		
		$st = $this->db->select
			(array(array('ppts', 'p'),array('users', 'u')),
			'p.*, u.user_first_name, u.user_last_name', 'ppt_converted = ? AND p.ppt_user_id = u.user_id',
			1,
			'ppt_id DESC', 
			$this->per_page
				
		);
		
		$this->assign('items', $st);
	}
	
	private function popular()
	{
		$this->assign('feed_title', 'Most popular presentations');
		$this->assign('feed_desc', 'Most popular powerpoint presentations from funppsfun.com');
		$this->assign('channel_url', 'http://funppsfun.com/popular');
		
		$st = $this->db->select(array(array('ppts', 'p'),array('users', 'u')),
			'p.*, u.user_first_name, u.user_last_name',
			'ppt_converted = ? AND p.ppt_user_id = u.user_id AND ppt_time > ? AND ppt_views > ? ',
			array(1, time() - self::WEEK, 0),
			'ppt_views DESC',
			$this->per_page
			
		);
		
		$this->assign('items', $st);
	}
	
	private function downloaded()
	{
		$this->assign('feed_title', 'Most downloaded presentations');
		$this->assign('feed_desc', 'Most downloaded powerpoint presentations from funppsfun.com');
		$this->assign('channel_url', 'http://funppsfun.com/downloaded');
		
		$st = $this->db->select(array(array('ppts', 'p'),array('users', 'u')),
			'p.*, u.user_first_name, u.user_last_name',
			'ppt_converted = ? AND p.ppt_user_id = u.user_id AND ppt_time > ? AND ppt_downloads > ? ',
			array(1, time() - self::WEEK, 0),
			'ppt_downloads DESC',
			$this->per_page
		
		);
		
		$this->assign('items', $st);
	}
	
	
	private function assign($k, $v)
	{
		$this->tpl->assign($k, $v);
	}
	
	private function init_tpl()
	{
		$this->config->init();
		
		UCMS::load_cms('Template.php');
		$this->tpl = new UCMS_Template($this->cms);
		
		$this->tpl_file = 'cron/rss_2.0.xml';
		
		$static_url = $this->config->get('static_url', '.');
		
		$this->url_img = $static_url . '/images';
		$this->url_js = $static_url . '/js';
		$this->url_css = $static_url . '/css';
		
		$this->assign('url_static', $static_url);
		$this->assign('url_img', $this->url_img);
		$this->assign('url_js', $this->url_js);
		$this->assign('url_css', $this->url_css);
		$this->assign('url_avatars', $static_url . '/avatars');
	}
	
	private function init_rss()
	{
		$feed_name = $this->param(2);
		
		if(!$feed_name)
		{
			die('Please supply feed name');
		}
		
		$this->feed_name = $feed_name;
	}
}
