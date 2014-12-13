<?php 
class userTagDao extends Dao {
	
	public $table_name = 'user_tag';
	private $fields = "userid,consult_id";
	
	public function add($data) {
		$fields = $this->dao->db->build_key($data, $this->fields);
		return $this->dao->db->insert($fields, $this->table_name);
	}

	public function get($cond) {
		return $this->dao->db->get_one_by_field($cond, $this->table_name);
	}

	public function getByField($cond) {
		return $this->dao->db->get_all($this->table_name,20,0,$cond);
	}

	public function update($data,$cond) {
		return $this->dao->db->update_by_field($data, $cond, $this->table_name); //根据条件更新数据
	}

	public function getByUserId($userid) {
		return $this->dao->db->get_all_sql("SELECT user_tag.consult_id, consult_type.title FROM user_tag left join consult_type on user_tag.consult_id = consult_type.typeid WHERE user_tag.`userid`=".$userid);
	}

        public function del($cond) {
                return $this->dao->db->delete_by_field($cond,$this->table_name);
        }

}
