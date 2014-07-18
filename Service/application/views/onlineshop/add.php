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
		<?php echo form_open($rootUri.'onlineshop_add/'.$ID, array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>

            <div class="control-group">
                <label class="control-label" for="name">店铺名称 *</label>
                <div class="controls">
                    <input id="name" name="name" type="text" placeholder="请输入店铺名称" value="<?php if(isset($name)) echo $name;?>" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="upload_btn">缩略图片 *</label>
                <div class="controls">
                    &nbsp;<input type=button class="btn btn-small btn-primary" id='upload_btn' value="选择图片">&nbsp;&nbsp;&nbsp;只能上传一张图片<b>&nbsp;,&nbsp;&nbsp;</b>建议大小为<b>&nbsp;:&nbsp;</b>44<b>&nbsp;*&nbsp;</b>44<img src="<?php echo $rootUri; ?>www/images/ajax_loader.gif" style="display:none;" id="loading_photo">
                    <input id="imguri" name="imguri" type="hidden" value="<?php if( isset($imguri) ) echo $imguri; ?>" />
                    <div id="img1" style=" padding:5px;">
                        <?php
                        if( isset($imguri) && $imguri != "" ) {
                            echo "<img src='".$rootUri."www/images/uploads/products/image/".$imguri."' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
                            echo "<img src='".$rootUri."www/images/image_close.png' class='close_btn' onclick='removeMe(\"".$imguri."\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
                        }
                        ?>
                    </div>
                </div>
            </div>

			<div class="control-group">
				<label class="control-label" for="linkurl">店铺地址 *</label>
				<div class="controls">
					<input id="linkurl" name="linkurl" type="text" placeholder="请输入店铺网站地址" value="<?php if(isset($linkurl)) echo $linkurl;?>" />
				</div>
			</div>
            <div class="control-group">
                <label class="control-label" for="showorder">显示排序 *</label>
                <div class="controls">
                    <input type="text" class="input-medium spinner-input" id="showorder" name="showorder" maxlength="9" value="<?php if(isset($showorder)) echo $showorder;?>">
                    <span class="help-inline">数字越大排在越前面, 现在最大是 "<?php echo $max_showorder; ?>"</span>
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