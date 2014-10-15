<?php
/**
 * uploadController
 * @author wangzhiqiang

 */
require(dirname(__FILE__) ."/../../class/UploadHandler.php");
class uploadController extends Controller {

	public $consult_img;
	
	public $initphp_list = array(
		'single_img',		//单张上次图片
	); //Action白名单

	public function __construct(){
 		$this->consult_img = dirname(__FILE__) . '/../../../www/img/consult/';
	}

	public function run() {    
		//$this->view->display("index_run"); //展示模板页面
	}
	
	public function single_img(){
		$gp = $this->controller->get_gp(array('files[]'));
		list($year,$mon,$day) = explode(" ", date("Y m d", time()));
		$consult_img_url = $this->consult_img.$year."/".$mon."/".$day."/";
		if(!is_dir($consult_img_url)) mkdir($consult_img_url,0777,true);
		$options['upload_dir']=$consult_img_url;
		if(isset($gp['files[]'])){
			$upload_handler = new UploadHandler($options);
		}
	}

} 
