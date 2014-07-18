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
		<?php echo form_open($rootUri.'user_add/'.$ID, array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
			<div class="control-group">
				<label class="control-label" for="userid">用户名 *</label>
				<div class="controls">
					<input id="userid" name="userid" type="text" placeholder="请输入用户名" value="<?php if(isset($userid)) echo $userid;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="username">真实姓名 *</label>
				<div class="controls">
					<input id="username" name="username" type="text" placeholder="请输入真实姓名" value="<?php if(isset($username)) echo $username;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="phonenum">手机号码 *</label>
				<div class="controls">
					<input id="phonenum" name="phonenum" type="text" placeholder="请输入手机号码" value="<?php if(isset($phonenum)) echo $phonenum;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="mailaddr">邮箱地址 *</label>
				<div class="controls">
					<input id="mailaddr" name="mailaddr" type="text" placeholder="请输入邮箱地址" value="<?php if(isset($mailaddr)) echo $mailaddr;?>" />
				</div>
			</div>
            <div class="control-group">
                <label class="control-label" for="job">工作岗位 *</label>
                <div class="controls">
                    <input id="job" name="job" type="text" placeholder="请输入工作岗位" value="<?php if(isset($job)) echo $job;?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password">密码 *</label>
                <div class="controls">
                    <input id="password" name="password" type="password" placeholder="请输入密码" value="<?php if(isset($password)) echo $password;?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="confirmpassword">重复密码 *</label>
                <div class="controls">
                    <input id="confirmpassword" name="confirmpassword" type="password" placeholder="请输入重复密码" value="<?php if(isset($confirmpassword)) echo $confirmpassword;?>" />
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