<?php 

UCMS::load_site_once('lib/Base.php');

class Lib_Tags extends Lib_Base
{
	
	public function add_tags($tags)
	{
		$tags = strtolower(trim($tags));
		$sep_tags = preg_split('/(\s*,\s*|\s)/', $tags);
		$ids = array();
		
		foreach($sep_tags as $word)
		{
			$ids[$word] = 0;	
		}
		
		$stm = $this->db->select('tags', 'tag_id, tag_word', 'tag_word IN (' . $this->db->get_questions(count($ids)) . ')', array_keys($ids));
		
		while ($row = $stm->fetch(PDO::FETCH_ASSOC))
		{
			$ids[$row['tag_word']] = $row['tag_id'];
		}
		
		$pdo = $this->db->get_db_obj();
		$stm = $pdo->prepare('INSERT INTO ' . $this->db->quote_table('tags') . ' (tag_word) VALUES(?)');
		$stm2 = $pdo->prepare('INSERT INTO ' . $this->db->quote_table('tags_refs') . ' (ref_word_id, ref_object_id, ref_object_type_id) VALUES(?, ?, ?)');
		$stm3 = $pdo->prepare('UPDATE ' . $this->db->quote_table('tags') . ' SET tag_count = tag_count + 1 WHERE tag_id = ?');
		
		
		foreach($ids as $word => $id)
		{
			if (!$id)
			{
				$stm->execute(array($word)) or $this->db->db_error($stm);
				$id = $this->db->lastInsertId();
			}
			
			$stm2->execute(array
			(
				$id,
				$this->obj_id,
				$this->obj_type_id
			));
			
			$stm3->execute(array($id));
		}
	
	}
	
	public function edit_tags($tags)
	{
		$this->delete_tags();
		$this->add_tags($tags);
	}
	
	public function delete_tags()
	{
		$this->db->delete('tags_refs', 'ref_object_id = ? AND ref_object_type_id = ?', array
		(
			$this->obj_id,
			$this->obj_type_id
		));	
	}
	
}

?>