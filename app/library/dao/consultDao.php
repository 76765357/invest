<?php 
class consultDao extends Dao {
	
	public $table_name = 'consult';
	private $fields = "zxid,zxtype,userid";
	
	public function add($data) {
		$rank = $this->dao->db->build_key($data, $this->fields);
		return $this->dao->db->insert($data, $this->table_name);
	}

	public function getByField($cond) {
		return $this->dao->db->get_all($this->table_name,20,0,$cond);
	}
}
