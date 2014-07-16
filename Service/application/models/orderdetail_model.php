<?php

class Orderdetail_model extends CI_Model
{

	var $tblname = "tbl_order";
	var $tbl_member = 'tbl_member';
	var $tbl_member_pos = 'tbl_member_pos';
	var $tbl_goods = 'tbl_goods';
	var $tbl_goods_property = 'tbl_goods_property';
	var $tbl_order_goods = 'tbl_order_goods';

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function getorderinfo($uid)
	{
		$aColumns = array('A.uid as primaryuid',  'A.orderno', 'A.memberid', 
			'DATEDIFF(s, \'19700101\', A.ordertime) as ordertime', 'A.status', 'A.posid', 'A.sendtime',
			'A.sendingno', 'A.seat', 'A.cancelreason as cancelreason',
			'B.memberlevel', 
			'C.receivername', 'C.addrprovince', 'C.addrcity', 'C.addrarea', 'C.addrstreet', 'C.phonenum');
		
		$aRetColumns = array('primaryuid', 'orderno', 'ordertime', 'receivername', 'addrprovince', 'reserveprice', 'status', 'memberlevel');

		$aOrderColumns = array('orderno', 'ordertime', 'receivername', '', 'status', 'memberlevel');

		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "A.uid";
		
		/* DB table to use */
		$sTable = $this->tblname . " AS A INNER JOIN $this->tbl_member AS B ON A.memberid = B.uid " . 
			"INNER JOIN $this->tbl_member_pos AS C ON A.posid = C.uid ";
					
		$sWhere = " WHERE A.deleted = 0 AND A.uid = $uid";
		
		$sQuery = "
			SELECT ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable
			$sWhere";
		
		$rst = $this->db->query($sQuery);
		if ($rst && $rst->num_rows() > 0) {
			$rResult = $rst->row_array();
		}

		return $this->common->convertUTF8($rResult);
	}

	function getmemberpos($posid)
	{
		$rst = array();
        $arr_sel = array(
			"A.uid as uid", 
			"A.receivername as receivername", 
			"A.phonenum as phonenum", 
			"A.landline as landline",
			"A.addrprovince as addrprovince", 
			"A.addrcity as addrcity", 
			"A.addrarea as addrarea", 
			"A.addrstreet as addrstreet", 
			"A.postaddr as postaddr", 
			"B.memberlevel as memberlevel"
		);

		$this->db->select($arr_sel);
		$this->db->where("A.uid", $posid);
		//$this->db->where("A.deleted", 0);
		$this->db->join($this->tbl_member." as B","B.uid=A.memberid");

		$query = $this->db->get($this->tbl_member_pos . " as A");
		if ($query->num_rows())
		{
			$result = $query->row_array();
		}
        if (isset($result))
		    return $this->common->convertUTF8($result);
        return array();
	}

	function getordergoods($orderid)
	{
		$rst = array();
		$totalprice = 0;
		$totalcount = 0;

		$arr_sel = array(
			"A.uid as uid", 
			"A.orderid as orderid", 
			"A.goodsid as goodsid", 
			"A.propertyid as propertyid",
			"A.reserveprice as reserveprice", 
			"A.price as price", 
			"A.quantity as quantity", 
			"B.name as goodsname",
			"C.size as size",
			"C.color as color",
			"C.remain as remain"
		);

		$this->db->select($arr_sel);
		$this->db->where("A.orderid", $orderid);
		$this->db->where("A.deleted", 0);
		$this->db->join($this->tbl_goods." as B","B.uid=A.goodsid");
		$this->db->join($this->tbl_goods_property." as C","C.uid=A.propertyid");

		$query = $this->db->get($this->tbl_order_goods . " as A");

		if ($query->num_rows())
		{
			$result = $query->result_array();
			foreach($result as $row)
			{
				$totalprice = $totalprice + $row["reserveprice"] * $row['quantity'];
				$totalcount = $totalcount + $row["quantity"];
			}
		}
		$rst['totalprice'] = $totalprice;
		$rst['totalcount'] = $totalcount;
		$rst['ordergoods'] = $result;

		return $this->common->convertUTF8($rst);
	}

	function update_sendingno()
	{
		$orderid = $this->input->post("orderid");
		$sendno = $this->input->post("sendno");

		$data = array(
		   'sendingno' => $sendno,
		   'status' => ORDER_STATUS_ALREADY_DELIVER
		);
		$this->db->where("uid", $orderid);
		$this->db->update($this->tblname, $data);

        return $this->db->affected_rows();
	}

	function completeorder()
	{
		$orderid = $this->input->post("orderid");

		$data = array(
		   'status' => ORDER_STATUS_ALREADY_RECEIVE
		);
		$this->db->where("uid", $orderid);
		$this->db->update($this->tblname, $data);

        return $this->db->affected_rows();
	}

	function cancelorder()
	{
		$orderid = intval($this->input->post("orderid"));
		$cancelreason = $this->input->post("cancelreason");

		$data = array(
		   'status' => ORDER_STATUS_ALREADY_CANCEL,
		   'cancelreason' => $cancelreason
		);
		$this->db->where("uid", $orderid);
		$this->db->update($this->tblname, $data);

        $ret = $this->db->affected_rows();

        $this->db->where("orderid", $orderid);
        $this->db->where("deleted", 0);
        $this->db->select("goodsid, propertyid, quantity");
        $query = $this->db->get($this->tbl_order_goods);
        if ($query != null)
        {
            foreach ($query->result() as $row)
            {
                $propertyid = $row->propertyid;
                $quantity = intval($row->quantity);

                // get remain count
                $this->db->where("uid", $propertyid);
                $this->db->where("deleted", 0);
                $this->db->select("remain");
                $subquery = $this->db->get($this->tbl_goods_property);
                if ($subquery == null)
                    continue;
                if ($subquery->num_rows() != 1)
                    continue;
                $subrow = $subquery->row();
                $remain = intval($subrow->remain);

                // add remain count
                $remain += $quantity;

                $data = array('remain' => $remain);
                $this->db->where("uid", $propertyid);
                $this->db->update($this->tbl_goods_property, $data);
            }
        }

        return $ret;
	}

	function changereceiver()
	{
		$posid = $this->input->post("posid");
		$receiver = $this->input->post("receiver");
		$phonenum = $this->input->post("phonenum");
		$postaddr = $this->input->post("postaddr");
		$addrprovince = $this->input->post("addrprovince");
		$addrcity = $this->input->post("addrcity");
		$addrarea = $this->input->post("addrarea");
		$addrstreet = $this->input->post("addrstreet");

		$data = array(
		   'receivername' => $receiver,
		   'phonenum' => $phonenum,
		   'postaddr' => $postaddr,
		   'addrprovince' => $addrprovince,
		   'addrcity' => $addrcity,
		   'addrarea' => $addrarea,
		   'addrstreet' => $addrstreet,
		);
		$this->db->where("uid", $posid);
		$this->db->update($this->tbl_member_pos, $data);

        return $this->db->affected_rows();
	}

}

?>