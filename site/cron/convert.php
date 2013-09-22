<?php 
UCMS::load_cms('cron/Simple.php');

class convert extends UCMS_Cron_Simple
{
	private $ftp;
	private $remote_ppt_path;
	private $remote_img_path;
	private $local_path;
	
	public function process()
	{
		parent::process();

		if(!$f = $this->lock())
		{
			return false;
		}
		
		$this->config->init();
		
		$this->ftp_connect($this->config->get('ftp_host'), $this->config->get('ftp_user'), $this->config->get('ftp_pass'));
		
		$this->remote_ppt_path = $this->config->get('ftp_remote_ppt_path');
		$this->remote_img_path = $this->config->get('ftp_remote_img_path');
		$this->local_path = UCMS::get_site_path().'/tmp';
		
		$fs = new COM("iSpring.PresentationConverter");

		$st = $this->db->select('ppts', '*', 'ppt_converted = ?', 0, 'ppt_id ASC');
		
		while($row = $st->fetch(PDO::FETCH_ASSOC))
		{
			$this->convert_ppt($row, $fs);
		}
	}
	
	private function lock()
	{
		$f = fopen(__FILE__, 'r');
		if(!$f)
		{
			return false;
		}
		
		if(!flock($f, LOCK_EX)) return false;
		
		return $f;
	}
	
	private function convert_ppt($ppt, $fs)
	{
		$local_path = $this->ftp_get_file($ppt['ppt_id'], $ppt['ppt_file']);
		$local_img_path = $this->local_path.'/'.$ppt['ppt_id'];
		
		if(!file_exists($local_img_path))
		{
			if(!mkdir($local_img_path, 0777))
			{
				die("Could not create $local_img_path.");
			}
		}
		
		print "converting $local_path\n";
		flush();

		try
		{
			$fs->OpenPresentation($local_path);
		}
		catch(Exception $e)
		{
			die("Error: ".$fs->LastErrorDescription);
		}
		
		$width = $fs->Presentation->Width;
		$height = $fs->Presentation->Height;
		$ratio = $height / $width;
		
		$thumb_width = 160;
		$fs->Presentation->Slides(1)->SaveImage($local_img_path .'/thumb.jpg', $thumb_width, $thumb_width * $ratio, 75);
		
		$pic_width = 550;
		$fs->Presentation->Slides->SaveThumbnails($local_img_path, '', 2, $pic_width, $pic_width * $ratio );
		$total_slides = $fs->Presentation->Slides->Count;
		
		$fs->ClosePresentation();
		
		if(!unlink($local_path))
		{
			echo 'Could not delete: '.$local_path;
		}
		
	 	$remote_img_path = $this->remote_img_path.'/'.$ppt['ppt_id'];
		ftp_mkdir($this->ftp, $remote_img_path);
		
		$d = opendir($local_img_path) or die("Could not open: $local_img_path");
		while (false !== ($file = readdir($d)))
		{
        if($file == '.' || $file == '..') continue;
        ftp_put($this->ftp, $remote_img_path.'/'.$file, $local_img_path.'/'.$file, FTP_BINARY);
        
        unlink($local_img_path.'/'.$file);
	  }
	    closedir($d);
	    rmdir($local_img_path);
		
		$this->db->update('ppts', array('ppt_converted' => 1, 'ppt_slides_count' => $total_slides, 'ppt_pic' => 'jpg'), 'ppt_id = ?', $ppt['ppt_id']);
	}
	
	private function ftp_connect($ftp_server, $ftp_user, $ftp_pass)
	{
		$this->ftp = ftp_connect($ftp_server)
		or die('Could not connect to '.$ftp_server);
		
		ftp_login($this->ftp, $ftp_user, $ftp_pass)
		or die('Login could not be established');

		ftp_pasv($this->ftp, true);
		
		ftp_set_option($this->ftp, FTP_TIMEOUT_SEC, 10);
		
	}
	
	private function ftp_get_file($id, $ext)
	{
		$local_path = $this->local_path.'/'.$id.'.'.$ext;
		
		if(PHP_OS == 'WINNT')
		{
			$local_path = str_replace('/', '\\', $local_path);
		}
		
		if(!ftp_get($this->ftp, $local_path, $this->remote_ppt_path.'/'.$id.'.'.$ext, FTP_BINARY))
		{
			die("Could not retrieve file: $id.$ext");
		}
		
		return $local_path;	
	}
	
}
