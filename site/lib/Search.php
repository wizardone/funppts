<?php 

class Search 
{

	private $db;
	
	public function __construct(common $common)
	{
		$this->db = $common->db();
	}
	
	public function search_user($params)
	{
		$db = $this->db;
		
		$query = 'SELECT * FROM ' . $db->quote_table('users');
	
		if (count($params) > 0) 
		{
			$query .= ' WHERE ';
		}
		
		$queries = array();
		
		foreach($params as $key => $value)
		{
			
			if ($key == 'first_name')
			{
				$queries[] = ' LOWER(user_first_name) = ' . $db->quote(strtolower($value));
			}
			
			if ($key == 'last_name')
			{
				$queries[] = ' LOWER(user_last_name) = ' . $db->quote(strtolower($value));
			}
			
			if ($key == 'sex')
			{
				$queries[] = ' user_sex = ' . $value;
			}
			
			if (($key == 'has_birthday') && ($value == 1))
			{
				$queries[] = 'DAY(FROM_UNIXTIME(user_birth_date)) = ' . $db->quote(date("d"));
				$queries[] = 'MONTH(FROM_UNIXTIME(user_birth_date)) = ' . $db->quote(date("m"));
			}
			
			
			if ($key == 'age_from')
			{
				$queries[] = 'YEAR(FROM_DAYS(DATEDIFF(' . $db->quote(date("Y-m-d")) . ', STR_TO_DATE(CONCAT(DAY(FROM_UNIXTIME(user_birth_date)), "/", MONTH(FROM_UNIXTIME(user_birth_date)), "/", YEAR(FROM_UNIXTIME(user_birth_date))), "%d/%m/%Y")))) >= ' . $value;
			}
			
			if ($key == 'age_to')
			
			{
				$queries[] = 'YEAR(FROM_DAYS(DATEDIFF(' . $db->quote(date("Y-m-d")) . ', STR_TO_DATE(CONCAT(DAY(FROM_UNIXTIME(user_birth_date)), "/", MONTH(FROM_UNIXTIME(user_birth_date)), "/", YEAR(FROM_UNIXTIME(user_birth_date))), "%d/%m/%Y")))) <= ' . $value;
			}
						
			if ($key == 'has_birthday_in')
			{
				$queries[] = '(DATEDIFF(DATE_ADD(' . $db->quote(date("Y-m-d")) . ', INTERVAL ' . $value . '), STR_TO_DATE(CONCAT(DAY(FROM_UNIXTIME(user_birth_date)), "/", MONTH(FROM_UNIXTIME(user_birth_date)), "/",' . $db->quote(date("Y")) .'), "%d/%m/%Y")) >= 0 )';
				$queries[] = '(DATEDIFF(' . $db->quote(date("Y-m-d")) . ', STR_TO_DATE(CONCAT(DAY(FROM_UNIXTIME(user_birth_date)), "/", MONTH(FROM_UNIXTIME(user_birth_date)), "/",' . $db->quote(date("Y")) . '), "%d/%m/%Y")) <= 0 )';
			}
				
			if (($key == 'has_avatar') && ($value == 1))
			{
				$queries[] = ' user_avatar != "" ';
			}
			
			if ($key == 'interests')
			{
				$sep_interests = preg_split('/(\s*,\s*|\s)/', $value);
				
				$or_queries = array();
				
				foreach($sep_interests as $interest)
				{		
					$or_queries[] = 'LOWER(user_interests) LIKE ' . $db->quote('%' . strtolower($interest) . '%') ; 
				}
				
				$queries[] = ' ( ' . implode(' OR ', $or_queries) . ' ) ';
			}
			
			
			
		}
		$query .= implode(' AND ', $queries);
		
		
		$stm = $db->query($query);
		
		if ($stm->rowCount() > 0 ) 
		{
			return $stm->rowCount();
		}
		else 
		{
			return false;
		}
	}
	
	public function search_tag($tag)
	{
		$db = $this->db;
		
		$query = 'SELECT ppts.* FROM ' . $db->quote_table('ppts') . 'as ppts, ' . $db->quote('tags_refs') . 'as tags_refs WHERE ';
		$query .= ' (ppts.ppt_id = tags_refs.ref_object_id) AND (tags_refs.ref_object_id = ' . $tag . ') '; 
		
		$stm = $db->query($query);
		$rows = array();
		
		while ($row = $stm->fetch(PDO::FETCH_ASSOC))
		{
			$rows[] = $row;
		}
		
		return $rows;
		
		
	}
	
	
	public function most_popular($limit = 5, $category = null, $tag = null)
	{
			$db = $this->db;
			
			$query = 'SELECT ppt.* FROM ' . $db->quote_table('ppts') . ' as ppt, ' . $db->quote_table('tags_refs') . ' as tags_refs, ' . $db->quote_table('ratings') . ' as ratings';
		
			if (($category + $tag) >= 0)
			{
				$query .= ' WHERE ';
				$queries = array();
				
				if ($category != null) 
				{
					$queries[] = ' ppt_category = ' . $category;
				}
				
				if ($tag != null)
				{
					$queries[] = 'ppt.ppt_id = tags_refs.ref_object_id ';
					$queries[] = 'tags_refs.ref_word_id = ' . $tag;
				}
				
				$query .= implode(' AND ', $queries);
			}
			
			$query .= 'ORDER BY ratings.rating_value DESC LIMIT ' . $limit;
			$stm = $db->query($query);
			
			$rows = array();
			
			while ($row = $stm->fetch(PDO::FETCH_ASSOC))
			{
				$rows[] = $row;
			}
			
			return $rows;
	}
	
}

?>