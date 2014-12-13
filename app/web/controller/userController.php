<?php
/**
 * userController
 * @author wanglong 
	define('ENTER','1');	//企业
	define('INTER','2');	//中介
 */


class userController extends Controller {
	
	public $initphp_list = array(
		'eregister',	//企业用户注册
		'iregister',	//中介用户注册
		'login',		//登陆
		'alogin',		//管理员登陆
		'get_user',	//获取用户信息统一接口
		'get_inter_user',//中介信息
		'get_enter_user',//企业用户信息
		'mod_info',		//修改用户信息
		'mod_intro',	//修改自己的简介
		'set_rank',	//给评价
		'get_rank',	//拿评价
		'attention',//加或者取消关注
		'all_atten', //全部关注
		'accept_atten',//分页显示关注我的人
		'get_inter_user_list', //根据咨询类型获得中介列表
		'add_tag',	//增加用户tag
		'get_tag',	//用户tag
		'get_inter_user_cert',	//中介认证信息活得
		'set_inter_user_cert',	//中介认证信息设定
	); //Action白名单

	public function run() {    
		//$this->view->display("index_run"); //展示模板页面
	}
	
	public function eregister() {
		$reg = $this->controller->get_gp(array('phone', 'password'));
		$reg['role'] = ENTER; 
		$user = $this->controller->get_gp(array('phone'));
		$result = $this->_getUserDao()->getUser($user);
		$this->register($result,$reg);
	}

	public function iregister() {
		$reg = $this->controller->get_gp(array('phone', 'password'));
		$reg['role'] = INTER;
		$user = $this->controller->get_gp(array('phone'));
		$result = $this->_getUserDao()->getUser($user);
		$this->register($result,$reg);
	}

	public function register($result,$reg) {
		if($result > 0){
				$this->controller->ajax_msg('false','注册失败,原因:该手机号已存在');
		}else{
			$result = $this->_getUserDao()->addUser($reg);
			if ($result > 0) {
				$this->controller->ajax_exit('true',array('userid'=>$result));
			} else {
				$this->controller->ajax_msg('false','注册失败,原因:数据库插入错误');
			}
		}
	}

	public function login() {
		$user = $this->controller->get_gp(array('phone', 'password'));
		$role = $this->controller->get_gp(array('role'));
		if($role['role']=='qy')
		{
			$login_role=1;
		}elseif($role['role']=='zj'){
			$login_role=2;
		}
		$result = $this->_getUserDao()->getUser($user);
		#print_r($result);
		if ($result > 0) {
			if($login_role>0)
			{
				if($result['role']!=$login_role)
				{
					$this->controller->ajax_msg('false','请使用正确的客户端');
				}else{
					$this->controller->ajax_exit('true',array('userid'=>$result['userid']));
				}

			}else{
				$this->controller->ajax_exit('true',array('userid'=>$result['userid']));
			}
		} else {
			$this->controller->ajax_msg('false','登陆失败');
		}
	}
	
	public function alogin() {
		$user = $this->controller->get_gp(array('name', 'password'));
		$result = $this->_getUserDao()->getUser($user);
		#print_r($result);
		if ($result > 0) {
			session_start();
			$_SESSION['login'] = 'yes';
			header("Location:admin/index.php?c=admin&a=iuser_list");
			//$this->controller->ajax_exit('true',array('userid'=>$result['userid']));
		} else {
			echo '登陆失败';
			exit();
		}
	}

        public function get_user() {
                $user = $this->controller->get_gp(array('userid'));
		$myid = $this->controller->get_gp(array('myid'));
		$att=array('userid'=>$myid['myid'],'otheruserid'=>$user['userid'],'stat'=>'1');
		$relation_result = $this->_getRealeationDao()->get($att);
		$userinfo = $this->_getUserDao()->getUser($user);
		if($userinfo['role']==1)
		{
                    $result = $this->_getUserDao()->getEnterUser($user['userid']);
                    if ($result) {
                        $rCond = array('userid'=>$user['userid'],'stat'=>'1');
                        $rbCond = array('otheruserid'=>$user['userid'],'stat'=>'1');
                        $result['beattentionnum'] = $this->_getRealeationDao()->getCnt($rbCond);
                        $result['attentionnum'] = $this->_getRealeationDao()->getCnt($rCond);
			if($relation_result) {
				$result['hadgz']=1;
			}else{
				$result['hadgz']=0;
			}
                        $proj = $this->_getPrjDao()->getByCond(array('userid'=>$user['userid']));
                        if($proj[0] > 0) foreach($proj[0] as $v){
                                $result['mineapp'][] = array('name'=>$v['name'],'appid'=>$v['id']);
                        }
			if(empty($result['mineapp'])) $result['mineapp']=array();
                        $this->controller->ajax_exit('true',$result);
                    } else {
                        $this->controller->ajax_msg('false','失败,原因:没有数据');
                    }
		}else{
                    $result = $this->_getUserDao()->getInterUser($user['userid']);
                    if ($result) {
                        $tags = $this->_getTagDao()->getByUserId($user['userid']);
			$rCond = array('userid'=>$user['userid'],'stat'=>'1');
                        $rbCond = array('otheruserid'=>$user['userid'],'stat'=>'1');
                        $result['beattentionnum'] = $this->_getRealeationDao()->getCnt($rbCond);
                        $result['attentionnum'] = $this->_getRealeationDao()->getCnt($rCond);			
                        if($relation_result) {
                                $result['hadgz']=1;
                        }else{
                                $result['hadgz']=0;
                        }
                        #$result['fansnum'] = $this->_getRealeationDao()->getCnt($rbCond);
                        if(count($tags) > 0) foreach($tags as $v){
                                $result['tag'][] = array('typeid'=>$v['consult_id'],'title'=>$v['title']);
                        }
                        $this->controller->ajax_exit('true',$result);
                    } else {
                        $this->controller->ajax_msg('false','失败,原因:没有数据');
                    }
		}
        }
	
