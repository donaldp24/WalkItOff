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
		<?php echo form_open($rootUri.'goodsbanner_add/'.$ID, array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
			<div class="control-group">
				<label class="control-label" for="goodsid">产品名称 *</label>
				<div class="controls">
					<select id="goodsid" name="goodsid" class="chosen-select" >
						<?php foreach($goodslist as $goods) { ?>
						<option value="<?php echo $goods['uid']; ?>" 
						<?php if(isset($info) && isset($info['goodsid']) && $info['goodsid'] == $goods['uid']) echo 'selected'; ?>><?php echo $goods['name']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="showorder">显示排序 *</label>
				<div class="controls">
					<input type="text" class="input-medium spinner-input" id="showorder" name="showorder" maxlength="9" value="<?php if(isset($showorder)) echo $showorder;?>">
                    <span class="help-inline">数字越大排在越前面, 现在最大是 "<?php echo $max_showorder; ?>"</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="upload_btn1">推荐图片 *</label>
				<div class="controls">
					&nbsp;<input type=button class="btn btn-small btn-primary" id='upload_btn1' value="选择图片">&nbsp;&nbsp;&nbsp;只能上传一张图片<b>&nbsp;,&nbsp;&nbsp;</b>建议大小为<b>&nbsp;:&nbsp;</b>640<b>&nbsp;*&nbsp;</b>260<img src="<?php echo $rootUri; ?>www/images/ajax_loader.gif" style="display:none;" id="loading_photo1">
					<input id="img_name1" name="img_name1" type="hidden" value="<?php if( isset($info['imguri']) ) echo $info['imguri']; ?>" />
					<div id="img1" style="padding:5px;">
						<?php
							if( isset($info) && isset($info['imguri']) && $info['imguri'] != "" ) {
								echo "<img src='".$rootUri."www/images/uploads/products/image/".$info['imguri']."' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
								echo "<img src='".$rootUri."www/images/image_close.png' class='close_btn' onclick='removeMe1(\"".$info['imguri']."\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
							}
						?>
					</div>
				</div>
			</div>

			<div class = "form-actions">
				<input id="check_save" name="check_save" type="hidden" value="" />
				<button class="btn btn-success" id="save_btn" name="save_btn">
					<i class="icon-ok bigger-110"></i>
					提交
				</button>
				&nbsp; &nbsp; &nbsp;
				<button class="btn btn-info" id="cancel_btn" >
					<i class="icon-arrow-left bigger-110"></i>
					返回
				</button>
			</div> 
		<?php echo form_close();?>
		<div>
		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
</div><!--/.row-fluid-->