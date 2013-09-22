<?php
fun_main();

function fun_main() 
{
	$root = dirname(__FILE__) . '/..';
    $site = $root . '/site';
    $ucms = $root . '/ucms';
    
	require($site . '/config.php');
	require($ucms . '/UCMS.php');

	$cms_config = new UCMS_UCMSConfig($config);
	$cms = new UCMS($cms_config, $site, $ucms);
}