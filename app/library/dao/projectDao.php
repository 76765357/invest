<?php 
class projectDao extends Dao {
	
	public $table_name = 'project';
	private $fields = "id,userid,name,introduction,bestside,city,member,isoutside";
	
	public function add($project) {
		$project= $this->dao->db->build_key($project, $this->fields);
		return $this->dao->db->insert($project, $this->table_name);
	}

	public function getAll() {
		return $this->dao->db->get_all($this->table_name);
	}

	public function getByCond($cond) {
		return $this->dao->db->get_all($this->table_name,100,0,$cond);
	}
}
