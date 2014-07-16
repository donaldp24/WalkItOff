<div class="page-header position-relative">
	<h1>
		<?php echo $mainmenu; ?>
		<small>
			<i class="icon-double-angle-right"></i>
            会员管理
		</small>
	</h1>
</div><!--/.page-header-->

<div class="row-fluid">
    <?php echo form_open($rootUri.'member', array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
	<div class="span12">
		<!--PAGE CONTENT BEGINS-->
        <?php if (isset($error_msg)) { ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="icon-remove"></i>
                </button>
                <strong>
                    <i class="icon-ok"></i>
                    <!-- 操作失败！-->
                </strong>
                <?php echo $error_msg; ?>
                <br>
            </div>
        <?php } ?>

        <!--search_start-->
        <div class="search" style="margin:5px;">
            <input type="hidden" name="del_ids" id="del_ids" value="">
            <a href='#modal-delete-confirm' role='button' class='' data-toggle='modal'>
                <button id = "btn_delete" class = "btn btn-app btn-danger btn-mini">
                    <i class="icon-trash bigger-200"></i>
                    删除
                </button>
            </a>
        </div>
        <!--search_end-->

        <!-- table content begins-->
		<table id="sample-table-2" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="center">
						<label>
							<input type="checkbox" class="ace" />
							<span class="lbl"></span>
						</label>
					</th>
					<th>用户名</th>
					<th>真实姓名</th>
					<th>手机号码</th>
					<th>会员等级</th>
					<th>密码</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
        <!-- table content ends-->

		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
    <?php echo form_close();?>

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
                <input type="hidden" id="sel_member" name="sel_member" value="">

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

    <!-- delete confirm dialog -->
    <div id="modal-delete-confirm" class="modal hide fade" tabindex="-1" style="width:380px;">
        <div class="modal-header no-padding">
            <div class="table-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                确认删除
            </div>
        </div>

        <div class="modal-body padding-12">
            <div class="row-fluid">
                <div class="row-fluid" style="margin-top:14px;">
                    <div>
                        您选中的项目将被删除，请确认是否删除！
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-small btn-success pull-left" data-dismiss="modal" onclick="return del_data();">
                <i class="icon-ok"></i>
                确认
            </button>
            <button class="btn btn-small btn-danger pull-left" data-dismiss="modal" onclick="return onCloseDlg();">
                <i class="icon-remove"></i>
                取消
            </button>
        </div>
    </div>
    <!-- delete confirm dialog -->

</div><!--/.row-fluid-->