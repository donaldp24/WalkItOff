<?php

class Message_model extends CI_Model {
    var $tblname = "tbl_message";

	function __construct() {
        parent::__construct();
        $this->load->library('common');
        $this->load->library('session');
    }

    public function getList()
    {
        $aColumns = array('uid',  'title', 'convert(varchar(19), createtime, 120) as createtime', 'allowread');
        $aRetColumns = array('uid',  'title', 'createtime', 'allowread');

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
                    if ($aRetColumns[$i] != 'forpublic')
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
                        $row[] = "<a href='" . base_url() . "message_add/" . $aRow["uid"] . "'>" . $aRow[ $aRetColumns[$i] ] . "</a>";
                    } else if ($aRetColumns[$i] == 'createtime') {
                        //$row[] = substr($aRow[ $aRetColumns[$i]], 0, 10);
                        $row[] = $aRow[ $aRetColumns[$i] ];
                    } else if ($aRetColumns[$i] == 'allowread') {
                        if ($aRow[ $aRetColumns[$i] ] == 0)
                            $row[] = "非公告";
                        else
                            $row[] = "公告";
						/*
                    } else if ($aRetColumns[$i] == 'forpublic') {
                        if ($aRow['allowread'] == 0)
                            $row[] = "<a href='" . base_url() . "message/public" . $aRow["uid"] . "' onclick='return onPublic(".$aRow["uid"].")'>公告</a>";
                        else
                            $row[] = "<a href='" . base_url() . "message/private" . $aRow["uid"] . "' onclick='return onPrivate(".$aRow["uid"].")'>取消公告</a>";
							*/
                    } else if ( $aRetColumns[$i] != '' ) {
                        /* General output */
                        $row[] = $aRow[$aRetColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        return $this->common->convertUTF8($output);
    }

    function getMessage($uid)
    {
        $this->db->where('uid', $uid);
        $query = $this->db->get($this->tblname);
		if ($query->num_rows())
		{
			$result = $query->row_array();
		}

        return $this->common->convertUTF8($result);
    }

    function addMessage($uid)
    {
		$title = $this->input->post("title", true);
		$contents = $this->input->post("contents");
		$allowread = $this->input->post("allowread", true);

        $data = array(
            'title' => $title,
            'contents' => $contents,
            'createtime' => date("Y-m-d H:i:s"),
            'allowread' => ($allowread == "on") ? 1 : 0
        );

        if ($uid > 0)
        {
            $this->db->where('uid', $uid);
            $this->db->update($this->tblname, $data);
            if ($this->db->affected_rows() > 0)
                return $uid;
        }
        else
        {
            $this->db->insert($this->tblname, $data);
            if ($this->db->affected_rows() > 0)
                return $this->db->insert_id();
        }
        return 0;
    }


    function deleteMessage($uid)
    {
        $this->db->where('uid', $uid);
        $this->db->delete($this->tblname);
        if ($this->db->affected_rows() > 0)
            return $uid;
        return 0;
    }

    function deleteMessageList($arrayUid)
    {
        foreach ($arrayUid as $uid)
        {
            $ret = deleteMessage($uid);
            if ($ret == 0)
                return $ret;
        }
        return count($arrayUid);
    }

    function delete_id($ids)
    {
        $arr_id = explode(",", $ids);
        if (count($arr_id) == 0)
            return 0;

        $where = "uid = ". $arr_id[0];
        for($i = 1; $i < count($arr_id) - 1; $i++)
        {
            $where.=" OR uid = " . $arr_id[$i];
        }
        $query = $this->db->query("UPDATE ". $this->tblname . " SET deleted = 1 WHERE {$where}");
        return $this->db->affected_rows();
    }

    function change_status($id, $val)
    {
        $this->db->where("uid", $id);
        $this->db->set("allowread", $val);
        $this->db->update($this->tblname);

        if ($this->db->affected_rows() <= 0)
            return 0;
        return $id;
    }
}