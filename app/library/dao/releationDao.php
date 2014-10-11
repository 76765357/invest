<?php 
class releationDao extends Dao {
	
	public $table_name = 'releation';
	private $fields = "id,userid,otheruserid,stat";
	
	public function add($rel) {
		$rel= $this->dao->db->build_key($rel, $this->fields);
		return $this->dao->db->insert($rel, $this->table_name);
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

	public function getByField($cond,$limit=20,$start=0,$id_key='id') {
		return $this->dao->db->get_all($this->table_name,$limit,$start,$cond,$id_key);
	}
}
