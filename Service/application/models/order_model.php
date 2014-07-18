<?php

class Order_model extends CI_Model
{

	var $tblname = "tbl_order";
	var $tbl_member = 'tbl_member';
	var $tbl_member_pos = 'tbl_member_pos';
	var $tbl_goods = 'tbl_goods';
	var $tbl_order_goods = 'tbl_order_goods';
	var $tbl_sms_history = 'tbl_sms_history';

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function getorderlist()
	{
		$aColumns = array('A.uid as primaryuid',  'A.orderno', 'A.memberid', 
			'DATEDIFF(s, \'19700101\', A.ordertime) as ordertime', 'A.status', 'A.posid', 'A.sendtime',
			'A.sendingno', 'A.seat', 
			'B.memberlevel', 
			'C.receivername', 'C.addrprovince', 'C.addrcity', 'C.addrarea', 'C.addrstreet', 'C.phonenum');
		
		$aRetColumns = array('primaryuid', 'orderno', 'ordertime', 'receivername', 'addrprovince', 'reserveprice', 'status', 'memberlevel');

		$aOrderColumns = array('orderno', 'ordertime', 'receivername', '', 'status', 'memberlevel');

		$aWhereColumns = array('A.uid', 'orderno', 'ordertime', 'receivername', 'C.addrprovince', 'C.addrcity', 'C.addrarea', 'C.addrstreet', 'C.phonenum', 'A.status', 'B.memberlevel');

		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "A.uid";
		
		/* DB table to use */
		$sTable = $this->tblname . " AS A INNER JOIN $this->tbl_member AS B ON A.memberid = B.uid " . 
			"INNER JOIN $this->tbl_member_pos AS C ON A.posid = C.uid ";
//			"INNER JOIN $this->tbl_order_goods AS D ON D.orderid = A.uid";
				
		/* 
		 * Paging
		 */
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
				intval( $_GET['iDisplayLength'] );
		}
		
		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= "".$aOrderColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
						($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		
		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aWhereColumns) ; $i++ )
			{
				$sWhere .= "".$aWhereColumns[$i]." LIKE '%". $_GET['sSearch'] ."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aWhereColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				$sWhere .= "".$aWhereColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
			}
		}
		
	
		if (trim($sWhere) != "") {
			$sWhere .= " AND A.deleted = 0";
		} else {
			$sWhere .= " WHERE A.deleted = 0";
		}
		
		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "
			SELECT DISTINCT TOP " . $_GET['iDisplayLength'] . " ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable
			$sWhere";
//			$sOrder";

        if ($sWhere != "") {
            $sQuery .= " AND $sIndexColumn NOT IN (SELECT TOP " . $_GET['iDisplayStart'] . " $sIndexColumn FROM $sTable $sWhere $sOrder)";
        } else {
            $sQuery .= " WHERE $sIndexColumn NOT IN (SELECT TOP " . $_GET['iDisplayStart'] . " $sIndexColumn FROM $sTable $sOrder)";
        }
        $sQuery .= " $sOrder";
        log_message('info', 'data-query : ' . $sQuery);

		$rst = $this->db->query($sQuery);
		if ($rst && $rst->num_rows() > 0) {
			$rResult = $rst->result_array();
		}
		/* Data set length after filtering */

		$sQuery = "
			SELECT COUNT( $sIndexColumn) " . "
			FROM   $sTable
			$sWhere";
