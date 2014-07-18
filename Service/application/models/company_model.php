<?php

class Company_model extends CI_Model {

    var $tblname = "tbl_comp_img";

	function __construct() {
        parent::__construct();
        $this->load->library('common');
        $this->load->library('session');
    }

    public function getList()
    {
        $aColumns = array('uid', 'imguri', 'showorder');

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
            $sQuery .= " AND uid NOT IN (SELECT TOP " . $_GET['iDisplayStart'] . " uid FROM $sTable)";
        } else {
            $sQuery .= " WHERE uid NOT IN (SELECT TOP " . $_GET['iDisplayStart'] . " uid FROM $sTable)";
        }
        $sQuery .= " $sOrder";

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

        foreach ($rResult as $key => $aRow)
        {
            $row = array();
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                if ( $aColumns[$i] == 'imguri' ) {
                    $row[] = "<a href='" . base_url() . "company/edit/" . $aRow["uid"] . "'><img src='".base_url() . $aRow[ $aColumns[$i] ] . "'/> </a>";
                } else if ( $aColumns[$i] != '' ) {
                    /* General output */
                    $row[] = $aRow[ $aColumns[$i] ];
                }
            }
            $output['aaData'][] = $row;
        }

        return $this->common->convertUTF8($output);
    }

    function getPhotos()
    {
        $this->db->select('imguri, showorder');
        $this->db->order_by('showorder');
        $query = $this->db->get($this->tblname);

        $ret = array();
        for ($i = 0; $i < 5; $i++)
        {
            $ret[$i] = '';
        }
        if (is_null($query) || $query->num_rows() <= 0)
            return $ret;

        $rows = $query->result();
        for ($i = 0; $i < 5; $i++)
        {
            $flag = 0;
            foreach ($rows as $row)
            {
                if ($row->showorder == $i + 1)
                {
                    $ret[$i] = $row->imguri;
                    $flag = 1;
                    break;
                }
            }
            if ($flag == 0)
                $ret[$i] = '';
        }
        return $ret;
    }

    function updatePhoto($photo_name, $showorder){
        $this->db->select('uid, showorder');
        $this->db->order_by('showorder');
        $this->db->where('showorder', $showorder);
        $query = $this->db->get($this->tblname);
        if (is_null($query))
            return 0;

        $arr = $query->result_array();
        $rows = $query->num_rows();
        $insert_idx = 0;

        if ($rows == 0)
        {
            $data = array(
                'imguri' => trim($photo_name),
                'showorder' => $showorder
            );
            $this->db->insert($this->tblname, $data);
            return $showorder;
        }
        else
        {
            $data = array(
                'imguri' => trim($photo_name),
                'showorder' => $showorder
            );
            $this->db->where('showorder', $showorder);
            $this->db->update($this->tblname, $data);
            return $showorder;
        }
    }

    function removePhoto($index)
    {
        $this->db->select('imguri');
        $this->db->where('showorder', $index);
        $query = $this->get($this->tblname);
        if ($query->num_rows() <= 0)
            return "";

        $row = $query->row();

        $this->db->where('showorder', $index);
        $this->db->delete($this->tblname);
        return $this->common->convertUTF8($row->imguri);
    }
    
}