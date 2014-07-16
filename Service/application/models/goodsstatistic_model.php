<?php

class GoodsStatistic_model extends CI_Model {

    var $tbl_goods = "tbl_goods";
    var $tbl_order = "tbl_order";
    var $tbl_order_goods = "tbl_order_goods";

    function __construct() {
        parent::__construct();
        $this->load->library('common');
        $this->load->library('session');
    }

    public function getList()
    {
        /*
         SELECT        a.uid, a.orderno, a.ordertime, b.allprice, d.receivername, { fn CONCAT(d.addrprovince, d.addrcity) } AS addr
        FROM            tbl_order AS a INNER JOIN
                             (SELECT        orderid, SUM(reserveprice * quantity) AS allprice
                               FROM            tbl_order_goods
                               GROUP BY orderid) AS b ON a.uid = b.orderid INNER JOIN
                         tbl_member AS c ON a.memberid = c.uid INNER JOIN
                         tbl_member_pos AS d ON a.posid = d.uid
         */
        /*
         SELECT        a.name as name, a.goodsno as goodsno, b.allprice as allprice, b.allcount, b.allprice / b.allcount AS avgprice, a.uid as uid
        FROM            tbl_goods AS a INNER JOIN
                             (SELECT        goodsid, SUM(quantity) AS allcount, SUM(quantity * reserveprice) AS allprice
                               FROM            tbl_order_goods
                               GROUP BY goodsid) AS b ON a.uid = b.goodsid
         */
        $aColumns = array('a.name as name', 'C.size as size', 'C.color as color', 'b.quantity as quantity', 'E.membername as membername', 'CONVERT(varchar(19), d.ordertime, 120) as ordertime', 'b.uid as icol');

        $aOrderColumns = array('a.name', 'c.size', 'c.color', 'b.quantity', 'e.membername', 'd.ordertime', 'b.uid');

        $aSearchColumns = array('name', 'size', 'color', 'quantity', 'membername', 'ordertime', 'icol');

		$aRetColumns = array('name', 'size', 'color', 'quantity', 'membername', 'ordertime', 'icol');

        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = "b.uid";

        /* DB table to use */
        $sTable = "tbl_goods AS a INNER JOIN
                 tbl_order_goods AS b ON a.uid = b.goodsid INNER JOIN
				 tbl_goods_property AS C on b.propertyid = C.uid INNER JOIN
				 tbl_order AS D on b.orderid = D.uid INNER JOIN
				 tbl_member AS E on e.uid = d.memberid";

        /*ss
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
            for ( $i=0 ; $i<count($aSearchColumns) ; $i++ )
            {
                $sWhere .= "".$aSearchColumns[$i]." LIKE '%". $_GET['sSearch'] ."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }

        /* Individual column filtering */
        for ( $i=0 ; $i<count($aSearchColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
				if ($aSearchColumns[$i] == "ordertime") {
					$compdates = explode("~", $_GET['sSearch_'.$i]);
					if (count($compdates) > 1 && (trim($compdates[0]) != "" || trim($compdates[1]) != "")) {
						if ( $sWhere == "" )
						{
							$sWhere = "WHERE ";
						}
						else
						{
							$sWhere .= " AND ";
						}

						if (trim($compdates[0]) != "") {
							$sWhere .=  $aSearchColumns[$i] . " >= '" . $compdates[0] . " 00:00:00'";
						}
						if (trim($compdates[0]) != "" && trim($compdates[1]) != "") {
							$sWhere .= " AND " ;
						}
						if (trim($compdates[1]) != "") {
							$sWhere .= $aSearchColumns[$i] . " <= '" . $compdates[1] . " 23:59:59'";
						}

					}
				} else {
					if ( $sWhere == "" )
					{
						$sWhere = "WHERE ";
					}
					else
					{
						$sWhere .= " AND ";
					}
					$sWhere .= "".$aSearchColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
				}
            }
        }

        if ($sWhere == "")
            $sWhere = " WHERE b.deleted = 0";
        else
            $sWhere .= " AND b.deleted = 0";

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
			SELECT COUNT(b.uid) " . "
			FROM   $sTable
			$sWhere";

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
			WHERE b.deleted = 0
		";

        log_message("info", "total count - " . $sQuery);

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

		if (isset($rResult)) {
			foreach ($rResult as $key => $aRow)
			{
                $row = array();
				for ( $i=0 ; $i<count($aRetColumns) ; $i++ )
				{
					if ( $aRetColumns[$i] == 'ordertime' ) {
						//$row[] = date('Y-m-d H:i:s', $aRow[ $aRetColumns[$i] ]);
                        $row[] = $aRow[ $aRetColumns[$i] ];
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

    function get_graph_data($goods_id)
    {
		$areas = array("深圳", "广州", "东莞", "东山", "北京");
        /*$field = array("area","sum(count) as allcount");
        $field1 = array("area","allcount");
        $this->db->where("GoodsID",$goods_id);
        if ( $_POST["from"] != "" && $_POST["to"] !="")
        {
            $this->db->where("Time>=",$_POST["from"]);
            $this->db->where("Time<=",$_POST["to"]);
        }
        $this->db->join("mst_member","order_tbl.UserID=mst_member.ID");
        $this->db->group_by("area");
        $query = $this->db->get("order_tbl");
        return DisplayData($query->result_array(),$field1);
        */
    }

	function getexcellist()
	{
		$rst = "";

        $aColumns = array('a.name as name', 'C.size as size', 'C.color as color', 'b.quantity as quantity', 'E.membername as membername', 'CONVERT(varchar(19), d.ordertime, 120) as ordertime', 'b.uid as icol');

		$sTable = "tbl_goods AS a INNER JOIN
                 tbl_order_goods AS b ON a.uid = b.goodsid INNER JOIN
				 tbl_goods_property AS C on b.propertyid = C.uid INNER JOIN
				 tbl_order AS D on b.orderid = D.uid INNER JOIN
				 tbl_member AS E on e.uid = d.memberid";
 
		$sWhere = "";

		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];

		$goodsname = $_POST['name0'];
		$membername = $_POST['name4'];
		if ((trim($startdate) != "" || trim($enddate) != "")) {
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}

			if (trim($startdate) != "") {
				$sWhere .=  "ordertime >= '" . $startdate . " 00:00:00'";
			}
			if (trim($startdate) != "" && trim($enddate) != "") {
				$sWhere .= " AND " ;
			}
			if (trim($enddate) != "") {
				$sWhere .= "ordertime <= '" . $enddate . " 23:59:59'";
			}

		}

		if ($goodsname != "产品名称") {
			if ($sWhere == "")
				$sWhere .= " WHERE a.name like '%" .$goodsname. "%'";
			else
				$sWhere .= " AND a.name like '%".$goodsname."%'";
		}

		if ($membername != "购买会员") {
			if ($sWhere == "")
				$sWhere .= " WHERE e.membername like '%" .$membername. "%'";
			else
				$sWhere .= " AND e.membername like '%".$membername."%'";
		}

        if ($sWhere == "")
            $sWhere .= " WHERE b.deleted = 0";
        else
            $sWhere .= " AND b.deleted = 0";

        $sQuery = "
			SELECT " . str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable
			$sWhere";

		$rst = $this->db->query($sQuery);
		$result = chr(239) . chr(187) . chr(191) . "产品名称,尺码,颜色,销售量,会员,销售时间 \r\n";
        if ($rst && $rst->num_rows() > 0) {
            $rResult = $rst->result_array();
	        $rResult = $this->common->convertUTF8($rResult);

			foreach($rResult as $row)
			{
				$result .= $row['name'] . ",";
				$result .= $row['size'] . ",";
				$result .= $row['color'] . ",";
				$result .= $row['quantity'] . ",";
				$result .= $row['membername'] . ",";
				$result .= $row['ordertime'] . ",\r\n";
			}
        }

		return $result;
	}

}