	public function add_tag() {
		$gp = $this->controller->get_gp(array('userid','taglist'));
		#$gp['taglist']='[{"typename":"新三板","typeid":"1"},{"typename":"上市","typeid":"2"}]';
		$gp['taglist']=json_decode(stripcslashes($gp['taglist']),true);
		$do_result=1;
		if(count($gp['taglist'])<=0) 
		{
			$this->controller->ajax_msg('false','没有标签'); exit;
		}
		
		$del_cond=array('userid'=>$gp['userid']);
		$del_result = $this->_getTagDao()->del($del_cond);
		foreach($gp['taglist'] as $v)
		{
			$att = array('userid'=>$gp['userid'],'consult_id'=>$v['typeid']);
			#$result = $this->_getTagDao()->get($att);
			#if(!$result)
			{
				$add_result=$this->_getTagDao()->add($att);
				if($add_result <=0) $do_result=0;
			}
			
		}
                if ($do_result > 0) {
                        $this->controller->ajax_exit('true');
                } else {
                        $this->controller->ajax_msg('false','加标签失败');
                }

	} 

	public function get_tag() {
		$user = $this->controller->get_gp(array('userid'));
		$tags = $this->_getTagDao()->getByUserId($user['userid']);
		print_r($tags);
	
	}
	
	public function get_enter_user() {
		$user = $this->controller->get_gp(array('userid'));
		$result = $this->_getUserDao()->getEnterUser($user['userid']);
		if ($result) {
			$rCond = array('userid'=>$user['userid'],'stat'=>'1');
			$rbCond = array('otheruserid'=>$user['userid'],'stat'=>'1');
			$result['beattentionnum'] = $this->_getRealeationDao()->getCnt($rbCond);
			$result['attentionnum'] = $this->_getRealeationDao()->getCnt($rCond);
			$proj = $this->_getPrjDao()->getByCond(array('userid'=>$user['userid']));
			if($proj[0] > 0) foreach($proj[0] as $v){
				$result['mineapp'][] = array('name'=>$v['name'],'appid'=>$v['id']);
			}
			$this->controller->ajax_exit('true',$result);
		} else {
			$this->controller->ajax_msg('false','失败,原因:没有数据');
		}
	}

	public function get_inter_user() {
		$user = $this->controller->get_gp(array('userid'));
		$result = $this->_getUserDao()->getInterUser($user['userid']);
		if ($result) {
			$tags = $this->_getTagDao()->getByUserId($user['userid']);
			$rbCond = array('otheruserid'=>$user['userid'],'stat'=>'1');
			$result['fansnum'] = $this->_getRealeationDao()->getCnt($rbCond);
			if(count($tags) > 0) foreach($tags as $v){
				$result['tag'][] = array('typeid'=>$v['consult_id'],'title'=>$v['title']);
			}
			$this->controller->ajax_exit('true',$result);
		} else {
			$this->controller->ajax_msg('false','失败,原因:没有数据');
		}
	}

	//加关注以及取消关注
	public function attention(){
		$att = $this->controller->get_gp(array('userid','otheruserid'));
		$record = $this->_getRealeationDao()->get($att);
		if($record && $record['stat'] == 1){
			$result = $this->_getRealeationDao()->update(array('stat'=>0),$att);
		}else if($record && $record['stat'] == 0){
			$result = $this->_getRealeationDao()->update(array('stat'=>1),$att);
		}else{
			$att['stat'] = 1;//关注:1
			$result = $this->_getRealeationDao()->add($att);
		}
		if ($result > 0) {
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_msg('false','注册失败,原因:数据库错误');
		}
	}

