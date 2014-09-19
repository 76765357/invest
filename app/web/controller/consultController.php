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
								'my',				//我的咨询
								'pub_detail'		//公开咨询详情
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
			$reArr['msgarr'] = array();
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
		$msg = $this->controller->get_gp(array('message'));
		$fromto = $this->controller->get_gp(array('from','to','lastdate'));
		if($msg['message'] !=''){
			$consult = array('userid'=>$fromto['from'],'zxtype'=>MSG_CON);
			$zxid = $this->_getConsultDao()->add($consult);
			if ($zxid > 0) {
				$gp = $msg + $fromto;
				$gp['zxid'] = $zxid;
				$result = $this->_getMsgDao()->add($gp);
				$reArr = array('zxid'=>$result);
				$this->output_pub_msg();
			} else {
				$this->controller->ajax_exit('false');
			}
		}else{
				$this->output_pub_msg();
		}
	}
	
	public function output_pub_msg(){
		$fromto = $this->controller->get_gp(array('from','to','lastdate'));
		$cond = array('from'=>$fromto['from'],'to'=>$fromto['to']);
		$msg = $this->_getMsgDao()->getByField($cond);
		$data = array();
		foreach($msg[0] as $v){
			$data['messagearr'][] = $v;
		}
		$this->controller->ajax_exit('true',$data);
	}

	public function add_pub(){
		$gp = $this->controller->get_gp(array('businesstype', 'content','imagelist','userid'));
		$consult = array('userid'=>$gp['userid'],'zxtype'=>PUB_CON);
		$zxid = $this->_getConsultDao()->add($consult);
		if ($zxid > 0) {
			$gp['zxid'] = $zxid;
			$result = $this->_getPubDao()->add($gp);
			$reArr = array('zxid'=>$result);
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_exit('false');
		}
	}

	public function my(){
		$gp = $this->controller->get_gp(array('userid'));
		$conDao = $this->_getConsultDao();
		$msgDao = $this->_getMsgDao();
		$phoDao = $this->_getPhoneDao();
		$pubDao = $this->_getPubDao();
		$consults = $conDao->getUserConsult($gp['userid']);
		$myConsult = array('result');
		foreach($consults as $k=>$v){
			if($v['zxtype'] == PUB_CON){
				$data = $pubDao->getOnePub($v['zxid']);
				$myConsult['result'][$k]['zxid'] = $v['zxid'];
				$myConsult['result'][$k]['zxtype'] = PUB_CON;
				$myConsult['result'][$k]['question'] = $data['content'];
				//todo
				$myConsult['result']['lastanswerer'] = '';
			}
			if($v['zxtype'] == MSG_CON){
				$data = $msgDao->getOneMsg($v['zxid']);
				$myConsult['result'][$k]['zxid'] = $v['zxid'];
				$myConsult['result'][$k]['zxtype'] = MSG_CON;
				$myConsult['result'][$k]['question'] = $data['message'];
			}
			if($v['zxtype'] == TEL_CON){
				$data = $phoDao->getOneTel($v['zxid']);
				$myConsult['result'][$k]['zxid'] = $v['zxid'];
				$myConsult['result'][$k]['zxtype'] = TEL_CON;
				$myConsult['result'][$k]['date'] = $data['date'];
				$myConsult['result'][$k]['phone'] = $data['phone'];
			}
		}
		$this->controller->ajax_exit('true',$myConsult);
	}

	public function pub_detail(){

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
