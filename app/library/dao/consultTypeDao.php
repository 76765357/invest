<?php 
class consultTypeDao extends Dao {
	
	public $table_name = 'consult_type';
	private $fields = "title,content,desp";
	
	public function add($consult) {
		$consult = $this->dao->db->build_key($consult, $this->fields);
		return $this->dao->db->insert($consult, $this->table_name);
	}

	public function getAll() {
		return $this->dao->db->get_all($this->table_name, 100, 0, array(), 'typeid', 'DESC');
	}

	public function getOne($typeid) {
		return $this->dao->db->get_one($typeid, $this->table_name, 'typeid');
	}

	public function update($data,$cond) {
		return $this->dao->db->update_by_field($data, $cond, $this->table_name); //根据条件更新数据
	}

	public function del($cond) {
		$this->dao->db->delete_by_field($cond, $this->table_name);
	}
}
