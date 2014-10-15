<?php 
class userDao extends Dao {
	
	public $table_name = 'user';
	private $fields = "userid,role,name,avatar,password,address,hadservernums,company,phone,position,city,area,fansnum,introduction,serverrank,serverprecent,professionrank,professionprecent";
	//企业用户信息字段
	private $efields = "userid,name,avatar,company,position,city,area,introduction";
	//中介用户信息字段
	private $ifields = "userid,name,avatar,address,hadservernums,company,phone,position,city,fansnum,introduction,serverrank,serverprecent,professionrank,professionprecent";
	
	public function addUser($user) {
		$user = $this->dao->db->build_key($user, $this->fields);
		return $this->dao->db->insert($user, $this->table_name);
	}

	public function getUser($user) {
		return $this->dao->db->get_one_by_field($user, $this->table_name);
	}

	public function update($data,$cond) {
		return $this->dao->db->update_by_field($data, $cond, $this->table_name); //根据条件更新数据
	}

	//获取中介用户信息
	public function getInterUser($userid){
		$cond = array('userid'=>$userid);
		$sql = "SELECT ".$this->ifields." FROM ".$this->table_name.$this->dao->db->build_where($cond);
		return $this->dao->db->get_one_sql($sql);
	}

	//获取企业用户信息
	public function getEnterUser($userid){
		$cond = array('userid'=>$userid);
		$sql = "SELECT ".$this->efields." FROM ".$this->table_name.$this->dao->db->build_where($cond);
		return $this->dao->db->get_one_sql($sql);
	}

	public function getAll($num,$offset,$field){
		return $this->dao->db->get_all($this->table_name,$num,$offset,$field,'userid');
	}

	public function getUserConut($cond){
		return $this->dao->db->get_count($this->table_name,$cond);
	}
}
