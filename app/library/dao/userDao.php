<?php 
class userDao extends Dao {
	
	public $table_name = 'user';
	private $fields = "userid,name,password,address,hadservernums,company,phone,position,fansnum,introduction,serverrank,serverprecent,professionrank,professionprecent,tag";
	
	public function addUser($user) {
		$user = $this->dao->db->build_key($user, $this->fields);
		return $this->dao->db->insert($user, $this->table_name);
	}

	public function getUser($user) {
		return $this->dao->db->get_one_by_field($user, $this->table_name);
	}
}
