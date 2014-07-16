<?php

class Goodslevel_model extends CI_Model
{

	var $tbllevel1 = "tbl_goodslevel1";
	var $tbllevel2 = "tbl_goodslevel2";
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	public function getgoodslevellist()
	{
		$aColumns = array('uid',  'name', 'showorder');
		
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "uid";


		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		if(!$sidx) $sidx ="";
		// connect to the database
		$count = $this->db->query("SELECT COUNT(*) AS count FROM $this->tbllevel1")->row()->count;

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
	
		$sOrder = "";

		if ($sidx != "") {
			$sOrder = "ORDER BY $sidx $sord";
		}

		$sQuery = "
			SELECT TOP " . $limit . " ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $this->tbllevel1";


		$sQuery .= " WHERE deleted =0 AND uid NOT IN (SELECT TOP " . $start . " uid FROM $this->tbllevel1 $sOrder)";
		$sQuery .= " $sOrder";
		
		$result = $this->db->query($sQuery)->result_array();

		$responce['page'] = $page;
		$responce['total'] = $total_pages;
		$responce['records'] = $count;
		$i=0;

		foreach ($result as $key => $aColumns) {
			$responce['rows'][$i]['id']=$aColumns[$sIndexColumn];
			$responce['rows'][$i]['cell']=array();
			foreach ($aColumns as $key2 => $row) {
				$responce['rows'][$i]['cell'][$key2] = $aColumns[$key2];
			}

			$i++;
		}        
		//log_message('info', 'asdf');
		return $this->common->convertUTF8($responce);
	}

	public function manipulatelevel()
	{
		$oper = $this->input->post("oper");

		switch ($oper)
		{
			case "add":
				$this->additems();
				break;
			case "del":
				$this->deleteitems();
				break;
			case "edit":
				$this->edititems();
				break;
		}
	}

	public function deleteitems()
	{
		$ids = $this->input->post("id");
        $arr_id = explode(",", $ids);
        if (count($arr_id) == 0)
            return 0;

        $where = "uid = " . $arr_id[0];
        for($i = 1; $i < count($arr_id) - 1; $i++)
        {
            $where.= " OR uid = " . $arr_id[$i];
        }
 		$sql = "UPDATE $this->tbllevel1 SET deleted = 1 WHERE {$where}";
        $query = $this->db->query($sql);
   
		return $this->db->affected_rows();

	}

	public function edititems()
	{
		$id = $this->input->post("id");
		$name = $this->input->post("name");
		$showorder = $this->input->post("showorder");

		$data = array(
			'name' => $name,
			'showorder' => $showorder
		);

		$this->db->where("uid", $id);

		$this->db->update($this->tbllevel1, $data);

		return $this->db->affected_rows();

	}

	public function additems()
	{
		$name = $this->input->post("name");
		$showorder = $this->input->post("showorder");

		$data = array(
			'name' => $name,
			'showorder' => $showorder
		);

		$this->db->insert($this->tbllevel1, $data);

		return $this->db->affected_rows();

	}

	/* ------------To process level 2 data---------------- */
	public function getgoodslevel2list()
	{
		$level1id = $this->input->get("level1id");
		$aColumns = array('uid',  'name', 'showorder');
		
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "uid";


		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		if(!$sidx) $sidx ="";
		// connect to the database
		$count = $this->db->query("SELECT COUNT(*) AS count FROM $this->tbllevel2 WHERE levelid = $level1id")->row()->count;

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
	
		$sOrder = "";

		if ($sidx != "") {
			$sOrder = "ORDER BY $sidx $sord";
		}

		$sQuery = "
			SELECT TOP " . $limit . " ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $this->tbllevel2
			";
		
		$sQuery .= " WHERE deleted =0 AND levelid = $level1id AND uid NOT IN (SELECT TOP " . $start . " uid FROM $this->tbllevel2)";
		$sQuery .= " $sOrder";

		$result = $this->db->query($sQuery)->result_array();

		$responce['page'] = $page;
		$responce['total'] = $total_pages;
		$responce['records'] = $count;
		$i=0;

		foreach ($result as $key => $aColumns) {
			$responce['rows'][$i]['id']=$aColumns[$sIndexColumn];
			$responce['rows'][$i]['cell']=array();
			foreach ($aColumns as $key2 => $row) {
				$responce['rows'][$i]['cell'][$key2] = $aColumns[$key2];
			}

			$i++;
		}        
		//log_message('info', 'asdf');
		return $this->common->convertUTF8($responce);
	}

	public function manipulatelevel2()
	{
		$level1id = $this->input->get("level1id");
		$oper = $this->input->post("oper");

		switch ($oper)
		{
			case "add":
				$this->addlevel2items($level1id);
				break;
			case "del":
				$this->deletelevel2items($level1id);
				break;
			case "edit":
				$this->editlevel2items($level1id);
				break;
		}
	}

	public function deletelevel2items($level1id)
	{
		$ids = $this->input->post("id");
        $arr_id = explode(",", $ids);
        if (count($arr_id) == 0)
            return 0;

        $where = "uid = " . $arr_id[0];
        for($i = 1; $i < count($arr_id) - 1; $i++)
        {
            $where.= " OR uid = " . $arr_id[$i];
        }
 		$sql = "UPDATE $this->tbllevel2 SET deleted = 1 WHERE {$where} AND levelid = $level1id";
        $query = $this->db->query($sql);
   
		return $this->db->affected_rows();

	}

	public function editlevel2items($level1id)
	{
		$id = $this->input->post("id");
		$name = $this->input->post("name");
		$showorder = $this->input->post("showorder");

		if (trim($id) == "" || $id < 1 ) return 0;

		$data = array(
			'name' => $name,
			'levelid' => $level1id,
			'showorder' => $showorder
		);

		$this->db->where("uid", $id);

		$this->db->update($this->tbllevel2, $data);

		return $this->db->affected_rows();

	}

	public function addlevel2items($level1id)
	{
		$name = $this->input->post("name");
		$showorder = $this->input->post("showorder");

		$data = array(
			'name' => $name,
			'levelid' => $level1id,
			'showorder' => $showorder
		);

		$this->db->insert($this->tbllevel2, $data);

		return $this->db->affected_rows();

	}

}

?>