<?php

class Shop_model extends CI_Model {
    var $tblname = "tbl_online_shop";

	function __construct() {
        parent::__construct();
        $this->load->library('common');
        $this->load->library('session');
    }

    public function getList()
    {
        $aColumns = array('uid', 'name', 'linkurl',  'showorder',  'imguri');

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
                    $sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
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
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                $sWhere .= "".$aColumns[$i]." LIKE '%". $_GET['sSearch'] ."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }

        /* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
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
                $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }

        if ($sWhere == "")
            $sWhere = " WHERE deleted = 0";
        else
            $sWhere .= " AND deleted = 0";

        log_message('info', 'shop model where - '  . $sWhere);
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

        $rst = $this->db->query($sQuery);
        if ($rst && $rst->num_rows() > 0) {
            $rResult = $rst->result_array();
        }
        else
            $rResult = array();
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
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    if ( $aColumns[$i] == 'name' ) {
                        $row[] = "<a href='" . base_url() . "onlineshop_add/" . $aRow["uid"] . "'><img width='80' height='60' src='" .
                            base_url() . "www/images/uploads/products/image/" . $aRow["imguri"] .
                            "'>" . $aRow[ $aColumns[$i] ] . "</a>";
                    } else if ($aColumns[$i] == 'linkurl') {
                        $row[] = "<a href='" . $aRow[ $aColumns[$i] ] . "' target='_blink'>" . $aRow[ $aColumns[$i] ];
                    } else if ( $aColumns[$i] != '' ) {
                        /* General output */
                        $row[] = $aRow[ $aColumns[$i] ];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        return $this->common->convertUTF8($output);
    }

	//获取
    function getShop($uid)
    {
        $this->db->select('uid, name, linkurl, imguri, showorder');
        $this->db->where('uid', $uid);
        $query = $this->db->get($this->tblname);
        if ($query == null)
            return null;
        return $this->common->convertUTF8($query->result_array());
    }

    function addShop($uid, $name, $linkurl, $imguri, $showorder)
    {
        $data = array(
            'name' => $name,
            'linkurl' => $linkurl,
            'imguri' => $imguri,
            'showorder' => intval($showorder)
        );
        if ($uid == 0)
        {
            $this->db->insert($this->tblname, $data);
            if ($this->db->affected_rows() > 0)
                return $this->db->insert_id();
        }
        else
        {
            $this->db->where('uid', $uid);
            $this->db->update($this->tblname, $data);
            if ($this->db->affected_rows() > 0)
                return $uid;
        }
        return 0;
    }

    function deleteShop($uid)
    {
        $this->db->where('uid', $uid);
        $this->db->delete($this->tblname);
        if ($this->db->affected_rows() > 0)
            return $uid;
        return 0;
    }

    function deleteShopList($arrayUid)
    {
        foreach ($arrayUid as $uid)
        {
            $ret = deleteShop($uid);
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

        $where = "uid = " . $arr_id[0];
        for($i = 1; $i < count($arr_id) - 1; $i++)
        {
            $where .= " OR uid = " . $arr_id[$i];
        }
        $query = $this->db->query("UPDATE " . $this->tblname . " SET deleted = 1 WHERE {$where}");
        return $this->db->affected_rows();
    }

    function get_max_showorder()
    {
        $sql = "SELECT max(showorder) as max_showorder FROM " . $this->tblname . " WHERE deleted = 0";
        $query = $this->db->query($sql);
        if ($query == null || $query->num_rows() <= 0)
            return 0;
        $max_val = $query->row()->max_showorder;
        return $max_val;
    }
}