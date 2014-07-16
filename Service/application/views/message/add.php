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
		<?php echo form_open($rootUri.'message_add/'.$ID, array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>

			<div class="control-group">
				<label class="control-label" for="title">标题 *</label>
				<div class="controls">
					<input id="title" name="title" type="text" placeholder="请输入标题" value="<?php if(isset($title)) echo $title;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="contents">文章内容 *</label>
				<div class="controls">
					<textarea id="contents" name="contents" style="width:676px;height:350px;" rows="8"><?php if(isset($contents)) echo $contents;?></textarea>						

				</div>
			</div>
<!--
            <div class="control-group">
                <label class="control-label" for="upload_btn">是否公告 *</label>
                <div class="controls">
					<input name="allowread" id="allowread" class="ace ace-switch ace-switch-5" type="checkbox" <?php if(isset($allowread) && $allowread == "1") echo "checked";?> />
					<span class="lbl"></span>
                </div>
            </div>
-->
			<div class = "form-actions">
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