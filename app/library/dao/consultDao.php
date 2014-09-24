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
	
	public function getUserConsult($uid){
		//return $this->dao->db->get_all_sql("SELECT max(zxid) as zxid,zxtype,userid FROM `consult` where userid={$uid} group by zxtype");
		return $this->dao->db->get_all_sql("SELECT zxid,zxtype,userid FROM `consult` where userid={$uid} ");
	}
	
	public function getOneByField($cond) {
		return $this->dao->db->get_one_by_field($cond, $this->table_name);
	}
}
