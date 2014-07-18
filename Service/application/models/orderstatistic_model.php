<?php

class OrderStatistic_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->library('common');
        $this->load->library('session');
    }
    public function getList()
    {
        /*
         SELECT        a.uid, a.orderno, a.ordertime, d.receivername, b.allprice, c.memberlevel, { fn CONCAT(d.addrprovince, d.addrcity) } AS addr
        FROM            tbl_order AS a INNER JOIN
                             (SELECT        orderid, SUM(reserveprice * quantity) AS allprice
                               FROM            tbl_order_goods
                               GROUP BY orderid) AS b ON a.uid = b.orderid INNER JOIN
                         tbl_member AS c ON a.memberid = c.uid INNER JOIN
                         tbl_member_pos AS d ON a.posid = d.uid
         */

        $aColumns = array('a.uid as uid', 'a.orderno as orderno', 'DATEDIFF(s, \'19700101\', a.ordertime) as ordertime', 'd.receivername as receivername', 'b.allprice', 'c.memberlevel as memberlevel', '{ fn CONCAT(d.addrprovince, d.addrcity) } AS addr');
        $aRetColumns = array('uid', 'orderno', 'ordertime', 'receivername', 'allprice', 'memberlevel', 'addr');

        $aWhereColumns = array('a.orderno', 'a.ordertime', 'd.receivername', 'b.allprice', 'c.memberlevel', '({ fn CONCAT(d.addrprovince, d.addrcity) })');

        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = "a.uid";

        /* DB table to use */
        $sTable = "tbl_order AS a INNER JOIN
                             (SELECT        orderid, SUM(reserveprice * quantity) AS allprice
                               FROM            tbl_order_goods
                               GROUP BY orderid) AS b ON a.uid = b.orderid INNER JOIN
                         tbl_member AS c ON a.memberid = c.uid INNER JOIN
                         tbl_member_pos AS d ON a.posid = d.uid";

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
            $sWhere = " WHERE a.deleted = 0";
        else
            $sWhere .= " AND a.deleted = 0";

        if ($sWhere == "")
            $sWhere = " WHERE a.status = 2";
        else
            $sWhere .= " AND a.status = 2";

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
            $sQuery .= " AND $sIndexColumn NOT IN (SELECT DISTINCT TOP " . $_GET['iDisplayStart'] . " $sIndexColumn FROM $sTable $sWhere $sOrder)";
        } else {
            $sQuery .= " WHERE $sIndexColumn NOT IN (SELECT DISTINCT TOP " . $_GET['iDisplayStart'] . " $sIndexColumn FROM $sTable $sOrder)";
        }
        $sQuery .= " $sOrder";

        $rst = $this->db->query($sQuery);
        if ($rst && $rst->num_rows() > 0) {
            $rResult = $rst->result_array();
        }
        /* Data set length after filtering */

        $sQuery = "
			SELECT COUNT(a.uid) " . "
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
				for ( $i=0 ; $i<count($aRetColumns) ; $i++ )
				{
					if ( $aRetColumns[$i] == 'orderno' ) {
						$row[] = "<a href='" . base_url() . "orderdetail/" . $aRow["uid"] . "'>" . $aRow[$aRetColumns[$i]] . "</a>";
					} else if ( $aRetColumns[$i] == 'memberlevel' ) {
						$row[] = $this->order_model->get_memberlevel_value($aRow[$aRetColumns[$i]]);
					} else if ( $aRetColumns[$i] == 'ordertime' ) {
						$row[] = date('Y-m-d H:i:s', $aRow[ $aRetColumns[$i] ]);
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
}