<?php
if( !defined('IN') ) die('bad request');

class dataTable
{

	public $aColumns;
 	public $iTotal;
	public $iTotalDisplayRecords;
  	public $mbn;
     
	function __construct($clms,$mbn)
        {
		$this->aColumns = $clms;
		$this->mbn = $mbn;
        }
	

	public function get_limit(){
		$sLimit = "";
                if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
                {
                        $sLimit = intval( $_GET['iDisplayStart'] ).", ".
                                intval( $_GET['iDisplayLength'] );
                }
		return $sLimit;
	}
	
	public function get_order(){
                $sOrder = "";
                if ( isset( $_GET['iSortCol_0'] ) )
                {
                        $sOrder = "ORDER BY  ";
                        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
                        {
                                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                                {
                                        $sOrder = "".$this->aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
                                                ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";

                                }
                        }

                        $sOrder = substr_replace( $sOrder, "", -2 );
                        if ( $sOrder == "ORDER BY" )
                        {
                                $sOrder = "";
                        }
                }
		return $sOrder;
	}

	public function get_where(){
                $sWhere = "";
                if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
                {
                        $sWhere = "AND (";
                        for ( $i=0 ; $i<count($this->aColumns) ; $i++ )
                        {
                                if($this->aColumns[$i]!='') $sWhere .= $this->aColumns[$i]."  like \"%".( $_GET['sSearch'] )."%\" OR ";
                        }
                        $sWhere = substr_replace( $sWhere, "", -3 );
                        $sWhere .= ')';
                }
		
		for ( $i=0 ; $i<count($this->aColumns) ; $i++ )
                {
                        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
                        {
                                if ( $sWhere == "" ){
                                        $sWhere = " ";
                                }else{
                                        $sWhere .= " AND ";
                                }
                                if($this->aColumns[$i]!='') $sWhere .= $this->aColumns[$i]." like \"%".($_GET['sSearch_'.$i])."%\" ";
                        }
                }
		return $sWhere;
	}
	
	public function echo_result($data){
                $output = array(
                        "sEcho" => intval($_GET['sEcho']),
                        "iTotalRecords" => intval($this->iTotal),
                        "aaData" => array()
                );

		if(!empty($data)){
			$aColumnsCount = count($this->aColumns);
                        foreach($data as $k=>$v){
                                $row = array();//print_r($v);
                                for( $i=0; $i<$aColumnsCount; $i++){
                                        if($this->aColumns[$i]!= '' ) {
						$aclms = $this->aColumns[$i];//var_dump($aclms);
                                                if(is_object($v)){
							$row[] = $v->$aclms;
						}else{
							$row[] = $v[$aclms];
						}
                                        }else if ($i == (count($this->aColumns) - 1)){
						$mbn = $this->mbn;
						$vid = $this->aColumns[0];
						if(is_object($v)){
							if($mbn == 'make_btn_1'){
								$row[] = $this->$mbn($v->$vid,$v->i_result);
							}else{
								$row[] = $this->$mbn($v->$vid);
							}
						}else{
							if($mbn == 'make_btn_2'){
								$row[] = $this->$mbn($v['checksum']);
							}
						}
                                        }else{
                                                $row[] = '';
                                        }
                                }
                                $output['aaData'][] = $row;
                        }
                        $output["iTotalDisplayRecords"] = $this->iTotalDisplayRecords;

                }else{
			$empty_array = array();
			for( $i=0; $i<count($this->aColumns) ; $i++){
				$empty_array[] = '';
			}
                        $output['aaData'][] = $empty_array;
			$output["iTotalDisplayRecords"] = 0; 
                }
		echo json_encode( $output );
	}

	private function make_btn_1($id,$result=''){
		$closeHtml = '';
		if($result != '成功') $closeHtml = ' &nbsp;<a class="btn btn-mini btn-danger close-this" href="#'.$id.'" >  <i class="icon-remove icon-white"></i>关闭</a>';
		return '<a class="btn btn-mini btn-success" href="'.SINA_RT_URL.'/issue/view?issue_id='.$id.'" target=_blank>   <i class="icon-share icon-white"></i>>查看</a>'.$closeHtml;
	}
	private function make_btn_2($id){
		return '<a href="/?m=slowlog&a=detail&cs='.$id.'" target="_blank" class="btn btn-mini btn-info">查看</a>';
	}
}
