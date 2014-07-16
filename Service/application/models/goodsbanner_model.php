<?php

class Goodsbanner_model extends CI_Model
{

	var $tblname = "tbl_goods_banner";
	var $tblgoods = "tbl_goods";
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function getgoodsbannerlist()
	{
		$aColumns = array('A.uid as uid', 'A.goodsid as goodsid', 'name as goodsname', 'B.imguri as goodsimg', 'A.imguri as imguri', 'A.showorder as showorder');

		$aRetColumns = array('uid', 'goods', 'imguri', 'showorder');

		$aSearchColumns = array('A.uid', 'name', 'B.imguri', 'A.showorder');

        $aOrderColumns = array('A.uid', 'name', 'B.imguri', 'A.showorder');

		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "A.uid";
		
		/* DB table to use */
		$sTable = $this->tblname . " AS A INNER JOIN $this->tblgoods AS B ON A.goodsid = B.uid";
				
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
			for ( $i=0 ; $i<count($aSearchColumns) ; $i++ )
			{
				if ($aSearchColumns[$i] != "A.uid" && $aSearchColumns[$i] != "B.imguri"  && $aSearchColumns[$i] != "A.showorder")
				$sWhere .= "".$aSearchColumns[$i]." LIKE '%". $_GET['sSearch'] ."%' OR ";
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

		if ($sWhere == "") {
			$sWhere .= " WHERE A.deleted = 0";
		} else {
			$sWhere .= " AND A.deleted = 0";
		}

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "
			SELECT TOP " . $_GET['iDisplayLength'] . " ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable
			$sWhere ";

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
			SELECT COUNT(A.uid) " . " 
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
					if ( $aRetColumns[$i] == 'goods' ) {
						/*$row[] = "<a href='" . base_url() . "goodsbanner_add/" . $aRow['uid'] . "'>" .
							"<img width='120' height='80' src='" . base_url() . "www/images/uploads/products/image/" . $aRow['goodsimg'] . "' />" .
							"<label>" . $aRow['goodsname'] . "</label></a>";*/
                        $row[] = "<a href='" . base_url() . "goodsbanner_add/" . $aRow['uid'] . "'>" .
							"<label>" . $aRow['goodsname'] . "</label></a>";
					} else if ( $aRetColumns[$i] == 'imguri' ) {
						$row[] = "<img width='120' height='80' src='" . base_url() . "www/images/uploads/products/image/" . $aRow[ $aRetColumns[$i] ] . "'>";
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

	function getGoods()
	{
		$this->db->where('deleted', 0);

        $query = $this->db->get($this->tblgoods);

		$result = array();
		if ($query->num_rows())
		{
			$result = $query->result_array();
		}

		return $this->common->convertUTF8($result);
	}

	function getGoodsBannerInfo( $uid ) {
		$this->db->where('uid', $uid);
		//$this->db->where('deleted', 0);

        $query = $this->db->get($this->tblname);

		$result = array();
		if ($query->num_rows())
		{
			$result = $query->row_array();
		}

		return $this->common->convertUTF8($result);
	}

	function add_goodsbanner($uid,$goodsid,$imguri,$showorder) {
		$data = array(
			'goodsid' => $goodsid,
			'imguri' => $imguri,
			'showorder' => $showorder
		);
		if( $uid == 0 ) {
			$this->db->insert($this->tblname , $data);
		}else {
			$this->db->where('uid', $uid);
			$this->db->update($this->tblname, $data);
		}
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
        $query = $this->db->query($sql);
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

?>
