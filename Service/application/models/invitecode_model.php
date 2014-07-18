<?php

class Invitecode_model extends CI_Model {
    var $tblname = "tbl_invite_code_genhis";
    var $tbl_invite_codes = "tbl_invite_codes";

	function __construct() {
        parent::__construct();
    }
    public function getList()
    {
        $aColumns = array('uid', 'DATEDIFF(s, \'19700101\', createtime) as createtime', 'count', 'uid');

		$aWhereColumns = array('uid', 'createtime', 'count');

		$aOrderColumns = array('uid', 'createtime', 'count');

        $aRetColumns = array('uid', 'createtime', 'count', 'uid');

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

		if (isset($rResult)) {
			foreach ($rResult as $key => $aRow)
			{
				$row = array();
				$flag = 0;
				for ( $i=0 ; $i<count($aRetColumns) ; $i++ )
				{
					if ( $aRetColumns[$i] == 'createtime' ) {
						$row[] = date('Y-m-d H:i:s', $aRow[ $aRetColumns[$i] ]);
					} else if ($aRetColumns[$i] == 'uid' && $flag == 0) {
						$row[] = $aRow[ $aRetColumns[$i] ];
						$flag = 1;
					} else if ($aRetColumns[$i] == 'uid' && $flag == 1) {
						$row[] = "<a href='" . base_url() . "invitecode/export/" . $aRow["uid"] . "'>导出</a>";
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

    function insert_data()
    {
		$count = $this->input->post("codecount");
        $data = array(
            'createtime' => date('Y-m-d H:i:s'),
            'count' => $count
        );
        $this->db->insert($this->tblname, $data);
        if ($this->db->affected_rows() <= 0)
            return 0;

        $gen_count = 0;
        $id = $this->db->insert_id();
       for ($i = 0; $i < $count; $i++)
        {
            $data = array(
                'invitehisid' => $id,
                'invitecode' => makerandom(7)
            );
            $this->db->insert($this->tbl_invite_codes, $data);
            if ($this->db->affected_rows() <= 0)
                return $gen_count;
            $gen_count++;
        }
        return $gen_count;
    }

	function getcodelist($id)
	{
		$rst = "";
		$this->db->where("invitehisid", $id);
		$query = $this->db->get($this->tbl_invite_codes);
		if ($query->num_rows())
		{
			$result = $query->result();
			foreach($result as $row)
			{
				$rst .= $row->invitecode . "\t \n";
			}
		}
		
		return $rst;
	}
}