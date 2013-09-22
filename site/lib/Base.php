<?php

class Lib_Base
{
	protected $user_id;
	protected $obj_id;
	protected $obj_type_id;
	protected $obj_type_name;
	protected $db;
	
	public function __construct(common $common, $obj_id, $obj_type_name)
	{
		$this->user_id			= $common->get_user()->get_id();
		$this->obj_id			= $obj_id;
		$this->obj_type_id		= $common->get_object_type_id($obj_type_name);
		$this->obj_type_name	= $obj_type_name;	
		$this->db				= $common->db();
	}
	
	public function get_obj_id()
	{
		return $this->obj_id;
	}
	
	public function add_comment($title, $contents)
	{
		$this->db->insert('comments', array
		(
			'comment_obj_id'		=>	$this->obj_id,
			'comment_user_id'		=>	$this->user_id,
			'comment_obj_type_id'	=>	$this->obj_type_id,
			'comment_title'			=>	$title,
			'comment_contents'		=>	$contents,
			'comment_date'			=>	time()
		));
	}
	
	public function edit_comment($id, $title, $contents)
	{
		$this->db->update('comments', array
		(	
			'comment_title'			=>	$title,
			'comment_contents'		=>	$contents
		), 'comment_id = ? AND comment_obj_id = ? AND comment_obj_type_id = ?', array
		(
			$id, 
			$this->obj_id, 
			$this->obj_type_id
		));
	}
	
	public function delete_comment($id)
	{
		$this->db->delete('comments', 'comment_id = ? AND comment_obj_id = ? AND comment_obj_type_id = ?', array
		(
			$id, 
			$this->obj_id, 
			$this->obj_type_id
		));
	}
	
	public function add_rating($user_id, $rating)
	{
		if ($user_id)
		{
			$this->db->insert('ratings', array
			(
				'rating_user_id'		=>	$user_id,
				'rating_obj_id'			=>	$this->obj_id,
				'rating_obj_type_id'	=>	$this->obj_type_id,
				'rating_value'			=>	$rating
			));
		}
	}
	
	public function edit_rating($user_id, $rating)
	{
		if ($user_id)
		{
			$this->db->update('ratings', array
			(
				'rating_value'	=>	$rating
			), 'rating_user_id = ? AND rating_obj_id = ? AND rating_obj_type_id = ?', array
			(
				$user_id, 
				$this->obj_id, 
				$this->obj_type_id
			));
		}
	}
	
	public function delete_rating()
	{
		$this->db->delete('ratings', 'rating_user_id = ? AND rating_obj_id = ? AND rating_obj_type_id = ?', array
		(
			$this->user_id, 
			$this->obj_id, 
			$this->obj_type_id
		));
	}
	
}

?>
