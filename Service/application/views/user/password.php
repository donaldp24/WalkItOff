<div class="page-header position-relative">
	<h1>
		<?php echo $mainmenu; ?>
		<small>
			<i class="icon-double-angle-right"></i>
			修改密码
		</small>
	</h1>
</div><!--/.page-header-->

<div class="row-fluid">
	<div class="span12">
		<!--PAGE CONTENT BEGINS-->
        <?php if (isset($success)) { ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="icon-remove"></i>
                </button>
                <strong>
                    <i class="icon-ok"></i>
                    <!-- 操作失败！-->
                </strong>
                <?php echo $success; ?>
                <br>
            </div>
        <?php } ?>

		<?php echo form_open($rootUri.'account/changepass', array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
			<div class="control-group">
				<label class="control-label" for="name">旧密码 *</label>
				<div class="controls">
					<input type="password" placeholder="请输入旧密码" id="oldpassword" name="oldpassword" type="text">
					<span class="help-inline"><?php echo form_error('oldpassword'); ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="name">新密码 *</label>
				<div class="controls">
					<input type="password" placeholder="请输入新密码" id="password" name="password" type="text">

				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="name">确认密码 *</label>
				<div class="controls">
					<input type="password" placeholder="请输入确认密码" id="confirmpass" name="confirmpass" type="text">
				</div>
			</div>
			<div class = "form-actions">
				<input id="check_save" name="check_save" type="hidden" value="" />
				<button class="btn btn-success" id="save_btn" name="save_btn">
					<i class="icon-ok bigger-110"></i>
					提交
				</button>
				&nbsp; &nbsp; &nbsp;
				<button class="btn" type="reset">
					<i class="icon-undo bigger-110"></i>
					重置
				</button>
			</div> 
	    <?php echo form_close();?>

		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->

    <!-- reset password confirm dialog -->
    <div id="modal-confirm" class="modal hide fade" tabindex="-1" style="width:380px;">
        <div class="modal-header no-padding">
            <div class="table-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                重置密码
            </div>
        </div>

        <div class="modal-body padding-12">
            <div class="row-fluid">
                <input type="hidden" id="sel_user" name="sel_user" value="">

                <div class="row-fluid" style="margin-top:14px;">
                    <div>
                        您选中的用户密码将被重置，请确认是否马上重置密码！
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-small btn-success pull-left" data-dismiss="modal" onclick="return resetPassword();">
                <i class="icon-ok"></i>
                确认
            </button>
            <button class="btn btn-small btn-danger pull-left" data-dismiss="modal" onclick="return onCloseDlg();">
                <i class="icon-remove"></i>
                取消
            </button>
        </div>
    </div>
    <!-- confirm dialog -->

</div><!--/.row-fluid-->