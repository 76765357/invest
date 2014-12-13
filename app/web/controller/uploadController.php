<?php
/**
 * uploadController
 * @author wangzhiqiang

 */
header("Access-Control-Allow-Origin: *");
require(dirname(__FILE__) ."/../../class/UploadHandler.php");
class uploadController extends Controller {

	public $consult_img;
	
	public $initphp_list = array(
		'single_img',		//单张上次图片
		'single_image', 
	); //Action白名单

	public function __construct(){
 		$this->consult_img = dirname(__FILE__) . '/../../../www/img/consult/';
	}

	public function run() {    
		//$this->view->display("index_run"); //展示模板页面
	}
	
	public function single_img(){
		#$gp = $this->controller->get_gp(array('files[]'));
		list($year,$mon,$day) = explode(" ", date("Y m d", time()));
		$consult_img_url = $this->consult_img.$year."/".$mon."/".$day."/";
		if(!is_dir($consult_img_url)) mkdir($consult_img_url,0777,true);
		$options['upload_dir']=$consult_img_url;
		$options['print_response']=false;
		#$options['upload_url'] = get_full_url() .'/../../../www/img/consult/'.$year."/".$mon."/".$day."/";
		#if(isset($gp['files[]']))
		{
			$upload_handler = new UploadHandler($options);
			$upload_handler=(array)$upload_handler;

			#print_r($upload_handler);
			foreach($upload_handler as $k =>$v)
			{
				echo $k=str(substr($k,2));
				if($k=='image_objects')
				print_r($v);
			}
		}

	}

	public function single_image(){
	#	print_r($_FILES);
		$url=$this->get_full_url();
		
		$recode_FILES=time()."\n".json_encode($_FILES)."\n";
		file_put_contents("/tmp/recode_FILES.txt", $recode_FILES , FILE_APPEND);

                list($year,$mon,$day,$now) = explode(" ", date("Y m d YmdHis", time()));
                $consult_img_url = $this->consult_img.$year."/".$mon."/".$day."/";
                if(!is_dir($consult_img_url)) mkdir($consult_img_url,0777,true);
		$fileinfo=pathinfo($_FILES["file"]["name"]);
		$basename=$now.rand(100,999).".".$fileinfo['extension'];
		$filename=$consult_img_url.$basename;
		$fileurl=$url."/img/consult/".$year."/".$mon."/".$day."/".$basename;


		if ((($_FILES["file"]["type"] == "image/gif")|| ($_FILES["file"]["type"] == "image/jpeg")|| ($_FILES["file"]["type"] == "image/pjpeg"))&& ($_FILES["file"]["size"] < 10000000))
		{
			if ($_FILES["file"]["error"] > 0)
    			{
    				#$this->controller->ajax_msg('false',$_FILES["file"]["error"]);
                                $result['status']="false";
                                $result['result']=$_FILES["file"]["error"];
				echo json_encode($result);
    			}else{

      				move_uploaded_file($_FILES["file"]["tmp_name"], $filename);
				$result['status']="true";
				$result['result']['imageurl']=$fileurl;
				echo json_encode($result);
				$recode=time()."\n".json_encode($result)."\n";
				file_put_contents("/tmp/result.txt", $recode , FILE_APPEND);
				#$this->controller->ajax_exit('true',$result);
			}
		}else{
  			#$this->controller->ajax_msg('false','Invalid file'); 
                        $result['status']="false";
                        $result['result']='Invalid file';
                        echo json_encode($result);
  		}

	}

    protected function get_full_url() {
        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
            substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }

} 


