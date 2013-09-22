<?php

UCMS::load_site('pages/common_site.php');
UCMS::load_site('lib/PPT.php');
UCMS::load_site('lib/Form.php');

class editprofile extends common_site 
{
	
	public function __construct(UCMS $cms) 
	{
		parent::__construct($cms);
		
		if (!$this->user->is_logged())
		{
			$this->page_redirect('index');
		}
	}
	
	public function process() 
	{
		parent::process();
		$this->assign_copy('page_name', 'User Edit Profile');
		
		$len = array(3, 10);
		
		$fields = array
		(
			
			'user_pass'					=>		array
			(
				'len'		=> 3,
				'required'	=> false
			),
			'user_first_name'			=>		array
			(
				'len'		=> $len,
				'required'	=> false
			),
			'user_last_name'			=>		array
			(
				'len'		=> $len,
				'required'	=> false
			),
			'user_email'				=>		array
			(
				'mail'		=> $this->user->get_id(),
				'exists'	=> false,
			),
			'user_birth_date_day'		=>		array
			(
				'required'	=> false,
			),
			'user_birth_date_month'		=>		array
			(
				'required'	=> false
			),
			'user_birth_date_year'		=>		array
			(
				'required'	=> false
			),
			'user_sex'					=>		array
			(
				'required'	=> false
			),
			'user_interests'			=>		array
			(
				'required'	=> false
			),
			'user_avatar'				=>		array
			(
				'required'	=> false,
				'file'		=> true,
				'mime'		=> array('image/jpeg',
									 'image/png',
									 'image/pjpeg',
									 'image/x-png'
									 )
			)
		);
			
		$form = new Lib_Form($this, $fields);
		if ($form->submitted())
		{
			if ($form->valid())
			{
				if ($fields['user_pass']['value'])
				{
					$this->user->update_password($fields['user_pass']['value']);
				}
				
				$this->user->update_full_name($fields['user_first_name']['value'], $fields['user_last_name']['value']);
				
				$this->user->update_email($fields['user_email']['value']);
				
				
				if (($fields['user_birth_date_year']['value'] != 0) && ($fields['user_birth_date_year']['value'] !=0) && ($fields['user_birth_date_year']['value'] != 0))
				{
					$this->user->update_birth_date($fields['user_birth_date_year']['value'], $fields['user_birth_date_month']['value'], $fields['user_birth_date_day']['value']);
				}
				else
				{
					$this->user->update_field('user_birth_date', 0);
				}
				
				$this->user->update_sex($fields['user_sex']['value']);
				$this->user->update_interests($fields['user_interests']['value']);
				
					
				if (isset($fields['user_avatar']['value']))
				{	
					$this->user->update_avatar($fields['user_avatar']['value']);
				}
				
				$this->page_redirect_post('index');
				
			}
		}
		else 
		{
			$data =& $this->user->get_data();
			
			if ($this->get('del'))
			{
				$this->user->delete_avatar();
			}
			
			$data['user_birth_date_day'] = date('d', $data['user_birth_date']);
			$data['user_birth_date_month'] = date('m', $data['user_birth_date']);
			$data['user_birth_date_year'] = date('Y', $data['user_birth_date']);
			
			$form->set_values($data);
			
		}	
		
		$sex = array
		(
			0	=>	$this->lang['text_undefined'],
			1	=>	$this->lang['text_male'],
			2	=>	$this->lang['text_female']
		);
		
		$days = array
		(
			0	=>	$this->lang['combo_none'],
			1	=>	'1',
			2	=>	'2',
			3	=>	'3',
			4	=>	'4',
			5	=>	'5',
			6	=>	'6',
			7	=>	'7',
			8	=>	'8',
			9	=>	'9',
			10	=>	'10',
			11	=>	'11',
			12	=>	'12',	
			13	=>	'13',
			14	=>	'14',
			15	=>	'15',
			16	=>	'16',
			17	=>	'17',
			18	=>	'18',
			19	=>	'19',
			20	=>	'20',
			21	=>	'21',
			22	=>	'22',
			23	=>	'23',
			24	=>	'24',		
			25	=>	'25',
			26	=>	'26',
			27	=>	'27',
			28	=>	'28',
			29	=>	'29',
			30	=>	'30',
			31	=>	'31',
			
		);
		
		$years = array
		(
			0		=>	$this->lang['combo_none'],
			1950	=>	'1950',
			1951	=>	'1951',
			1952	=>	'1952',
			1953	=>	'1953',
			1954	=>	'1954',
			1955	=>	'1955',
			1956	=>	'1956',
			1957	=>	'1957',
			1958	=>	'1958',
			1959	=>	'1959',
			1960	=>	'1960',
			1961	=>	'1961',
			1962	=>	'1962',
			1963	=>	'1963',
			1964	=>	'1964',
			1965	=>	'1965',
			1966	=>	'1966',
			1967	=>	'1967',
			1968	=>	'1968',
			1969	=>	'1969',
			1970	=>	'1970',
			1971	=>	'1971',
			1972	=>	'1972',
			1973	=>	'1973',
			1974	=>	'1974',
			1975	=>	'1975',
			1976	=>	'1976',
			1977	=>	'1977',
			1978	=>	'1978',
			1979	=>	'1979',
			1980	=>	'1980',
			1981	=>	'1981',
			1982	=>	'1982',
			1983	=>	'1983',
			1984	=>	'1984',
			1985	=>	'1985',
			1986	=>	'1986',
			1987	=>	'1987',
			1988	=>	'1988',
			1989	=>	'1989',
			1990	=>	'1990',
			1991	=>	'1991',
			1992	=>	'1992',
			1993	=>	'1993',
			1994	=>	'1994',
			1995	=>	'1995',
			1996	=>	'1996',
			1997	=>	'1997',
			1998	=>	'1998',
			1999	=>	'1999',
			2000	=>	'2000',
			2001	=>	'2001',	
			2002	=>	'2002',
			2003	=>	'2002',
			2004	=>	'2003',
			2005	=>	'2004',
			2006	=>	'2005',
			2006	=>	'2006',
			2007	=>	'2007',
			
		);
		
		$months = array
		(
			0		=>	$this->lang['combo_none'],
			1		=>	$this->lang['month_january'],
			2		=>	$this->lang['month_february'],
			3		=>	$this->lang['month_march'],
			4		=>	$this->lang['month_april'],
			5		=>	$this->lang['month_may'],
			6		=>	$this->lang['month_june'],
			7		=>	$this->lang['month_july'],
			8		=>	$this->lang['month_august'],
			9		=>	$this->lang['month_september'],
			10		=>	$this->lang['month_october'],
			11		=>	$this->lang['month_november'],
			12		=>	$this->lang['month_december'],	
			
		);
		
		$form->set_data('user_sex', $sex);
		$form->set_data('user_birth_date_day', $days);
		$form->set_data('user_birth_date_year', $years);
		$form->set_data('user_birth_date_month', $months);										
	}
	
}

?>