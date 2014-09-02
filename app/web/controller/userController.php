<?php
/**
 * userController
 * @author wanglong 
 */
class userController extends Controller {
	
	public $initphp_list = array('reg','log'); //Action白名单

	public function run() {    
		//$this->view->display("index_run"); //展示模板页面
	}
	
	public function reg() {
		$reg = $this->controller->get_gp(array('phone', 'password'));
		$user = $this->controller->get_gp(array('phone'));
		$result = $this->_getUserDao()->getUser($user);
		if($result > 0){
				$return_data = array('success'=>false,'msg','注册失败,原因:该手机号已存在');
		}else{
			$result = $this->_getUserDao()->addUser($reg);
			if ($result > 0) {
				$return_data = array('success'=>true,'userid'=>$result);
			} else {
				$return_data = array('success'=>false,'msg','注册失败,原因:数据库插入错误');
			}
		}
		exit(json_encode($return_data));
	}

	public function log() {
		$user = $this->controller->get_gp(array('phone', 'password'));
		$result = $this->_getUserDao()->getUser($user);
		if ($result > 0) {
			$return_data = array('success'=>true,'userid'=>$result['id']);
		} else {
			$return_data = array('success'=>false);
		}
		exit(json_encode($return_data));
	}
	
	private function _getUserDao() {
		return InitPHP::getDao("user");
	}
} 
