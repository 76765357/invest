<?php 
class consultDao extends Dao {
	
	public $table_name = 'consult';
	private $fields = "zxid,zxtype,userid,role,from,to,p_time";
	
	public function add($data) {
		$rank = $this->dao->db->build_key($data, $this->fields);
		return $this->dao->db->insert($data, $this->table_name);
	}

	public function getByField($cond,$limit=20,$start=0,$id_key='id') {
		return $this->dao->db->get_all($this->table_name,$limit,$start,$cond,$id_key);
	}
	
	public function getUserConsult($uid){
		//return $this->dao->db->get_all_sql("SELECT max(zxid) as zxid,zxtype,userid FROM `consult` where userid={$uid} group by zxtype");
		return $this->dao->db->get_all_sql("SELECT zxid,zxtype,userid FROM `consult` where userid={$uid} ");
	}

        public function getConsultNew($uid,$limit=20,$start=0){
                return $this->dao->db->get_all_sql("SELECT *  FROM `consult` where `to`={$uid} and zxtype in (1,2) and is_answer=0 order by zxid desc limit $start,$limit");
        }
	
	public function getOneByField($cond) {
		return $this->dao->db->get_one_by_field($cond, $this->table_name);
	}
	public function getCount($cond){
		return $this->dao->db->get_count( $this->table_name,$cond);
	}
}
