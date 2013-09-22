<?php
function smarty_function_page_url_this_custom($params, $smarty)
{	
	if(isset($params['page']))
	{
		$page = $params['page'];
		unset($params['page']);
	}
	else
	{
		$page = 'index';
	}
	
	if (isset($params['assign']))
	{
		$assign = $params['assign'];
		unset($params['assign']);
	}
	else 
	{
		$assign = false;
	}
	
	$result = UCMS_Pages_Base::$current_page->page_url_this_custom($page, $params);
	
	if ($assign === false)
	{
		return $result;
	}
	else 
	{
		$smarty->assign($assign, $result);
	}
}
?>