<?php
/**
 * userController
 * @author wanglong 
 */

define('INTER','intermediary');	//中介
define('ENTER','enterprise');	//企业

class userController extends Controller {
	
	public $initphp_list = array('eregister',
		'iregister',
		'login',
		'get_inter_user',//中介信息
		'get_enter_user',//企业用户信息
		'mod_info',
		'mod_intro',
		'set_rank',	//给评价
		'get_rank',	//拿评价
		'attention',//加或者取消关注
		'all_atten' //全部关注
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
			$result['beattentionnum'] = '0';
			$result['attentionnum'] = '20';
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
			print_r($tags);
			if(count($tags) > 0) foreach($tags as $v){
				$result['tag'][] = array('typeid'=>$v['consult_id'],'title'=>$v['title']);
			}
			print_r($result);
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
		$data = $this->controller->get_gp(array('name', 'company', 'postion', 'city', 'area'));
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
