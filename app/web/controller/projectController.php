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
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_exit('false','项目添加失败,原因:数据库插入错误');
		}
	}

	//取得项目
	public function get() {

	}
	
	private function _getProjectDao() {
		return InitPHP::getDao("project");
	}
} 
