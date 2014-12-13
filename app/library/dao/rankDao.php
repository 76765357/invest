<?php 
class rankDao extends Dao {
	
	public $table_name = 'rank';
	private $fields = "id,userid,agencyid,starrank,ishelpful,iswell,content";
	
	public function add($rank) {
		$rank = $this->dao->db->build_key($rank, $this->fields);
		return $this->dao->db->insert($rank, $this->table_name);
	}

	public function getByField($cond) {
		return $this->dao->db->get_all($this->table_name,20,0,$cond);
	}
        public function get($cond) {
                return $this->dao->db->get_one_by_field($cond, $this->table_name);
        }
        public function update($data,$cond) {
                return $this->dao->db->update_by_field($data, $cond, $this->table_name);
        }
        public function getBySql($sql){
                return $this->dao->db->get_one_sql($sql);
        }

}
