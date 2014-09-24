<?php
/**
 * userController
 * @author wanglong 
	define('ENTER','1');	//企业
	define('INTER','2');	//中介
 */


class userController extends Controller {
	
	public $initphp_list = array(
		'eregister',	//企业用户注册
		'iregister',	//中介用户注册
		'login',		//登陆
		'get_inter_user',//中介信息
		'get_enter_user',//企业用户信息
		'mod_info',		//修改用户信息
		'mod_intro',	//修改自己的简介
		'set_rank',	//给评价
		'get_rank',	//拿评价
		'attention',//加或者取消关注
		'all_atten', //全部关注
		'get_inter_user_list', //根据咨询类型获得中介列表
	); //Action白名单

	public function run() {    
		//$this->view->display("index_run"); //展示模板页面
	}
	
	public function eregister() {
		$reg = $this->controller->get_gp(array('phone', 'password'));
		$reg['role'] = ENTER; 
		$user = $this->controller->get_gp(array('phone'));
		$result = $this->_getUserDao()->getUser($user);
		$this->register($result,$reg);
	}

	public function iregister() {
		$reg = $this->controller->get_gp(array('phone', 'password'));
		$reg['role'] = INTER;
		$user = $this->controller->get_gp(array('phone'));
		$result = $this->_getUserDao()->getUser($user);
		$this->register($result,$reg);
	}

	public function register($result,$reg) {
		if($result > 0){
				$this->controller->ajax_msg('false','注册失败,原因:该手机号已存在');
		}else{
			$result = $this->_getUserDao()->addUser($reg);
			if ($result > 0) {
				$this->controller->ajax_exit('true',array('userid'=>$result));
			} else {
				$this->controller->ajax_msg('false','注册失败,原因:数据库插入错误');
			}
		}
	}

	public function login() {
		$user = $this->controller->get_gp(array('phone', 'password'));
		$result = $this->_getUserDao()->getUser($user);
		if ($result > 0) {
			$this->controller->ajax_exit('true',array('userid'=>$result['userid']));
		} else {
			$this->controller->ajax_msg('false','登陆失败');
		}
	}
	
	public function get_enter_user() {
		$user = $this->controller->get_gp(array('userid'));
		$result = $this->_getUserDao()->getEnterUser($user['userid']);
		if ($result) {
			$rCond = array('userid'=>$user['userid'],'stat'=>'1');
			$rbCond = array('otheruserid'=>$user['userid'],'stat'=>'1');
			$result['beattentionnum'] = $this->_getRealeationDao()->getCnt($rbCond);
			$result['attentionnum'] = $this->_getRealeationDao()->getCnt($rCond);
			$proj = $this->_getPrjDao()->getByCond(array('userid'=>$user['userid']));
			if($proj[0] > 0) foreach($proj[0] as $v){
				$result['mineapp'][] = array('name'=>$v['name'],'appid'=>$v['id']);
			}
			$this->controller->ajax_exit('true',$result);
		} else {
			$this->controller->ajax_msg('false','失败,原因:没有数据');
		}
	}

	public function get_inter_user() {
		$user = $this->controller->get_gp(array('userid'));
		$result = $this->_getUserDao()->getInterUser($user['userid']);
		if ($result) {
			$tags = $this->_getTagDao()->getByUserId($user['userid']);
			$rbCond = array('otheruserid'=>$user['userid'],'stat'=>'1');
			$result['fansnum'] = $this->_getRealeationDao()->getCnt($rbCond);
			if(count($tags) > 0) foreach($tags as $v){
				$result['tag'][] = array('typeid'=>$v['consult_id'],'title'=>$v['title']);
			}
			$this->controller->ajax_exit('true',$result);
		} else {
			$this->controller->ajax_msg('false','失败,原因:没有数据');
		}
	}

