<?php

class Noticelog_model extends CI_Model {
    var $tblname = "tbl_noticelog";
    var $tblmember = "tbl_member";

	function __construct() {
        parent::__construct();
        $this->load->library('common');
        $this->load->library('session');
    }

	function addNotice($title,$contents,$members,$from_date,$to_date)
    {
        $data = array(
            "title"=>$title,
            "contents"=>$contents,
            "starttime"=>date("Y-m-d H:i:s"),//$from_date,
            "endtime"=>date("Y-m-d H:i:s"),//$to_date,
            "receiver"=>$members,
            'createtime'=>date("Y-m-d H:i:s")
        );
        $this->db->insert($this->tblname, $data);
        if ($this->db->affected_rows() <= 0)
            return 0;

        $id = $this->db->insert_id();
        $convertedContents = $this->common->convertUTF8($contents);

        $array = array();
        $count = 0;

        for($j = 0; $j < 5; $j++)
        {
            if (((1 << ($j + 1)) & $members) != 0)
            {
				$sql = "SELECT b.deviceid as deviceid FROM tbl_member as a, tbl_loginlist as b WHERE b.memberid = a.uid AND a.memberlevel=? AND b.forcelogout = 0";
				$data = array(
					$j + 1
					);
                $query = $this->db->query($sql, $data);
                if (isset($query))
                {
                    foreach($query->result() as $row)
                    {
                        /*$data = array(
                            "InfoID"=>$id,
                            "UserID"=>$row->ID,
                            "Received"=>0,
                            "ReceivedTime"=>"0000-00-00 00:00:00",
                            "SendTime"=>date("Y-m-d H:i:s"),
                            "memberlevel"=>$row->MemberLevel
                        );*/
                        //$this->db->insert("receiveinfo_tbl",$data);
						if ($row->deviceid != "")
						{
                            //deviceid = " < xxxxx xxxxx xxxx >" remove "<" and ">" => "xxxxx xxxxx xxxxx"
                            $deviceId = $row->deviceid;
                            $len = strlen($deviceId);
                            if ($len <= 2)
                                continue;
                            $deviceId = substr($deviceId, 1, $len - 2);
							//log_message('info', 'push notification : ' . $deviceId);
							//push_notification($convertedContents, $deviceId);
                            $item = array();
                            $item['msg'] = $convertedContents;
                            $item['deviceId'] = $deviceId;
                            $array[$count] = $item;
                            $count++;
						}
                    }
                }
            }
        }

        if ($count > 0)
            push_notification_array($array);
    }

    public function getList()
    {
        $aColumns = array('uid',  'title', 'contents', 'convert(varchar(19), createtime, 120) as createtime', 'receiver');
        $aRetColumns = array('uid',  'title', 'contents', 'createtime', 'receiver');

        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = "uid";

        /* DB table to use */
        $sTable = $this->tblname;

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
                    $sOrder .= "".$aRetColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
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
            for ( $i=0 ; $i<count($aRetColumns) ; $i++ )
            {
                $sWhere .= "".$aRetColumns[$i]." LIKE '%". $_GET['sSearch'] ."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }

        /* Individual column filtering */
        for ( $i=0 ; $i<count($aRetColumns) ; $i++ )
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
                $sWhere .= "".$aRetColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }

        if ($sWhere == "")
            $sWhere = " WHERE deleted = 0";
        else
            $sWhere .= " AND deleted = 0";


        /*
         * SQL queries
         * Get data to display
         */
        $sQuery = "
			SELECT TOP " . $_GET['iDisplayLength'] . " ".str_replace(" , ", " ", implode(", ", $aColumns))."
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
			SELECT COUNT(uid) " . "
			FROM   $sTable
			$sWhere";
//		$sQuery .= " $sOrder";

        $rst = $this->db->query($sQuery);
        if ($rst && $rst->num_rows() > 0) {
            $aResultFilterTotal = $rst->row();
        }

        if (isset($aResultFilterTotal))
            $iFilteredTotal = $aResultFilterTotal->computed;
        else
            $iFilteredTotal = 0;

        /* Total data set length */
        $sQuery = "
			SELECT COUNT(".$sIndexColumn.")
			FROM   $sTable
			WHERE deleted = 0
		";

        $rst = $this->db->query($sQuery);
        if ($rst && $rst->num_rows() > 0) {
            $aResultTotal = $rst->row();
        }

        if (isset($aResultTotal))
            $iTotal = $aResultTotal->computed;
        else
            $iTotal = 0;


        /*
         * Output
         */
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        if (isset($rResult))
        {
            foreach ($rResult as $key => $aRow)
            {
                $row = array();
                for ( $i=0 ; $i<count($aRetColumns) ; $i++ )
                {
                    if ( $aRetColumns[$i] == 'title' ) {
                        //$row[] = "<a href='" . base_url() . "message/edit/" . $aRow["uid"] . "'>" . $aRow[ $aColumns[$i] ] . "</a>";
                        $row[] = $aRow[ $aRetColumns[$i] ];
                    } else if ($aRetColumns[$i] == 'receiver') {
                        $val = "";
                        $intval = 0;
                        for($j = 0; $j < 5; $j++)
                        {
                            if (((1 << ($j + 1)) & $aRow[$aRetColumns[$i]]) != 0)
                            {
                                if ($val == "")
                                    $val = $j + 1;
                                else
                                    $val = $val .", " . ($j + 1);
                                $intval = $intval + (1 << ($j + 1));
                            }
                        }
                        if ($val != "")
                        {
                            if ($intval == (1 << 1) + (1 << 2) + (1 << 3) + (1 << 4) + (1 << 5))
                                $row[] = "所有会员";
                            else
                                $row[] = $val . "级会员";
                        }
                        else
                            $row[] = "";
                    } else if ( $aRetColumns[$i] != '' ) {
                        /* General output */
                        $row[] = $aRow[ $aRetColumns[$i] ];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        return $this->common->convertUTF8($output);
    }

    function delete_id($ids)
    {
        $arr_id = explode(",", $ids);
        if (count($arr_id) == 0)
            return 0;

        $where = "uid = " . $arr_id[0];
        for($i = 1; $i < count($arr_id) - 1; $i++)
        {
            $where.= " OR uid = " . $arr_id[$i];
        }
        $query = $this->db->query("UPDATE " . $this->tblname ." SET deleted = 1 WHERE {$where}");
        return $this->db->affected_rows();
    }
}