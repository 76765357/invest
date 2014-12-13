<?php
/**
 * adminController
 * @author wanglong 
 */


class adminController extends Controller {
	public $initphp_list = array(
		'index',
		'test',
		'iuser_list',
		'euser_list',
		'report_list',
		'add_yewu',
		'do_add_yewu',
		'do_del_yewu',
		'yewu_list',
	); //Action白名单

	private $page_length = 10;

	public function run() {   
		$this->showpage();
	}

	public function showpage(){
		$gp = $this->controller->get_gp(array('a'));
		$this->view->assign('action', $gp['a']);
		#$this->view->assign('action', TEMPLATE_PATH .$gp['a']);
		$this->view->display("admin_index"); //展示模板页面
	}

	public function index() {
		$this->showpage();
	}

	public function iuser_list() {
		$gp = $this->controller->get_gp(array('page'));
		$cond = array('role'=>INTER);
		$page = $gp['page'];
		$pager= $this->getLibrary('pager'); //分页加载
		$total = $this->_getUserDao()->getUserConut($cond);
		$users = $this->_getUserDao()->getAll($this->page_length,($page-1) * $this->page_length,$cond);
		$page_html = $pager->pager($total, $this->page_length, 'index.php?c=admin&a=iuser_list', true); //最后一个参数为true则使用默认样式
		$this->view->assign('page_html', $page_html);
		$this->view->assign('users', $users[0]);
		$this->showpage();
	}

	public function euser_list() {
		$gp = $this->controller->get_gp(array('page'));
		$cond = array('role'=>ENTER);
		$page = $gp['page'];
		$pager= $this->getLibrary('pager'); //分页加载
		$total = $this->_getUserDao()->getUserConut($cond);
		$users = $this->_getUserDao()->getAll($this->page_length,($page-1) * $this->page_length,$cond);
		foreach($users[0] as $k=>$v){
			$cond = array('userid'=>$v['userid']);
			$prj = $this->_getPrjDao()->getByCond($cond);
			if($prj[1] > 0)
				$users[0][$k]['prj'] = $prj[0];	
		}
		$page_html = $pager->pager($total, $this->page_length, 'index.php?c=admin&a=euser_list', true); //最后一个参数为true则使用默认样式
		$this->view->assign('page_html', $page_html);
		$this->view->assign('users', $users[0]);
		$this->showpage();
	}

	public function report_list() {
		$this->showpage();
	}
	
	public function add_yewu() {
		$gp = $this->controller->get_gp(array('id'));
		if($gp['id'] > 0){
			$data = $this->_getConTypeDao()->getOne($gp['id']);
			$this->view->assign('data', $data);
		}
		$this->showpage();
	}

	public function do_add_yewu() {
		$typeid = $this->controller->get_gp(array('typeid'));
		$gp = $this->controller->get_gp(array('title','content','desp'));
		if($typeid['typeid'] > 0){
			$result = $this->_getConTypeDao()->update($gp,$typeid);
		}else{
			$result = $this->_getConTypeDao()->add($gp);
		}
		if ($result > 0) {
			$this->controller->ajax_exit('true','保存成功');
		} else {
			$this->controller->ajax_msg('false','保存失败,原因:数据库错误');
		}
	}

	public function do_del_yewu(){
		$typeid = $this->controller->get_gp(array('typeid'));
		$result = $this->_getConTypeDao()->del($typeid);
		$this->controller->ajax_exit('true','删除成功,请刷新后查看结果');
		if ($result > 0) {
			$this->controller->ajax_exit('true','删除成功');
		} else {
			$this->controller->ajax_msg('false','删除失败,原因:数据库错误');
		}
	}

	public function yewu_list() {
		$yewu= $this->_getConTypeDao()->getAll();
		$this->view->assign('yewu', $yewu[0]);
		$this->showpage();
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
