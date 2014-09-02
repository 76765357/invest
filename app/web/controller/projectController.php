<?php
/**
 * projectController
 * @author wanglong 
 */
class projectController extends Controller {
	
	public $initphp_list = array('add','get'); //Action白名单

	public function run() {    
		//$this->view->display("index_run"); //展示模板页面
	}

	//添加项目
	public function add() {
		$project = $this->controller->get_gp(array('userid', 'name','introduction','bestside','bestside','isoutside'));
		$result = $this->_getProjectDao()->add($project);
		if ($result > 0) {
			$return_data = array('success'=>true);
		} else {
			$return_data = array('success'=>false,'msg','项目添加失败,原因:数据库插入错误');
		}
		exit(json_encode($return_data));
	}

	//取得项目
	public function get() {

	}
	
	private function _getProjectDao() {
		return InitPHP::getDao("project");
	}
} 
