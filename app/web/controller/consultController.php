<?php
/**
 * consultController
 * @author wanglong 
define('PUB_CON',0);
define('MSG_CON',1);
define('TEL_CON',2);
 */
class consultController extends Controller {
	
	public $initphp_list = array(
		'get_types',		//公开咨询和私密咨询得公用接口
		'get_types_desc',	//咨询业务类型介绍
		'add_phone',		//电话咨询
		'add_msg',			//图文咨询
		'add_pub',			//公开咨询
		'my',				//我的咨询
		'pub_detail',		//公开咨询详情
		//index.php?c=consult&a=add_answer&userid=1&zxid=1&content=1haoshouzhang
		'add_answer',		//添加公开咨询回答
		'consult_list',           //咨询列表
		'consult_public',	//公开咨询列表
		'consult_new',		//最新收到的咨询
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
		$gp = $this->controller->get_gp(array('userid','to', 'date','phone'));
		$role=$this->controller->get_gp(array('role'));
                if($role['role']=='qy')
                {
                        $role['role']=1;
                }elseif($role['role']=='zj'){
                        $role['role']=2;
                }
		$consult = array('userid'=>$gp['userid'],'from'=>$gp['userid'],'to'=>$gp['to'],'role'=>$role['role'],'zxtype'=>TEL_CON,'p_time'=>time());
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
                $gp=$this->controller->get_gp(array('role'));
		if($gp['role']=='qy')
                {
                        $gp['role']=1;
                }elseif($gp['role']=='zj'){
                        $gp['role']=2;
                }
		if($msg['message'] !=''){
			$consult = array('userid'=>$fromto['from'],'from'=>$fromto['from'],'to'=>$fromto['to'],'role'=>$gp['role'],'zxtype'=>MSG_CON,'p_time'=>time());
			print_r($consult);
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
		$gp = $this->controller->get_gp(array('businesstype', 'content','imagelist','userid','role'));
		if($gp['role']=='qy')
		{
			$gp['role']=1;
		}elseif($gp['role']=='zj'){
			$gp['role']=2;
		}
		$consult = array('userid'=>$gp['userid'],'role'=>$gp['role'],'from'=>$gp['userid'],'zxtype'=>PUB_CON,'p_time'=>time());
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

	public function consult_public(){
                $r_info = $this->controller->get_gp(array('type','page','pagenum'));
                if($r_info['page']<=0) $r_info['page']=1;
                if($r_info['pagenum']<=0) $r_info['pagenum']=20;
                $page_start= ($r_info['page']-1)*$r_info['pagenum'];
		if($r_info['type'] ) $cond = array('businesstype'=>$r_info['type']);
		$consult_list=$this->_getPubDao()->getByField($cond,$r_info['pagenum'],$page_start);
		#print_r($consult_list);
		foreach($consult_list[0] as $k => $v)
		{
			$consults['data'][$k]['zxid']=$v['zxid'];
			$consults['data'][$k]['zxtype']=PUB_CON;
			$consults['data'][$k]['qustion']=$v['content'];
			$cond = array('userid'=>$v['userid']);

                        $otheruser =  $this->_getUserDao()->getUser($cond);
                        $consults['data'][$k]['name'] = $otheruser['name'];
                        $consults['data'][$k]['company'] = $otheruser['company'];
                        $consults['data'][$k]['position'] = $otheruser['position'];
                        $consults['data'][$k]['imageurl'] = $otheruser['avatar'];
                        $count_cond = array('zxid'=>$v['zxid']);
                        $counts=$this->_getAnsDao()->getCnt($count_cond);
                        #print_r($counts);
                        $consults['data'][$k]['hadanswernum'] =$counts;

		}
                if(empty($consults['data'])){
                        $this->controller->ajax_msg('false','没有数据');
                }else{
                        $this->controller->ajax_exit('true',$consults);
                }
	
	}

	public function consult_new(){
		$r_info = $this->controller->get_gp(array('userid','page','pagenum'));
		if($r_info['page']<=0) $r_info['page']=1;
		if($r_info['pagenum']<=0) $r_info['pagenum']=20;
		$page_start= ($r_info['page']-1)*$r_info['pagenum'];
		$consult_new=$this->_getConsultDao()->getConsultNew($r_info['userid'],$r_info['pagenum'],$page_start);
		#print_r($consult_new);
		$Consult_n['pagenum']=$r_info['pagenum'];
                $Consult_n['page']=$r_info['page'];
                $msgDao = $this->_getMsgDao();
                $phoDao = $this->_getPhoneDao();
		foreach($consult_new as $k => $v)
		{
			if($v['zxtype'] == MSG_CON){
                                $data = $msgDao->getOneMsg($v['zxid']);
                                $Consult_n['data'][$k]['zxid'] = $v['zxid'];
                                $Consult_n['data'][$k]['zxtype'] = MSG_CON;
                                $Consult_n['data'][$k]['question'] = $data['message'];
                        }
                        if($v['zxtype'] == TEL_CON){
                                $data = $phoDao->getOneTel($v['zxid']);
                                $Consult_n['data'][$k]['zxid'] = $v['zxid'];
                                $Consult_n['data'][$k]['zxtype'] = TEL_CON;
                                $Consult_n['data'][$k]['servertime'] = $data['date'];
                                $Consult_n['data'][$k]['phone'] = $data['phone'];
                        }
			$p_time=date('Y-m-d',$v['p_time']);
			$Consult_n['data'][$k]['publishtime'] = $p_time;
			$cond = array('userid'=>$v['userid']);
			$otheruser =  $this->_getUserDao()->getUser($cond);
                        $Consult_n['data'][$k]['name'] = $otheruser['name'];
                        $Consult_n['data'][$k]['company'] = $otheruser['company'];
                        $Consult_n['data'][$k]['position'] = $otheruser['position'];
			$Consult_n['data'][$k]['imageurl'] = $otheruser['avatar'];
		}
                if(empty($Consult_n['data'])){
                        $this->controller->ajax_msg('false','没有数据');
                }else{
                        $this->controller->ajax_exit('true',$Consult_n);
                }
		
	}


	public function consult_list(){
		$r_info = $this->controller->get_gp(array('zxtype','page','pagenum'));
		if($r_info['page']<=0) $r_info['page']=1;
		if($r_info['pagenum']<=0) $r_info['pagenum']=20;
		$page_start= ($r_info['page']-1)*$r_info['pagenum'];
		$pubDao = $this->_getPubDao();
		if(!isset($r_info['zxtype'])) $this->controller->ajax_msg('false','没有咨询类型');
		$cond = array('zxtype'=>$r_info['zxtype']);
		$consult_list=$this->_getConsultDao()->getByField($cond,$r_info['pagenum'],$page_start,'zxid');
		if($r_info['zxtype']==PUB_CON)
		{
			$consults['pagenum']=$r_info['pagenum'];
			$consults['page']=$r_info['page'];
			foreach($consult_list[0] as $k => $v)
			{
				$data = $pubDao->getOnePub($v['zxid']);
				$consults['data'][$k]['zxid']=$v['zxid'];
				$consults['data'][$k]['zxtype']=$v['zxtype'];
				$consults['data'][$k]['qustion']=($v['content']!='')?$v['content']:'';
				$cond = array('userid'=>$v['userid']);
				$otheruser =  $this->_getUserDao()->getUser($cond);
				if($otheruser){
					$consults['data'][$k]['name'] = $otheruser['name'];
					$consults['data'][$k]['company'] = $otheruser['company'];
					$consults['data'][$k]['position'] = $otheruser['position'];
					$consults['data'][$k]['imageurl'] = $otheruser['avatar'];
				}
				$count_cond = array('zxid'=>$v['zxid']);
				$counts=$this->_getAnsDao()->getCnt($count_cond);
				#print_r($counts);
				$consults['data'][$k]['hadanswernum'] =$counts;
			}
		}
		if(empty($consults['data'])){
			$this->controller->ajax_msg('false','没有数据');
		}else{
			$this->controller->ajax_exit('true',$consults);
		}
	}

	public function my(){
		$gp = $this->controller->get_gp(array('userid'));
		$conDao = $this->_getConsultDao();
		$msgDao = $this->_getMsgDao();
		$phoDao = $this->_getPhoneDao();
		$pubDao = $this->_getPubDao();
		$consults = $conDao->getUserConsult($gp['userid']);
		foreach($consults as $k=>$v){
			if($v['zxtype'] == PUB_CON){
				$data = $pubDao->getOnePub($v['zxid']);
				$myConsult['listArr'][$k]['zxid'] = $v['zxid'];
				$myConsult['listArr'][$k]['zxtype'] = PUB_CON;
				$myConsult['listArr'][$k]['question'] = $data['content'];
				//todo
				$myConsult['listArr'][$k]['lastanswerer'] = '';
			}
			if($v['zxtype'] == MSG_CON){
				$data = $msgDao->getOneMsg($v['zxid']);
				$myConsult['listArr'][$k]['zxid'] = $v['zxid'];
				$myConsult['listArr'][$k]['zxtype'] = MSG_CON;
				$myConsult['listArr'][$k]['question'] = $data['message'];
			}
			if($v['zxtype'] == TEL_CON){
				$data = $phoDao->getOneTel($v['zxid']);
				$myConsult['listArr'][$k]['zxid'] = $v['zxid'];
				$myConsult['listArr'][$k]['zxtype'] = TEL_CON;
				$myConsult['listArr'][$k]['date'] = $data['date'];
				$myConsult['listArr'][$k]['phone'] = $data['phone'];
			}
		}
		if(empty($myConsult['listArr'])){
			$this->controller->ajax_msg('false','没有数据');
		}else{
			$this->controller->ajax_exit('true',$myConsult);
		}
	}

	public function pub_detail(){
		$gp = $this->controller->get_gp(array('userid','zxid'));
		$zxid = $gp['zxid'];
		$zx = $this->_getConsultDao()->getOneByField($gp);
		$pub = $this->_getPubDao()->getOnePub($zxid);
		if($zx && $zx['zxtype'] == PUB_CON){
			$answerarr = array();
			$ans = $this->_getAnsDao()->getByField(array('zxid'=>$zxid));
			if($ans[0])foreach($ans[0] as $k=>$v){
				$cond = array('userid'=>$v['userid']);
				$otheruser =  $this->_getUserDao()->getUser($cond);
				$answerarr[$k]['usertype'] = $otheruser['role']; 
				$answerarr[$k]['userid'] = $v['userid']; 
				$answerarr[$k]['name'] = $otheruser['name']; 
				$answerarr[$k]['company'] = $otheruser['company']; 
				$answerarr[$k]['city'] = $otheruser['city']; 
				$answerarr[$k]['area'] = $otheruser['area']; 
				$answerarr[$k]['avatar'] = $otheruser['avatar']; 
				if($otheruser['role'] == INTER){
					$answerarr[$k]['hadservernums'] = $otheruser['hadservernums']; 
				}
				$answerarr[$k]['answer'] = $v['content'];
			}
			$this->controller->ajax_exit('true',array('zxid'=>$gp['zxid'],'question'=>$pub['content'],'answerarr'=>$answerarr));
		}else{
			$this->controller->ajax_msg('false','非公开类咨询');
		}
	}

	public function add_answer(){
		$gp = $this->controller->get_gp(array('userid','zxid','content'));
		$ans = $this->_getAnsDao()->add($gp);
		if(!$ans){
			$this->controller->ajax_msg('false','插入失败');
		}else{
			$this->controller->ajax_exit('true');
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

	private function _getAnsDao() {
		return InitPHP::getDao("answer");
	}

	private function _getUserDao() {
		return InitPHP::getDao("user");
	}
} 

