<?php 
class consultTypeDao extends Dao {
	
	public $table_name = 'consult_type';
	private $fields = "id,name,content";
	
	public function add($consult) {
		$consult = $this->dao->db->build_key($consult, $this->fields);
		return $this->dao->db->insert($consult, $this->table_name);
	}

	public function getAll() {
		return $this->dao->db->get_all($this->table_name);
	}
}
