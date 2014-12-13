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
		'get_types_detail',
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
		'new_answer',		//最新收到的回复
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


        public function get_types_detail(){
                $gp = $this->controller->get_gp(array('typeid'));
		$result = $this->_getTypeDao()->getOne($gp['typeid']);
		#print_r($result);
		$reArr = array();
		if($result)
		{
			$reArr['detail'] = $result['desp'];
			$this->controller->ajax_exit('true',$reArr);
		}else{
			$this->controller->ajax_exit('false');
		}
		/*
                if($types[0]){
                        $reArr['msgarr'] = array();
                        foreach($types[0] as $v){
                                $reArr['list'][] = $v;
                        }
                        $this->controller->ajax_exit('true',$reArr);
                }else{
                        $this->controller->ajax_exit('false');
                }
		*/
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
                $imglist = $this->controller->get_gp(array('imgurl'));
                $fromto = $this->controller->get_gp(array('userid','guyid','lasttime'));
		$zxid = $this->controller->get_gp(array('zxid'));
                $gp=$this->controller->get_gp(array('role'));
		$now=time();
                if($gp['role']=='qy')
                {
                        $gp['role']=1;
                }elseif($gp['role']=='zj'){
                        $gp['role']=2;
                }
		if(empty($msg['message']) && empty($imglist['imgurl'])){
			//list
			$list_m = $this->_getMsgDao()->getMsgByZxid($zxid['zxid'],$fromto['lasttime']);
			//print_r($list_m);
			$list_a = $this->_getAnsDao()->getAnsByZxid($zxid['zxid'],$fromto['lasttime']);
			if( $list_m[0]['imagelist']!='' or $list_m[0]['message']!='')
			{
				$tt=array('date'=>$list_m[0]['lastdate'],'from'=>$list_m[0]['from'],'imgurl'=>$list_m[0]['imagelist'],'message'=>$list_m[0]['message'],'to'=>$list_m[0]['to']);
				$list[]=$tt;
			}
			$tt=array();
			foreach($list_a as $v)
			{
				if($v['imagelist']!='' or $v['content']!='')
				{
					$tt=array('date'=>$v['p_time'],'from'=>$v['userid'],'imgurl'=>$v['imagelist'],'message'=>$v['content'],'to'=>$v['to_id']);
					$list[]=$tt;
				}
			}
			foreach($list as $kk =>$vv)
			{
				$cond = array('userid'=>$vv['from']);
				$userinfo =  $this->_getUserDao()->getUser($cond);
				$list[$kk]['avatar']=$userinfo['avatar'];
			}
                        if(!$list){
                        	$this->controller->ajax_msg('false','没有数据');
                        }else{
                                $reArr = array('messagearr'=>$list);
                                $this->controller->ajax_exit('true',$reArr);
                        }
				
		}else{
			if($zxid['zxid']>0)
			{
				$answer = array('zxid'=>$zxid['zxid'],'userid'=>$fromto['userid'],'to_id'=>$fromto['guyid'],'content'=>$msg['message'],'imagelist'=>$imglist['imgurl'],'p_time'=>$now);
                		$ans = $this->_getAnsDao()->add($answer);
                		if(!$ans){
                        	$this->controller->ajax_msg('false','插入失败');
                		}else{
                        		$reArr = array('zxid'=>$zxid['zxid'],'time'=>$now);
					$this->controller->ajax_exit('true',$reArr);
                		}
				//duihua
			}else{
				//add msg
				$consult = array('userid'=>$fromto['userid'],'from'=>$fromto['userid'],'to'=>$fromto['guyid'],'role'=>$gp['role'],'zxtype'=>MSG_CON,'p_time'=>$now);
                        	$zxid = $this->_getConsultDao()->add($consult);
                        	if ($zxid > 0) {
                                	$a_items = array('zxid'=>$zxid,'message'=>$msg['message'],'imagelist'=>$imglist['imgurl'],'from'=>$fromto['userid'],'to'=>$fromto['guyid'],'lastdate'=>$now);
                                	
                                	$result = $this->_getMsgDao()->add($a_items);
                                	if($result){
                        			$reArr = array('zxid'=>$zxid,'time'=>$now);
                        			$this->controller->ajax_exit('true',$reArr);
					}
                               
                        	} else {
                                	$this->controller->ajax_exit('false');
                        	}
			}
		}


	}

	public function add_msg_bak(){
		$msg = $this->controller->get_gp(array('message'));
		$imglist = $this->controller->get_gp(array('imglist'));
		$fromto = $this->controller->get_gp(array('from','to','lastdate'));
                $gp=$this->controller->get_gp(array('role'));
		if($gp['role']=='qy')
                {
                        $gp['role']=1;
                }elseif($gp['role']=='zj'){
                        $gp['role']=2;
                }
		if($msg['message'] =='') $msg['message'] =$imglist['imglist'];
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
			$gp['imageurl'] = $gp['imagelist'];
			$result = $this->_getPubDao()->add($gp);
			$reArr = array('zxid'=>$result);
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_exit('false');
		}
	}

	public function consult_public(){
                $r_info = $this->controller->get_gp(array('type','page','pagenum'));
		$userid = $this->controller->get_gp(array('userid'));
                if($r_info['page']<=0) $r_info['page']=1;
                if($r_info['pagenum']<=0) $r_info['pagenum']=20;
                $page_start= ($r_info['page']-1)*$r_info['pagenum'];
		if($r_info['type'] ) $cond = array('businesstype'=>$r_info['type']);
		$consult_list=$this->_getPubDao()->getByField($cond,$r_info['pagenum'],$page_start);
		#print_r($consult_list);
		$k=0;
		foreach($consult_list[0] as $v)
		{
			if($userid['userid'] and $userid['userid']==$v['userid']) continue;
			$consults['data'][$k]['zxid']=$v['zxid'];
			$consults['data'][$k]['zxtype']=PUB_CON;
			$consults['data'][$k]['question']=$v['content'];
			$consults['data'][$k]['imglist']=$v['imageurl'];
			$cond = array('userid'=>$v['userid']);

                        $otheruser =  $this->_getUserDao()->getUser($cond);
			$consults['data'][$k]['userid'] = $v['userid'];
                        $consults['data'][$k]['name'] = $otheruser['name'];
                        $consults['data'][$k]['company'] = $otheruser['company'];
                        $consults['data'][$k]['position'] = $otheruser['position'];
                        $consults['data'][$k]['imageurl'] = $otheruser['avatar'];
                        $count_cond = array('zxid'=>$v['zxid']);
                        $counts=$this->_getAnsDao()->getCnt($count_cond);
                        #print_r($counts);
                        $consults['data'][$k]['hadanswernum'] =$counts;
			
			$consult_info=$this->_getConsultDao()->getOneByField($count_cond);	
			#print_r($consult_info);
			$consults['data'][$k]['publishtime'] =date('Y-m-d',$consult_info['p_time']);
			$k++;

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
			$Consult_n['data'][$k]['userid'] = $v['userid'];
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
		$gp = $this->controller->get_gp(array('userid','page','pagenum'));
                if($gp['page']<=0) $gp['page']=1;
                if($gp['pagenum']<=0) $gp['pagenum']=20;
                $page_start= ($gp['page']-1)*$gp['pagenum'];

		$conDao = $this->_getConsultDao();
		$msgDao = $this->_getMsgDao();
		$phoDao = $this->_getPhoneDao();
		$pubDao = $this->_getPubDao();
		$consults = $conDao->getUserConsult($gp['userid'],$gp['pagenum'],$page_start);
		foreach($consults as $k=>$v){
			#print_r($v);
			if($v['zxtype'] == PUB_CON){
				$answer_p="";
				$data = $pubDao->getOnePub($v['zxid']);
				$myConsult['listArr'][$k]['zxid'] = $v['zxid'];
				$myConsult['listArr'][$k]['zxtype'] = PUB_CON;
				$myConsult['listArr'][$k]['question'] = $data['content'];
				//todo
				$sql= "select userid from answer where zxid={$v['zxid']} group by userid order by id desc";
				$ans = $this->_getAnsDao()->getBySql($sql);
				$ansnum=count($ans);
				$num=0;
				foreach($ans as $vv)
				{
					$cond = array('userid'=>$vv['userid']);
                                	$userinfo =  $this->_getUserDao()->getUser($cond);
					if($userinfo['name']=="")
					{
						
						$phone=substr($userinfo['phone'],0,3)."xxxx".substr($userinfo['phone'],-4);
						$answer_p .= $phone.",";
					}else{

                                		$answer_p .= $userinfo['name'].",";
					}
					$num++;
					if($num >=3) break;

				}
				#$answer_p=substr($answer_p,0,-1);
				$answer_p=substr($answer_p, 0, -1);
				if($ansnum>3) $answer_p.="等".$ansnum."人";
				if($answer_p)
				{
					$myConsult['listArr'][$k]['lastanswerer'] = $answer_p ;
				}else{
					$myConsult['listArr'][$k]['lastanswerer'] = "" ;
				}
				
				#$myConsult['listArr'][$k]['lastanswerer']['answer'] = $ans[0][0]['content'];
				#$myConsult['listArr'][$k]['lastanswerer']['imagelist'] = $ans[0][0]['imagelist'];
			}
			if($v['zxtype'] == MSG_CON){
				$data = $msgDao->getOneMsg($v['zxid']);
				$myConsult['listArr'][$k]['zxid'] = $v['zxid'];
				$myConsult['listArr'][$k]['zxtype'] = MSG_CON;
				$myConsult['listArr'][$k]['question'] = $data['message'];
				$myConsult['listArr'][$k]['imagelist'] = $data['imagelist'];
				$myConsult['listArr'][$k]['userid'] = $v['to'];
			}
			if($v['zxtype'] == TEL_CON){
				$data = $phoDao->getOneTel($v['zxid']);
				$myConsult['listArr'][$k]['zxid'] = $v['zxid'];
				$myConsult['listArr'][$k]['zxtype'] = TEL_CON;
				$myConsult['listArr'][$k]['date'] = $data['date'];
				$myConsult['listArr'][$k]['phone'] = $data['phone'];
				$myConsult['listArr'][$k]['userid'] = $v['to'];
			}
		}
		if(empty($myConsult['listArr'])){
			$this->controller->ajax_msg('false','没有数据');
		}else{
			$this->controller->ajax_exit('true',$myConsult);
		}
	}

	public function new_answer(){
		$gp = $this->controller->get_gp(array('userid','page','pagenum'));
                /*
		if($gp['page']<=0) $gp['page']=1;
                if($gp['pagenum']<=0) $gp['pagenum']=20;
                $page_start= ($gp['page']-1)*$gp['pagenum'];
		*/
		$conDao = $this->_getConsultDao();
                $msgDao = $this->_getMsgDao();
                $pubDao = $this->_getPubDao();


		$consults = $conDao->getConsultNewAns($gp['userid']);
		#print_r( $consults);
		foreach($consults as $v)
		{
			$cond=array('zxid'=>$v['zxid']);
			$ans = $this->_getAnsDao()->getNewAns($v['zxid']);
			if($ans[0])
			{
                        	$answer=array();
				if($v['zxtype'] == PUB_CON){
                                	$data = $pubDao->getOnePub($v['zxid']);
                                	$answer['zxid'] = $v['zxid'];
                                	$answer['zxtype'] = PUB_CON;
                                	$answer['question'] = $data['content'];
					if(empty($answer['question']) and !empty($data['imageurl']))
						$answer['question']='图片';
					$answer['lastanswerer']=$ans[0]['content'];
					if(empty($answer['lastanswerer']) and !empty($ans[0]['content']['imagelist']))
					{
						$answer['lastanswerer']='图片';
					}
                                	//todo
                           		$new_answer[]=$answer;
                        	}
                        	if($v['zxtype'] == MSG_CON){
                                	$data = $msgDao->getOneMsg($v['zxid']);
                                	$answer['zxid'] = $v['zxid'];
                                	$answer['zxtype'] = MSG_CON;
                                	$answer['question'] = $data['message'];
					$answer['userid'] = $v['to'];
                                        if(empty($answer['question']) and !empty($data['imagelist']))
                                        	$answer['question']='图片';
                                	$answer['lastanswerer']=$ans[0]['content'];
                                        if(empty($answer['lastanswerer']) and !empty($ans[0]['content']['imagelist']))
                                        {
                                                $answer['lastanswerer']='图片';
                                        }
					$new_answer[]=$answer;
                        	}				
			}
			
		}
                if(empty($new_answer)){
                        $this->controller->ajax_msg('false','没有数据');
                }else{
			$re['listArr']=$new_answer;
                        $this->controller->ajax_exit('true',$re);
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
				$answerarr[$k]['position'] = $otheruser['position'];
				$answerarr[$k]['city'] = $otheruser['city']; 
				$answerarr[$k]['area'] = $otheruser['area']; 
				$answerarr[$k]['avatar'] = $otheruser['avatar']; 
				if($otheruser['role'] == INTER){
					$answerarr[$k]['hadservernums'] = $otheruser['hadservernums']; 
				}
				$answerarr[$k]['answer'] = $v['content'];
				$answerarr[$k]['imagelist'] = json_decode($v['imagelist']);
			}
			$pub['imageurl'] = json_decode($pub['imageurl']);
			$this->controller->ajax_exit('true',array('zxid'=>$gp['zxid'],'question'=>$pub['content'],'imagelist'=>$pub['imageurl'],'answerarr'=>$answerarr));
		}else{
			$this->controller->ajax_msg('false','非公开类咨询');
		}
	}

	public function add_answer(){
		$gp = $this->controller->get_gp(array('userid','zxid','content','imagelist'));
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

