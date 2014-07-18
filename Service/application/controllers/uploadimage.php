<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UploadImage extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
//		$this->load->model(array('seo_model'));
		$this->load->library('image_lib');
	}

    function upload()
    {
		$ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
		$name = date("YmdHis");
		$basename = $name.".".$ext;
		$uploadfile =  "./www/images/uploads/products/image/".$basename;
		//die($_FILES['userfile']['tmp_name']);

		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {    
		  echo $basename;
		} else {
		  echo "Can not upload the file.";
		}
    }

    function resize()
	{

		$file_name = $_POST['photo'];
		$kind = $_POST['kinds'];

		$kinds = explode(",", $kind);
		foreach ($kinds as $item) {
			switch ($item)
			{
				case GOODS_THUMB_PREFIX:
					$this->cropimage($file_name, GOODS_THUMBNAIL_SIZE_X, GOODS_THUMBNAIL_SIZE_Y, $item);
					break;
				case GOODS_EXHIBITION_PREFIX:
					$this->cropimage($file_name, GOODS_EXHIBITION_SIZE_X, GOODS_EXHIBITION_SIZE_X, $item);
					break;
				case GOODS_DETAIL_PREFIX:
					$this->cropimage($file_name, GOODS_DETAIL_SIZE_X, GOODS_DETAIL_SIZE_X, $item);
					break;
				case COMPANY_IMAGE_PREFIX:
					$this->cropimage($file_name, COMPANY_IMAGE_SIZE_X, COMPANY_IMAGE_SIZE_X, $item);
					break;
				case BANNER_IMAGE_PREFIX:
					$this->cropimage($file_name, BANNER_IMAGE_SIZE_X, BANNER_IMAGE_SIZE_X, $item);
					break;
				case ONLINESHOP_IMAGE_PREFIX:
					$this->cropimage($file_name, ONLINESHOP_IMAGE_SIZE_X, ONLINESHOP_IMAGE_SIZE_X, $item);
					break;
			}
		}
	}

	function resizeimage($orgimg, $rwidth, $rheight)
	{
		$srcimg = FCPATH . "www/images/uploads/products/image/".$orgimg;
		$newimg = FCPATH . "www/images/uploads/products/image/tmp_".$orgimg;
		if (file_exists($srcimg)) {
			$imgsize = $this->_get_size($srcimg);
			$owidth = $imgsize['width'];
			$oheight = $imgsize['height'];

			$config['image_library'] = 'gd2';
			$config['source_image'] = $srcimg;
			$config['allowed_types'] = 'gif|jpg|png';
			$config['create_thumb'] = FALSE;
			$config['maintain_ratio'] = true;
			$config['quality'] = 100;
			$config['width'] = $rwidth;
			$config['height'] = $rheight;
			$config['thumb_marker'] = "";
			$dim = ($owidth / $oheight) - ($rwidth / $rheight);
			$config['master_dim'] = ($dim > 0)? "height" : "width";

			$config['new_image'] = $newimg;

			$this->image_lib->initialize($config);
			$this->image_lib->resize();
		}
	}

	function cropimage($orgimg, $cwidth, $cheight, $prefix, $del = 1)
	{
		$this->resizeimage($orgimg, $cwidth, $cheight);

		$srcimg = FCPATH . "www/images/uploads/products/image/tmp_".$orgimg;
		$newimg = FCPATH . "www/images/uploads/products/image/".$prefix.$orgimg;
		if (file_exists($srcimg)) {
			$imgsize = $this->_get_size($srcimg);
			$owidth = $imgsize['width'];
			$oheight = $imgsize['height'];

			$config['image_library'] = 'gd2';
			$config['source_image'] = $srcimg;
			$config['thumb_marker'] = "";
			$config['maintain_ratio'] = false;
			$config['quality'] = 100;
			$config['width'] = $cwidth;
			$config['height'] = $cheight;
			$config['x_axis'] = ($owidth - $cwidth + 1) / 2;
			$config['y_axis'] = ($oheight - $cheight + 1) / 2;
			$config['new_image'] = $newimg;
			$this->image_lib->initialize($config);
			$this->image_lib->crop();
			
			if ($del)
			{
				unlink($srcimg);
			}
		}
	}

	function _get_size($image)
    {
         $img = getimagesize($image);
         return Array('width'=>$img['0'], 'height'=>$img['1']);
    }
}