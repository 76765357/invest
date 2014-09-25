<?php 
class answerDao extends Dao {
	
	public $table_name = 'answer';
	private $fields = "id,zxid,userid,content";
	
	public function add($data) {
		$rel= $this->dao->db->build_key($data, $this->fields);
		return $this->dao->db->insert($data, $this->table_name);
	}

	public function del($cond) {
		return $this->dao->db->delete_by_field($cond,$this->table_name);
	}

	public function update($data,$cond) {
		return $this->dao->db->update_by_field($data, $cond, $this->table_name); //根据条件更新数据
	}

	public function get($cond) {
		return $this->dao->db->get_one_by_field($cond, $this->table_name);
	}

	public function getCnt($cond) {
		return $this->dao->db->get_count($this->table_name, $cond);
	}

	public function getByField($cond) {
		return $this->dao->db->get_all($this->table_name,20,0,$cond);
	}
}