	//用户信息编辑
	public function mod_info(){
		$cond= $this->controller->get_gp(array('userid'));
		if(!$cond['userid']) {
			$this->controller->ajax_msg('false','修改失败,原因:没有userid参数');
		}
		$data = $this->controller->get_gp(array('name','avatar', 'company', 'position', 'city', 'area','introduction'));
			
		foreach ($data as $k => $v)
		{
			if($v)
			$t_data[$k]=$v;
		}
		$result = $this->_getUserDao()->update($t_data,$cond);
		if ($result > 0) {
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_msg('false','修改失败,原因:数据库错误');
		}
	}

	//修改简介
	public function mod_intro() {
		$cond = $this->controller->get_gp(array('userid'));
		$data = $this->controller->get_gp(array('introduction'));
		$result = $this->_getUserDao()->update($data,$cond);
		if ($result > 0) {
			$this->controller->ajax_exit('true');
		} else {
			$this->controller->ajax_msg('false','修改失败,原因:数据库错误');
		}
	}

	public function set_rank(){
		$cond = $this->controller->get_gp(array('userid','agencyid'));
		$data = $this->controller->get_gp(array('userid','agencyid','starrank','ishelpful','iswell','content'));
		$result = $this->_getRankDao()->get($cond);
		if($result) {
			$result_e = $this->_getRankDao()->update($data,$cond);
		} else {
			$result_e = $this->_getRankDao()->add($data);
		}
		if($result_e)
		{
			$this->controller->ajax_exit('true');
		}else{
			$this->controller->ajax_exit('false');
		}
	}

	public function get_rank(){
		$cond = $this->controller->get_gp(array('agencyid'));
		$result = $this->_getRankDao()->getByField($cond);
		#print_r($result);
		$sql="SELECT count(1) num,sum(`starrank`) starrank ,sum(`ishelpful`) ishelpful,sum(`iswell`) iswell FROM `rank`";
		$all = $this->_getRankDao()->getBySql($sql);
		#print_r($all);
		$r['evaluatelis']=array();
		foreach($result[0] as $k => $v)
		{
			$r['evaluatelis'][$k]['content']=$v['content'];
			$r['evaluatelis'][$k]['professionran']=$v['ishelpful'];
			$r['evaluatelis'][$k]['serverran']=$v['iswell'];
			$r['evaluatelis'][$k]['tuijianran']=$v['starrank'];
			#$r['evaluatelis'][$k]['']=$v[''];
			$s_professionran	+= $v['ishelpful'];
			$s_serverran		+= $v['iswell'];
			$s_tuijianran		+= $v['starrank'];			
			$cond_u = array('userid'=>$v['userid']);
                        $userinfo =  $this->_getUserDao()->getUser($cond_u);
			$phone=substr($userinfo['phone'],0,3)."xxxx".substr($userinfo['phone'],-4);
			$r['evaluatelis'][$k]['id']=$phone;


		}
		$r['professionran']=round($s_professionran/$result[1]);
		$r['serverran']=round($s_serverran/$result[1]);
		$r['tuijianran']=round($s_tuijianran/$result[1]);

		$avg_professionran=round($all['ishelpful']/$all['num']);
		$avg_serverran=round($all['iswell']/$all['num']); 
		$avg_tuijianran=round($all['starrank']/$all['num']); 

		$r["professionprecent"]=round((($r['professionran']-$avg_professionran)/$avg_professionran)*100);
		$r["serverprecent"]=round((($r['serverran']-$avg_serverran)/$avg_serverran)*100);
		$r["tuijianprecent"]=round((($r['tuijianran']-$avg_tuijianran)/$avg_tuijianran)*100);

		if($r)
		{
			$this->controller->ajax_exit('true',$r);
		}else{
			$this->controller->ajax_exit('false');
		}

	}

	public function all_atten(){
		$cond = $this->controller->get_gp(array('userid'));
		#print_r($cond);
		$cond['stat']=1;
		$data = $this->_getRealeationDao()->getByField($cond);
		$members = array();
		if(!empty($data[0]))foreach($data[0] as $k=>$v){
			$cond = array('userid'=>$v['otheruserid']);
			
			$otheruser =  $this->_getUserDao()->getUser($cond);
			$members[$k]['userid'] = $otheruser['userid']; 
			$members[$k]['name'] = $otheruser['name']; 
			$members[$k]['company'] = $otheruser['company']; 
			$members[$k]['city'] = $otheruser['city']; 
			$members[$k]['area'] = $otheruser['area']; 
			$members[$k]['avatar'] = $otheruser['avatar']; 
			$members[$k]['hadservernums'] = $otheruser['hadservernums']; 
			$userTag = $this->_getTagDao()->getByUserId($v['otheruserid']);
			if($userTag) foreach($userTag as $ut){
				$members[$k]['tag'][] = array(
					"typeid" => $ut['consult_id'],
					"title" => $ut['title'],
				);
			}
		}
		$this->controller->ajax_exit('true',array('members'=>$members));
	}

