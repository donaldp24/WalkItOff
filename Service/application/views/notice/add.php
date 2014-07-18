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
		<?php echo form_open($rootUri.'notice_add/'.$ID, array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>

            <div class="control-group">
                <label class="control-label" for="title">推送标题 *</label>
                <div class="controls">
                    <input id="title" name="title" type="text" placeholder="请输入推送标题" value="<?php if(isset($title)) echo $title;?>" />
                </div>
            </div>

			<div class="control-group">
				<label class="control-label" for="contents">内容简介 *</label>
				<div class="controls">
					<input class="input-xxlarge" id="contents" name="contents" type="text" placeholder="请输入内容简介" value="<?php if(isset($contents)) echo $contents;?>" />
                    <span class="help-inline">建议字数为：1 ~ 50</span>
				</div>
			</div>

            <div class="control-group">
                <label class="control-label" for="contents">推送会员 </label>
                <div class="controls">
                    <input name="allmembers" id="allmembers" type="checkbox" class="ace" />
                    <label class="lbl" for="allmembers">所有用户</label>
                    &nbsp;
                    <input name="level1members" id="level1members" type="checkbox" class="ace"/>
                    <label class="lbl" for="level1members">一级会员</label>
                    &nbsp;
                    <input name="level2members" id="level2members" type="checkbox" class="ace"/>
                    <label class="lbl" for="level2members">二级会员</label>
                    &nbsp;
                    <input name="level3members" id="level3members" type="checkbox" class="ace"/>
                    <label class="lbl" for="level3members">三级会员</label>
                    &nbsp;
                    <input name="level4members" id="level4members" type="checkbox" class="ace"/>
                    <label class="lbl" for="level4members">四级会员</label>
                    &nbsp;
                    <input name="level5members" id="level5members" type="checkbox" class="ace"/>
                    <label class="lbl" for="level5members">五级会员</label>

                </div>
            </div>

            <input type="hidden" name="receiver" value="0" id="receiver"/>

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