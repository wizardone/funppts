<?php
UCMS::load_cms('cron/Base.php');

class daemon extends UCMS_Cron_Base
{
	const DIR		= 'c:\fun';
	const COMMAND	= 'c:\php\php.exe index.php convert';
	
	public function process()
	{
		parent::process();
		
		chdir(self::DIR);
		
		// enter eternity
		while(true)
		{
			sleep(300);
			system(self::COMMAND);
		}
	}
}