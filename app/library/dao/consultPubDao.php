<?php 
class consultPubDao extends Dao {
	
	public $table_name = 'consult_Public';
	private $fields = "id,zxid,businesstype,content,imageurl,userid";
	
	public function add($rank) {
		$rank = $this->dao->db->build_key($rank, $this->fields);
		return $this->dao->db->insert($rank, $this->table_name);
	}

	public function getByField($cond) {
		return $this->dao->db->get_all($this->table_name,20,0,$cond);
	}
}