//		$sQuery .= " $sOrder";

		$rst = $this->db->query($sQuery);
		if ($rst && $rst->num_rows() > 0) {
			$aResultFilterTotal = $rst->row();
		}

		$iFilteredTotal = $aResultFilterTotal->computed;
		
		/* Total data set length */
		$sQuery = "
			SELECT COUNT(".$sIndexColumn.")
			FROM   $sTable
			WHERE A.deleted = 0
		";

		$rst = $this->db->query($sQuery);
		if ($rst && $rst->num_rows() > 0) {
			$aResultTotal = $rst->row();
		}

		$iTotal = $aResultTotal->computed;
		
		
		/*
		 * Output
		 */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		
		if (isset($rResult)) {
			foreach ($rResult as $key => $aRow)
			{
				$row = array();
				for ( $i=0 ; $i<count($aRetColumns) ; $i++ )
				{
					if ($aRetColumns[$i] == 'primaryuid' || $aRetColumns[$i] == 'addrprovince'){
						continue;
					} else if ( $aRetColumns[$i] == 'orderno' ) {
						$row[] = "<a href='" . base_url() . "orderdetail/" . $aRow["primaryuid"] . "'>" . $aRow[$aRetColumns[$i]] . "</a>";
                    } else if ( $aRetColumns[$i] == 'ordertime' ) {
                        $row[] = date('Y-m-d H:i:s', $aRow[ $aRetColumns[$i] ]);
                    } else if ( $aRetColumns[$i] == 'receivername' ) {
                        $row[] = $aRow[ $aRetColumns[$i] ]."(".$aRow['phonenum'].
						") ".$aRow['addrprovince'].$aRow['addrcity'].$aRow['addrarea'].$aRow['addrstreet'];
                    } else if ( $aRetColumns[$i] == 'status' ) {
                        $row[] = $this->get_status_message($aRow[ $aRetColumns[$i] ]);
                    } else if ( $aRetColumns[$i] == 'reserveprice' ) {
                        $row[] = $this->get_total_price($aRow[ "primaryuid" ]);
                    } else if ( $aRetColumns[$i] == 'memberlevel' ) {
                        $row[] = $this->get_memberlevel_value($aRow[ $aRetColumns[$i] ]);
					} else if ( $aRetColumns[$i] != '' ) {
						/* General output */
						$row[] = $aRow[ $aRetColumns[$i] ];
					}
				}
				if ($aRow["status"] >= ORDER_STATUS_ALREADY_DELIVER) {
					$row[] = 0;
				} else {
					$row[] = $aRow["primaryuid"];
				}
				$row[] = $aRow["phonenum"];
				$output['aaData'][] = $row;
			}
		}

		return $this->common->convertUTF8($output);
	}

	function get_total_price($orderid)
	{
		$total = 0;
		$this->db->where("orderid", $orderid);
		$this->db->where("deleted", 0);
		$query = $this->db->get($this->tbl_order_goods);
		if ($query->num_rows())
		{
			$result = $query->result();
			foreach($result as $row)
			{
				$total = $total + $row->reserveprice * $row->quantity;
			}
			
		}
		return $total;
	}

	function get_status_message($status)
	{
		switch($status)
		{
			case ORDER_STATUS_WAIT_DELIVER:
				return '<span class="label label-large label-important arrowed">待发货</span>';
				return "<font color='red'>待发货</font>";
				break;
			case ORDER_STATUS_ALREADY_DELIVER:
				return '<span class="label label-large label-info arrowed-right arrowed-in">已发货</span>';
				break;
			case ORDER_STATUS_ALREADY_RECEIVE:
				return '<span class="label label-large label-success arrowed-in arrowed-in-right">已完成</span>';
				break;
			case ORDER_STATUS_ALREADY_CANCEL:
				return '<span class="label label-large arrowed"><s>已取消</s></span>';
				break;
		}
	}

	function get_memberlevel_value($level)
	{
		switch($level)
		{
			case MEMBER_LEVEL0:
				return "普通";
				break;
			case MEMBER_LEVEL1:
				return "一级";
				break;
			case MEMBER_LEVEL2:
				return "二级";
				break;
			case MEMBER_LEVEL3:
				return "三级";
				break;
			case MEMBER_LEVEL4:
				return "四级";
				break;
			case MEMBER_LEVEL5:
				return "五级";
				break;
		}
	}

	function get_modal_userid($name){
		$field = "uid";
		$where = "membername = '".$name."'";
		$query = $this->db->query("SELECT {$field} FROM {$this->tbl_member} WHERE {$where}");
		return DisplayData($query->result_array(), $field);
	}
	function open_modal($userid){
		$field = "content, SendTime";
		$query = $this->db->query("SELECT A.content, B.SendTime FROM $this->tbl_sms as A, receiveletter_tbl as B WHERE B.UserID='".$userid."' AND B.LetterID=A.uid");
		return DisplayData($query->result_array(), $field);
	}
	function sending_messages($names, $data, $time){
		$name = explode(',',$names);
		$ddt = array('content'=>$data,'SendTime'=>$time);
        $this->db->insert($this->tbl_letter,$ddt);
		$letter_id = $this->db->insert_id();
		for($i=0; $i<count($name); $i++){
			$nm = explode('(',$name[$i]);
			$query2 = $this->db->query("SELECT ID,DeviceID FROM $this->tbl_member WHERE UserName='".$nm[0]."'");
            $data_query = $query2->result_array();
			foreach($data_query as $da)
            {
			    $ldt = array('LetterID'=>$letter_id,'SendTime'=>$time,'UserID'=>$da["ID"],"Received"=>0);
			    $this->db->insert($this->tbl_rcvlt,$ldt);
                push_notification($data,$da["DeviceID"]);
            }
		}
		return 'OK';
	}

	function sendsms()
	{
		$phonenum = $this->input->post("phonenum");
		$receiver = $this->input->post("receiver");
		$content = $this->input->post("smscontent");
		$sendtime = date('Y-m-d H:i:s');

		$url = SMS_SERVER.
			'?act='.SMS_ACTION_SEND.
			'&unitid='.SMS_COMP_ID.
			'&username='.SMS_USERNAME.
			'&passwd='.SMS_PASSWORD.
			'&msg='.$content.
			'&phone='.$phonenum.
			'&port='.
			'&sendtime='.$sendtime;

		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'method'  => 'GET'
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		
		$results = explode(",", $result);

		if (count($results) > 2)
		{
			$this->log_sms_history($receiver, $phonenum, $content, $sendtime, "");
		}

		return $this->common->convertUTF8($results);
	}

	function log_sms_history($receiver, $phonenum, $contents, $sendtime, $status)
	{
		$data = array(
		   'receiverinfo' => $receiver,
		   'createtime' => $sendtime,
		   'contents' => $contents
		);
		$this->db->insert($this->tbl_sms_history, $data);

        return $this->db->affected_rows();
	}
}

?>