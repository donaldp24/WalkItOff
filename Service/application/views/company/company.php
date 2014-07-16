<div class="page-header position-relative">
	<h1>
		<?php echo $mainmenu; ?>
		<small>
			<i class="icon-double-angle-right"></i>
			<?php echo $submenu; ?>
		</small>
	</h1>
</div><!--/.page-header-->

<div class="row-fluid">
	<div class="span12">
		<!--PAGE CONTENT BEGINS-->
		<?php if (isset($error_msg)) { ?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">
				<i class="icon-remove"></i>
			</button>
			<strong>
				<i class="icon-ok"></i>
				操作失败！
			</strong>
			<?php echo $error_msg; ?>
			<br>
		</div>
		<?php } ?>
		<div id = "workmain">
		<?php echo form_open($rootUri.'company/'.$ID, array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>

			<div class="control-group">
				<label class="control-label" for="upload_btn2">展示图片 *</label>
				<div class="controls">
					&nbsp;<input type="button" class="btn btn-small btn-primary" id='upload_btn2' value="选择图片">&nbsp;&nbsp;&nbsp;可上传多张图片<b>&nbsp;,&nbsp;&nbsp;</b>建议图片大小为<b>&nbsp;:&nbsp;</b>640<b>&nbsp;*&nbsp;</b>914<img src="<?php echo $rootUri; ?>www/images/ajax_loader.gif" style="display:none;" id="loading_photo2">
					<input id="imguri" name="imguri" type="hidden" value="<?php echo $imguri; ?>" />
					<div id="img2"><?php
                        $arr_img = explode(",", $imguri);
                    for($i = 0; $i < 5 ; $i++)
                    {
                        if (trim($arr_img[$i]) != '')
                        {
                            echo "<div id='contain_". $i . "' style='float:left;'>";
                            echo "<img src='".$rootUri."www/images/uploads/products/image/".$arr_img[$i]."' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' id = 'imguri_" . $i . "' name='imguri_" . $i . "'>";
                            echo "<img src='".$rootUri."www/images/image_close.png' class='close_btn' onclick='removeMe2(this, \"".$arr_img[$i]."\", " . $i . ")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
                            echo "</div>";
                        }
                        else
                        {
                            echo "<div id='contain_". $i . "' style='float:left;'>";
                            echo "<img src='".$rootUri."www/images/u42_normal.png' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' id = 'imguri_" . $i . "' name='imguri_" . $i . "'>";
                            echo "<img src='".$rootUri."www/images/image_close.png' class='close_btn' onclick='removeMe2(this, \"".$arr_img[$i]."\", " . $i . ")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
                            echo "</div>";
                        }
                    }

					?></div>
				</div>
			</div>

			<div class = "form-actions">
				<input id="check_save" name="check_save" type="hidden" value="" />
				<button class="btn btn-success" id="save_btn" name="save_btn">
					<i class="icon-ok bigger-110"></i>
					确认
				</button>
				&nbsp; &nbsp; &nbsp;
				<button class="btn" type="reset">
					<i class="icon-undo bigger-110"></i>
					重置
				</button>
				&nbsp; &nbsp; &nbsp;
				<button class="btn btn-info" id="cancel_btn" >
					<i class="icon-arrow-left bigger-110"></i>
					取消
				</button>
			</div> 

		<?php echo form_close();?>
		<div>
		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
</div><!--/.row-fluid-->