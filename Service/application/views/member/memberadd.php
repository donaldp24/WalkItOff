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
		<?php echo form_open($rootUri.'member_add/'.$ID, array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>

            <div class="control-group">
                <label class="control-label" for="memberid">会员账号 *</label>
                <div class="controls">
                    <input id="memberid" name="memberid" type="text" placeholder="请输入会员账号" value="<?php if(isset($info['memberid'])) echo $info['memberid'];?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="membername">会员实名 *</label>
                <div class="controls">
                    <input id="membername" name="membername" type="text" placeholder="请输入会员名" value="<?php if(isset($info['membername'])) echo $info['membername'];?>" />
                </div>
            </div>
			<div class="control-group">
				<label class="control-label" for="sex">性别 *</label>
				<div class="controls">
					<label>
						<input class="ace" name="sex" type="radio" value="1"<?php if(isset($info['sex']) && $info['sex'] == 1) echo "checked";?> >
						<span class="lbl"> 男</span>
					</label>

					<label>
						<input class="ace" name="sex" type="radio" value="0"<?php if( isset($info['sex']) && $info['sex'] == 0 ) echo "checked";?>>
						<span class="lbl"> 女</span>
					</label>
				</div>
			</div>		
            <div class="control-group">
                <label class="control-label" for="phonenum">手机号 *</label>
                <div class="controls">
                    <input id="phonenum" name="phonenum" type="text" placeholder="请输入手机号" value="<?php if(isset($info['phonenum'])) echo $info['phonenum'];?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mailaddr">邮箱地址 *</label>
                <div class="controls">
                    <input id="mailaddr" name="mailaddr" type="text" placeholder="请输入邮箱地址" value="<?php if(isset($info['mailaddr'])) echo $info['mailaddr'];?>" />
                </div>
            </div>
			<div class="control-group">
				<label class="control-label" for="addrprovince">地址 *</label>
				<div class="controls">
					<select id="addrprovince" name="addrprovince" class="chosen-select">
					<?php
						foreach( $provinces as $records ) {
							if(isset($info) && $records['regionname'] == $info['addrprovince'])
								echo "<option value='".$records['regionname']."' selected>".$records['regionname']."</option>";
							else
								echo "<option value='".$records['regionname']."'>".$records['regionname']."</option>";
						}
					?></select>&nbsp;

					<select id="addrcity" name="addrcity" class="chosen-select">
					<?php
					if (isset($cities)) {
						foreach( $cities as $records ) {
							if(isset($info) &&  $records['regionname'] == $info['addrcity'] )
								echo "<option value='".$records['regionname']."' selected>".$records['regionname']."</option>";
							else
								echo "<option value='".$records['regionname']."'>".$records['regionname']."</option>";
						}
					}
					?>
					</select>&nbsp;
					<select id="addrarea" name="addrarea" class="chosen-select">
					<?php
					if (isset($areas)) {
						foreach( $areas as $records ) {
							if(isset($info) &&  $records['regionname'] == $info['addrarea'] )
								echo "<option value='".$records['regionname']."' selected>".$records['regionname']."</option>";
							else
								echo "<option value='".$records['regionname']."'>".$records['regionname']."</option>";
						}
					}
					?>
					</select>&nbsp;
				</div>
			</div>

            <div class="control-group">
                <label class="control-label" for="qqnum">QQ号 *</label>
                <div class="controls">
                    <input id="qqnum" name="qqnum" type="text" placeholder="请输入QQ号码" value="<?php if(isset($info['qqnum'])) echo $info['qqnum'];?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="identifycard">身份证号 *</label>
                <div class="controls">
                    <input id="identifycard" name="identifycard" type="text" placeholder="请输入身份证号" value="<?php if(isset($info['identifycard'])) echo $info['identifycard'];?>" />
                </div>
            </div>
			<input type="hidden" id="uid" name="uid" value="<?php if(isset($info['uid'])) echo $info['uid']; ?>">
            <div class="control-group">
                <label class="control-label" for="memberlevel">会员级数*</label>
                <div class="controls">
					<select id="memberlevel" name="memberlevel">
					<?php
					if (isset($levels)) {
						foreach( $levels as $records ) {
							if(isset($info) &&  $records['uid'] == $info['memberlevel'] )
								echo "<option value='".$records['uid']."' selected>".$records['name']."</option>";
							else
								echo "<option value='".$records['uid']."'>".$records['name']."</option>";
						}
					}
					?>
					</select>
                </div>
            </div>
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