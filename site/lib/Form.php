<?php 

UCMS::load_cms('forms/Simple.php');

class Lib_Form extends UCMS_Forms_Simple 
{
	
	public function __construct(common $page, &$fields, $json = false, $name = 'form', $method = 'POST')
	{
		parent::__construct($page, $fields, $json, $name, $method, $page->get_lang_obj());
	}

	protected function check_exists($field, &$value, &$fdata, &$data, $field_num = null)
	{
		$user = $this->page->get_user();
	
		if(isset($fdata['mail']) && $fdata['mail'])
		{
			if($data == $user->email_exists($value, $fdata['mail']))
			{
				return true;
			}
		}
		else
		{
			if($data == $user->login_exists($value))
			{
				return true;
			}
		}
	
		if(!$data)
		{
			$error = 'exists';
		}
		else
		{
			$error = 'existsnot';
		}
	
		$this->set_error($field, $error, $value, $field_num);
		return false;
	}
 
}

?>