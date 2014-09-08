<?php
/**
 * consultController
 * @author wanglong 
 */
define('PUB_CON',0);
define('MSG_CON',1);
define('TEL_CON',2);
class consultController extends Controller {
	
	public $initphp_list = array(
								'get_types',		//公开咨询和私密咨询得公用接口
								'get_types_desc',	//咨询业务类型介绍
								'add_phone',		//电话咨询
								'add_msg',			//图文咨询
								'add_pub',			//公开咨询
								'',
								''
							); //Action白名单

	public function run() {    
		//$this->view->display("index_run"); //展示模板页面
	}
	
	public function get_types(){
		$types = $this->_getTypeDao()->getAll();
		$reArr = array();
		if($types[0]){
			$reArr['list'] = array();
			foreach($types[0] as $v){
				unset($v['content']);
				$reArr['list'][] = $v;
			}
			$this->controller->ajax_exit('true',$reArr);
		}else{
			$this->controller->ajax_exit('false');
		}
	}

	public function get_types_desc(){
		$types = $this->_getTypeDao()->getAll();
		$reArr = array();
		if($types[0]){
			$reArr['messagearr'] = array();
			foreach($types[0] as $v){
				$reArr['list'][] = $v;
			}
			$this->controller->ajax_exit('true',$reArr);
		}else{
			$this->controller->ajax_exit('false');
		}
	}

	public function add_phone(){
		$gp = $this->controller->get_gp(array('userid', 'date','phone'));
		$consult = array('userid'=>$gp['userid'],'zxtype'=>TEL_CON);
		$zxid = $this->_getConsultDao()->add($consult);
		if ($zxid > 0) {
			$gp['zxid'] = $zxid;
			$result = $this->_getPhoneDao()->add($gp);
			$reArr = array('zxid'=>$result);
			$this->controller->ajax_exit('true',$reArr);
		} else {
			$this->controller->ajax_exit('false');
		}
	}

	public function add_msg(){
		$gp = $this->controller->get_gp(array('message', 'userid','lastdate'));
		$consult = array('userid'=>$gp['userid'],'zxtype'=>MSG_CON);
		$zxid = $this->_getConsultDao()->add($consult);
		if ($zxid > 0) {
			$gp['zxid'] = $zxid;
			$result = $this->_getMsgDao()->add($gp);
			$reArr = array('zxid'=>$result);
			$this->controller->ajax_exit('true',$reArr);
		} else {
			$this->controller->ajax_exit('false');
		}
	}
	
	public function add_pub(){
		$gp = $this->controller->get_gp(array('businesstype', 'content','imageurl','userid'));
		$consult = array('userid'=>$gp['userid'],'zxtype'=>PUB_CON);
		$zxid = $this->_getConsultDao()->add($consult);
		if ($zxid > 0) {
			$gp['zxid'] = $zxid;
			$result = $this->_getMsgDao()->add($gp);
			$reArr = array('zxid'=>$result);
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_exit('false');
		}
	}

	private function _getConsultDao() {
		return InitPHP::getDao("consult");
	}
	
	private function _getPhoneDao() {
		return InitPHP::getDao("consultPhone");
	}

	private function _getMsgDao() {
		return InitPHP::getDao("consultMsg");
	}

	private function _getPubDao() {
		return InitPHP::getDao("consultPub");
	}

	private function _getTypeDao() {
		return InitPHP::getDao("consultType");
	}
} 