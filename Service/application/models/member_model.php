<?php

class Member_model extends CI_Model {

    var $tblname = "tbl_member";
    var $tbl_region = "tbl_region";

    function __construct() {
        parent::__construct();
        $this->load->library('common');
        $this->load->library('session');
   }

	function getList() {
        $aColumns = array('uid',  'memberid', 'membername', 'phonenum', 'memberlevel', 'uid as forpwd');
        $aRetColumns = array('uid', 'memberid', 'membername', 'phonenum', 'memberlevel', 'forpwd');

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
                if ($aRetColumns[$i] != 'forpwd')
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
            $sWhere .= " WHERE deleted = 0";
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

        $iFilteredTotal = $aResultFilterTotal->computed;

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

        if (isset($rResult ))
        {
            foreach ($rResult as $key => $aRow)
            {
                $row = array();
                for ( $i=0 ; $i<count($aRetColumns) ; $i++ )
                {
                    if ( $aRetColumns[$i] == 'uid')
                    {
                        $row[] = $aRow[ $aRetColumns[$i] ];
                    } else if ( $aRetColumns[$i] == 'memberlevel') {
                        $row[] = $this->order_model->get_memberlevel_value($aRow[ $aRetColumns[$i] ]);
                    } else if ( $aRetColumns[$i] == 'memberid') {
                        $row[] = "<a href='".base_url()."member_add/".$aRow['uid']."'>".$aRow[ $aRetColumns[$i] ] ."</a>";
                    } else if ($aRetColumns[$i] == 'forpwd') {
                        $row[] = "<a href='#modal-confirm' role='button' class='green' data-toggle='modal' onclick='return onResetPasswordClicked(" . $aRow["uid"] . ")'>重置密码</a>";
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
        $sql = "UPDATE " . $this->tblname ." SET deleted = 1 WHERE {$where}";
        log_message('info', "user delete sql = " . $sql);
        $query = $this->db->query($sql);
        return $this->db->affected_rows();
    }

    function deleteList($ids)
    {
        foreach ($ids as $uid) {
            $data = array(
                'deleted' => 1
            );
            $this->db->where('uid', $uid);
            $this->db->update($this->tblname, $data);
        }
        return $this->db->affected_rows();
    }

    function resetpassword($uid)
    {
        $data = array(
            'password' => 'aaaaaa'
        );
        $this->db->update($this->tblname, $data, "uid=".$uid);
    }

	function savemember()
	{
		$error_msg = "";
		$uid = $this->input->post("uid", true);
		$memberid = $this->input->post("memberid", true);
		$membername = $this->input->post("membername", true);
		$sex = $this->input->post("sex", true);
		$phonenum = $this->input->post("phonenum", true);
		$mailaddr = $this->input->post("mailaddr", true);
		$addrprovince = $this->input->post("addrprovince", true);
		$addrcity = $this->input->post("addrcity", true);
		$addrarea = $this->input->post("addrarea", true);
		$qqnum = $this->input->post("qqnum", true);
		$memberlevel = $this->input->post("memberlevel", true);
		$identifycard = $this->input->post("identifycard", true);

		if( $memberid != "" && $membername != "") {
			$data = array(
				'memberid' => $memberid,
				'membername' => $membername,
				'sex' => $sex,
				'phonenum' => $phonenum,
				'mailaddr' => $mailaddr,
				'addrprovince' => $addrprovince,
				'addrcity' => $addrcity,
				'addrarea' => $addrarea,
				'qqnum' => $qqnum,
				'memberlevel' => $memberlevel,
				'identifycard' => $identifycard
			);
			if ($uid == "")
			{
				$this->db->insert($this->tblname, $data);
			} else {
				$this->db->where("uid", $uid);
				$this->db->update($this->tblname, $data);
			}
		}else {
			$error_msg = ERROR_MSG_GOODS_ADD_GOODS;
		}

		return $error_msg;
	}

	function getmemberinfo($uid)
	{
		$this->db->where("uid", $uid);
		//$this->db->where("deleted", 0);
		$query = $this->db->get($this->tblname);
		if ($query->num_rows())
		{
			$result = $query->row_array();
		}

		return $this->common->convertUTF8($result);
	}

	function getprovinces()
	{
		$this->db->where("regiontype", 1);
		$query = $this->db->get($this->tbl_region);
		if ($query->num_rows())
		{
			$result = $query->result_array();
		}

		return $this->common->convertUTF8($result);
	}

	function getcities($province)
	{
		$result = array();
		$this->db->where("regionname", $province);
		$this->db->where("regiontype", 1);
		$query = $this->db->get($this->tbl_region);
		if ($query->num_rows())
		{
			$provinceinfo = $query->row();
			$this->db->where("regiontype", 2);
			$this->db->where("parentid", $provinceinfo->uid);
			$query = $this->db->get($this->tbl_region);
			if ($query->num_rows())
			{
				$result = $query->result_array();
			}
		}

		return $this->common->convertUTF8($result);
	}

	function getareas($city)
	{
		$result = array();
		$this->db->where("regionname", $city);
		$this->db->where("regiontype", 2);
		$query = $this->db->get($this->tbl_region);
		if ($query->num_rows())
		{
			$provinceinfo = $query->row();
			$this->db->where("regiontype", 3);
			$this->db->where("parentid", $provinceinfo->uid);
			$query = $this->db->get($this->tbl_region);
			if ($query->num_rows())
			{
				$result = $query->result_array();
			}
		}

		return $this->common->convertUTF8($result);
	}

	function getmemberlevels()
	{
		$result = array();
		for($i=0; $i < 6; $i++) {
			$row['uid'] = $i;
			$row['name'] = $this->order_model->get_memberlevel_value($i);
			$result[] = $row;
		}

		return $this->common->convertUTF8($result);
	}

}