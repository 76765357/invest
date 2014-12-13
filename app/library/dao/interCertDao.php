<?php 
class interCertDao extends Dao {
	
	public $table_name = 'inter_cert';
	private $fields = "userid,profession,professioncode,imformation,achieve";
	
	public function add($data) {
		$fields = $this->dao->db->build_key($data, $this->fields);
		return $this->dao->db->insert($fields, $this->table_name);
	}

	public function getOneByField($cond) {
		return $this->dao->db->get_one_by_field($cond,$this->table_name);
	}

	public function update($data,$cond) {
		return $this->dao->db->update_by_field($data, $cond, $this->table_name); //根据条件更新数据
	}
}
