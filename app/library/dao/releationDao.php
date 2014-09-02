<?php 
class releationDao extends Dao {
	
	public $table_name = 'releation';
	private $fields = "id,userid,otheruserid,stat";
	
	public function add($rel) {
		$rel= $this->dao->db->build_key($rel, $this->fields);
		return $this->dao->db->insert($rel, $this->table_name);
	}

	public function getAll() {
		return $this->dao->db->get_all($this->table_name);
	}
}
