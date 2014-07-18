<?php

class Goods_model extends CI_Model
{

	var $tblname = "tbl_goods";
	var $tblproperty = "tbl_goods_property";
	var $tbllevel1 = "tbl_goodslevel1";
	var $tbllevel2 = "tbl_goodslevel2";
	var $tblgoods_img = "tbl_goods_img";
	var $tblgoods_relative = "tbl_goods_relatives";
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->library('common');
		$this->load->library('session');
    }

	public function getgoodslist()
	{
		$aColumns = array('A.uid as uid', 'C.name as level1name', 'B.name as level2name', 'A.name as name', 'A.style as style', 'A.goodsno as goodsno', 'A.price as price', 'A.reserveprice as reserveprice', 'A.imguri as imguri', 'D.allcount as allcount');

		$aRetColumns = array('uid', 'level1name', 'level2name', 'name', 'style', 'goodsno', 'price', 'reserveprice', 'imguri', 'allcount');

		$aFilterColumns = array('A.uid', 'C.name', 'B.name', 'A.name', 'A.style', 'A.goodsno', 'A.price', 'A.reserveprice', 'D.allcount');

		$aOrderColumns = array('A.uid',  'C.name', 'B.name', 'A.name', 'A.style', 'A.goodsno', 'A.price', 'A.reserveprice', 'D.allcount');
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "A.uid";
		
		/* DB table to use */
		$sTable = $this->tblname . " AS A INNER JOIN $this->tbllevel2 AS B ON A.level2id = B.uid " . 
			"INNER JOIN $this->tbllevel1 AS C ON B.levelid = C.uid " .
            "INNER JOIN (SELECT goodsid, sum(remain) as allcount FROM tbl_goods_property WHERE deleted = 0 GROUP BY goodsid ) as D ON A.uid = D.goodsid ";
				
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
			for ( $i=0 ; $i<count($aFilterColumns) ; $i++ )
			{
				$sWhere .= "".$aFilterColumns[$i]." LIKE '%". $_GET['sSearch'] ."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aFilterColumns) ; $i++ )
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
				$sWhere .= "".$aFilterColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
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

		$rst = $this->db->query($sQuery);
		if ($rst && $rst->num_rows() > 0) {
			$rResult = $rst->result_array();
		}
		/* Data set length after filtering */

		$sQuery = "
			SELECT COUNT($sIndexColumn) " . " 
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
					if ( $aRetColumns[$i] == 'imguri') {
						continue;
					} else if ( $aRetColumns[$i] == 'reserveprice' ||  $aRetColumns[$i] == 'price')
					{
						$row[] = $aRow[ $aRetColumns[$i] ];
					} else if ( $aRetColumns[$i] == 'name' ) {
						/* $row[] = "<a href='" . base_url() . "goods_add/" . $aRow["uid"] . "'><img width='80' height='60' src='" .
							base_url() . "www/images/uploads/products/image/" . $aRow["imguri"] . 
							"'>" . $aRow[ $aRetColumns[$i] ] . "</a>"; */
                        $row[] = "<a href='" . base_url() . "goods_add/" . $aRow["uid"] . "'>" . $aRow[ $aRetColumns[$i] ] . "</a>";
					} else if ( $aRetColumns[$i] != '' ) {
						/* General output */
						$row[] = $aRow[ $aRetColumns[$i] ];
					}
				}
				$remain = $this->getgoodsremain($aRow['uid']);
				$row[] = $remain;
				$output['aaData'][] = $row;
			}
		}

		return $this->common->convertUTF8($output);
	}

	function getgoodsremain($uid)
	{
		$remain = 0;
		$this->db->where("goodsid", $uid);
		$this->db->where("deleted", 0);
		$query = $this->db->get($this->tblproperty);
		if ($query->num_rows())
		{
			$result = $query->result();
			foreach($result as $row) {
				$remain += $row->remain;
			}
		}

		return $remain;
	}

	function getGoodsInfo( $uid ) {
        $arr_sel = array(
			"C.uid as uid", 
			"C.name as name", 
			"C.goodsno as goodsno", 
			"C.imguri as imguri",
			"C.level2id as level2id", 
			"level1.uid as level1id", 
			"C.reserveprice as reserveprice", 
			"C.price as price", 
			"C.style as style", 
			"C.showorder as showorder", 
			"C.createtime as createtime", 
			"C.imguri as img_exhib", 
			"C.imguri as img_detail",
			"C.imguri as goods_rel"
		);
		$this->db->select($arr_sel);
		$this->db->where('C.uid', $uid);
		//$this->db->where('C.deleted', 0);
		$this->db->join($this->tbllevel2." as level2","C.level2id=level2.uid");
		$this->db->join($this->tbllevel1." as level1","level2.levelid=level1.uid");

        $query = $this->db->get($this->tblname." as C");

		$result = array();
		if ($query->num_rows())
		{
			$result = $query->row_array();

			$imglists = $this->getGoodsImages($result['uid'], GOODS_IMAGE_EXHIBITION);
			$imgexhib = "";
			foreach ($imglists as $item)
			{
				if ($imgexhib != "")
				{
					$imgexhib .= ",";
				}
				$imgexhib .= $item["imguri"];
			}
			$result['img_exhib'] = $imgexhib;

			$imglists = $this->getGoodsImages($result['uid'], GOODS_IMAGE_DETAIL);
			$imgdetail = "";
			foreach ($imglists as $item)
			{
				if ($imgdetail != "")
				{
					$imgdetail .= ",";
				}
				$imgdetail .= $item["imguri"];
			}
			$result['img_detail'] = $imgdetail;

			$imglists = $this->getGoodsRelatives($result['uid']);
			$goodsrel = "";
			foreach ($imglists as $item)
			{
				if ($goodsrel != "")
				{
					$goodsrel .= ",";
				}
				$goodsrel .= $item["relid"];
			}
			$result['goods_rel'] = $goodsrel;
		}

		return $this->common->convertUTF8($result);
	}
	function getGoodsPropertyInfo( $uid ) {
		$query = $this->db->query("
			SELECT * FROM $this->tblproperty WHERE goodsid='{$uid}' AND deleted = 0
		");
		return $query->num_rows() ? $this->common->convertUTF8($query->result_array()) : array() ;
	}

	function getGoodsImages( $uid, $mode ) {
		$query = $this->db->query("
			SELECT * FROM $this->tblgoods_img WHERE goodsid='{$uid}' AND kind=$mode AND deleted = 0 ORDER BY showorder
		");
		return $query->num_rows() ? $this->common->convertUTF8($query->result_array()) : array() ;
	}

	function getGoodsRelatives( $uid) {
		$query = $this->db->query("
			SELECT * FROM $this->tblgoods_relative WHERE goodsid='{$uid}' AND deleted = 0 ORDER BY showorder
		");
		return $query->num_rows() ? $this->common->convertUTF8($query->result_array()) : array() ;
	}

	function add_goods($uid,$name,$no,$secondlevel,$pattern,$showorder,$img1,$img2,$img3,$order_price,$price,$kind,$sel_r_id) {
		$data = array(
			'name' => $name,
			'goodsno' => $no,
			'level2id' => $secondlevel,
			'imguri' => $img1,
			'reserveprice' => intval($order_price),
			'price' => intval($price),
			'style' => $pattern,
			'showorder' => $showorder,
			'createtime' => @date('Y-m-d')
		);
		if( $uid == 0 ) {
			$this->db->insert($this->tblname , $data);
			$uid = $this->db->insert_id();
			$this->setConcernData($uid, $kind, $img2, $img3, $sel_r_id, "add");
		}else {
			$this->db->where('uid', $uid);
			$this->db->update($this->tblname, $data);

			$this->setConcernData($uid, $kind, $img2, $img3, $sel_r_id, "edit");
		}
        
    }
	
	function setConcernData($uid,$kind, $img2,$img3,$sel_r_id, $mode)
	{
		if ($mode == "edit")
		{
			$this->db->where('goodsid', $uid);
			$this->db->update($this->tblproperty, array("deleted" => 1));
			$this->db->where('goodsid', $uid);
			$this->db->update($this->tblgoods_img, array("deleted" => 1));
			$this->db->where('goodsid', $uid);
			$this->db->update($this->tblgoods_relative, array("deleted" => 1));
		}
		if( $kind != "" ) {
			$records = explode('@@@', $kind);
			foreach( $records as $record ) {
				$values = explode('|||', $record);
				$property_data = array(
					'goodsid' => $uid,
					'size' => $values[0],
					'color' => $values[1],
					'remain' => $values[2],
				);
				$this->db->insert($this->tblproperty, $property_data);
			}
		}

		if( $img2 != "" ) {
			$records = explode(',', $img2);
			$sorder = 1;
			foreach( $records as $value ) {
				$goods_img = array(
					'goodsid' => $uid,
					'kind' => GOODS_IMAGE_EXHIBITION,
					'imguri' => $value,
					'showorder' => $sorder,
				);
				$this->db->insert($this->tblgoods_img, $goods_img);
				$sorder++;
			}
		}

		if( $img3 != "" ) {
			$records = explode(',', $img3);
			$sorder = 1;
			foreach( $records as $value ) {
				$goods_img = array(
					'goodsid' => $uid,
					'kind' => GOODS_IMAGE_DETAIL,
					'imguri' => $value,
					'showorder' => $sorder,
				);
				$this->db->insert($this->tblgoods_img, $goods_img);
				$sorder++;
			}
		}

		if( $sel_r_id != "" ) {
			$records = explode(',', $sel_r_id);
			$sorder = 1;
			foreach( $records as $value ) {
				$goods_rel = array(
					'goodsid' => $uid,
					'relid' => $value,
					'showorder' => $sorder,
				);
				$this->db->insert($this->tblgoods_relative, $goods_rel);
				$sorder++;
			}
		}
	}

	function getFirstLevel() {
		$query = $this->db->query("
			SELECT uid, name FROM $this->tbllevel1 WHERE deleted = 0
		");

		$result = array();
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
		}
		
		return $this->common->convertUTF8($result);
	}
	
	function getSecondLevel( $uid = 0 ) {
		$data = "";
		if ($uid == 0)
			return "";
		$query = $this->db->query("
			SELECT uid, name FROM $this->tbllevel2 WHERE levelid = $uid AND deleted = 0
		");
		if( $query->num_rows() ) {
			foreach( $query->result_array() as $records ) {
				$data .= $records['uid']."|||".$records['name']."@@@";
			}
			$data = substr( $data, 0, strlen($data)-3 );
		}
		return $this->common->convertUTF8($data);
	}
	
	function getRelativeGoodsName( $ID_str ) {
		$data = "";
		if( $ID_str != "" ) {
			$id_ary = explode(',', $ID_str);
			foreach( $id_ary as $uid ) {
				$result = $this->db->query("
					SELECT name,imguri,uid FROM $this->tblname WHERE uid={$uid} AND deleted = 0
				")->row_array();
				if( $result )
					$data .= $result['name']."|||".$result['imguri']."|||".$result['uid']."@@@";
				else
					continue;
			}
			$data = substr($data, 0, strlen($data)-3);
		}
		return $this->common->convertUTF8($data);
	}
	
	function getRelativeGoodsSearchResult( $firstleveluid, $secondleveluid, $search_name ) {
        $arr_sel = array("C.uid as uid", "C.name as goodsname", "C.goodsno as goodsno", "C.imguri as imguri");
		if( $search_name != "" ) {
            $this->db->like("C.name",$search_name);
		}

		if( $firstleveluid != "0" && $secondleveluid == "" ) {
		$this->common->php_debug($firstleveluid);
            $this->db->where("level1.uid",$firstleveluid);
		} else if( $firstleveluid != "0" && $secondleveluid != "" ) {

            $this->db->where("level1.uid",$firstleveluid);
            $this->db->where("level2.uid",$secondleveluid);
			$this->db->where("level1.deleted",0);
			$this->db->where("level2.deleted",0);
			$this->db->join($this->tbllevel2." as level2","C.level2id=level2.uid");
			$this->db->join($this->tbllevel1." as level1","level2.levelid=level1.uid");
//			$this->db->group_by(array("C.uid","level1.uid","level2.uid"));
		}
        $this->db->where("C.deleted",0);

		$data = "";
        $this->db->select($arr_sel);
        $query = $this->db->get($this->tblname." as C");
		if( $query->num_rows() ) {
			foreach( $query->result_array() as $record ) {
				$data .= $record['uid']."|||".$record['goodsname']."|||".$record["imguri"]."@@@";
			}
			$data = substr( $data, 0, strlen($data)-3 );
		}
		return $this->common->convertUTF8($data);
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