    public function accept_atten(){
                $r_info = $this->controller->get_gp(array('userid','page','pagenum'));
                $cond	= array('otheruserid'=>$r_info['userid'],'stat'=>1);
		if($r_info['page']<=0) $r_info['page']=1;
		$page_start= ($r_info['page']-1)*$r_info['pagenum'];
		if ($page_start <0) $page_start=0;
		if($r_info['pagenum']<=0) $r_info['pagenum']=20;
                $data = $this->_getRealeationDao()->getByField($cond,$r_info['pagenum'],$page_start);
                $members = array();
                if(!empty($data[0]))foreach($data[0] as $k=>$v){
                        $cond = array('userid'=>$v['userid']);
                        $otheruser =  $this->_getUserDao()->getUser($cond);
                        $members[$k]['userid'] = $otheruser['userid'];
                        $members[$k]['name'] = $otheruser['name'];
                        $members[$k]['company'] = $otheruser['company'];
                        $members[$k]['city'] = $otheruser['city'];
                        $members[$k]['area'] = $otheruser['area'];
                        $members[$k]['imageurl'] = $otheruser['avatar'];
                        $members[$k]['hadservernums'] = $otheruser['hadservernums'];
                        $userTag = $this->_getTagDao()->getByUserId($v['otheruserid']);
                        if($userTag) foreach($userTag as $ut){
                                $members[$k]['tag'][] = array(
                                        "typeid" => $ut['consult_id'],
                                        "title" => $ut['title'],
                                );
                        }
                }
                $this->controller->ajax_exit('true',array('pagenum'=>$r_info['pagenum'],'page'=>$r_info['page'],'data'=>$members));
        }
	public function get_inter_user_list(){
		$cond = $this->controller->get_gp(array('typeid'));
		$myid = $this->controller->get_gp(array('myid'));
		$cond = array('consult_id'=>$cond['typeid']);
		$data = $this->_getTagDao()->getByField($cond);
		$members = array();
		$k=0;
		if(!empty($data[0]))foreach($data[0] as $v){
			$cond = array('userid'=>$v['userid']);
			if($v['userid']==$myid['myid'] ) continue;
			$otheruser =  $this->_getUserDao()->getUser($cond);
			if($otheruser['role']!=2 or $otheruser['name']=="") continue;
			$members[$k]['userid'] = $otheruser['userid'];
			$members[$k]['role'] = $otheruser['role']; 
		
			$members[$k]['name'] = $otheruser['name']; 
			$members[$k]['company'] = $otheruser['company']; 
			$members[$k]['city'] = $otheruser['city']; 
			$members[$k]['area'] = $otheruser['area']; 
			$members[$k]['avatar'] = $otheruser['avatar']; 
			$members[$k]['hadservernums'] = $otheruser['hadservernums']; 
			$userTag = $this->_getTagDao()->getByUserId($v['userid']);
			if($userTag) foreach($userTag as $ut){
				$members[$k]['tag'][] = array(
					"typeid" => $ut['consult_id'],
					"title" => $ut['title'],
				);
			}
                	$att=array('userid'=>$myid['myid'],'otheruserid'=>$v['userid'],'stat'=>'1');
                	$relation_result = $this->_getRealeationDao()->get($att);
                        if($relation_result) {
                                $members[$k]['hadgz']=1;
                        }else{
                                $members[$k]['hadgz']=0;
                        }
			$k++;
		}
		
		$this->controller->ajax_exit('true',array('members'=>$members));
	}

	public function get_inter_user_cert(){
		$cond = $this->controller->get_gp(array('userid'));
		$certInfo = $this->_getInterCertDao()->getOneByField($cond);
		$this->controller->ajax_exit('true',$certInfo);
	}

	public function set_inter_user_cert(){
		$cond = $this->controller->get_gp(array('userid'));
		$data = $this->controller->get_gp(array('userid','profession','professioncode','imformation','achieve'));
		$certInfo = $this->_getInterCertDao()->getOneByField($cond);
		if($certInfo){
			$result_e = $this->_getInterCertDao()->update($data,$cond);
		} else {
			$result_e = $this->_getInterCertDao()->add($data);
		}
		if($result_e)
		{
			$this->controller->ajax_exit('true');
		}else{
			$this->controller->ajax_exit('false');
		}
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

	private function _getInterCertDao() {
		return InitPHP::getDao("interCert");
	}
} 