	//加关注以及取消关注
	public function attention(){
		$att = $this->controller->get_gp(array('userid','otheruserid'));
		$record = $this->_getRealeationDao()->get($att);
		if($record && $record['stat'] == 1){
			$result = $this->_getRealeationDao()->update(array('stat'=>0),$att);
		}else if($record && $record['stat'] == 0){
			$result = $this->_getRealeationDao()->update(array('stat'=>1),$att);
		}else{
			$att['stat'] = 1;//关注:1
			$result = $this->_getRealeationDao()->add($att);
		}
		if ($result > 0) {
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_msg('false','注册失败,原因:数据库错误');
		}
	}

	//用户信息编辑
	public function mod_info(){
		$cond= $this->controller->get_gp(array('userid'));
		if(!$cond['userid']) {
			$this->controller->ajax_msg('false','修改失败,原因:没有userid参数');
		}
		$data = $this->controller->get_gp(array('name', 'company', 'position', 'city', 'area','introduction'));
		$result = $this->_getUserDao()->update($data,$cond);
		if ($result > 0) {
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_msg('false','修改失败,原因:数据库错误');
		}
	}

	//修改简介
	public function mod_intro() {
		$cond = $this->controller->get_gp(array('userid'));
		$data = $this->controller->get_gp(array('introduction'));
		$result = $this->_getUserDao()->update($data,$cond);
		if ($result > 0) {
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_msg('false','修改失败,原因:数据库错误');
		}
	}

	public function set_rank(){
		//$result = $this->_getRankDao();
	}

	public function get_rank(){

	}

	public function all_atten(){
		$cond = $this->controller->get_gp(array('userid'));
		$data = $this->_getRealeationDao()->getByField($cond);
		$members = array();
		if(!empty($data[0]))foreach($data[0] as $k=>$v){
			$cond = array('userid'=>$v['otheruserid']);
			$otheruser =  $this->_getUserDao()->getUser($cond);
			$members[$k]['userid'] = $otheruser['userid']; 
			$members[$k]['name'] = $otheruser['name']; 
			$members[$k]['company'] = $otheruser['company']; 
			$members[$k]['city'] = $otheruser['city']; 
			$members[$k]['area'] = $otheruser['area']; 
			$members[$k]['avatar'] = $otheruser['avatar']; 
			$members[$k]['hadservernums'] = $otheruser['hadservernums']; 
			$userTag = $this->_getTagDao()->getByUserId($v['otheruserid']);
			if($userTag) foreach($userTag as $ut){
				$members[$k]['tag'][] = array(
					"typeid" => $ut['consult_id'],
					"title" => $ut['title'],
				);
			}
		}
		$this->controller->ajax_exit('true',array('members'=>$members));
	}

	public function get_inter_user_list(){
		$cond = $this->controller->get_gp(array('typeid'));
		$cond = array('consult_id'=>$cond['typeid']);
		$data = $this->_getTagDao()->getByField($cond);
		$members = array();
		if(!empty($data[0]))foreach($data[0] as $k=>$v){
			$cond = array('userid'=>$v['userid']);
			$otheruser =  $this->_getUserDao()->getUser($cond);
			$members[$k]['userid'] = $otheruser['userid']; 
			$members[$k]['name'] = $otheruser['name']; 
			$members[$k]['company'] = $otheruser['company']; 
			$members[$k]['city'] = $otheruser['city']; 
			$members[$k]['area'] = $otheruser['area']; 
			$members[$k]['avatar'] = $otheruser['avatar']; 
			$members[$k]['hadservernums'] = $otheruser['hadservernums']; 
			$userTag = $this->_getTagDao()->getByUserId($v['userid']);
			if($userTag) foreach($userTag as $ut){
				$members[$k]['tag'][] = array(
					"typeid" => $ut['consult_id'],
					"title" => $ut['title'],
				);
			}
		}
		$this->controller->ajax_exit('true',array('members'=>$members));
	}

	private function _getUserDao() {
		return InitPHP::getDao("user");
	}

	private function _getConTypeDao() {
		return InitPHP::getDao("consultType");
	}

	private function _getRealeationDao() {
		return InitPHP::getDao("releation");
	}

	private function _getRankDao() {
		return InitPHP::getDao("rank");
	}

	private function _getPrjDao() {
		return InitPHP::getDao("project");
	}

	private function _getTagDao() {
		return InitPHP::getDao("userTag");
	}
} 
