<div class="page-header position-relative">
	<h1>
		<?php echo $mainmenu; ?>
		<small>
			<i class="icon-double-angle-right"></i>
            邀请码
		</small>
	</h1>
</div><!--/.page-header-->

<div class="row-fluid">
    <?php echo form_open($rootUri.'invitecode', array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
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
        <div class="search" style="padding:5px;">
            <label for="codecount"></label><input type="text" name="codecount" id="codecount" value="" placeholder="请输入条数">生成任意英文字母与数组组合的7位邀请码 &nbsp;&nbsp;&nbsp;
            <a href='#modal-delete-confirm' role='button' style='text-decoration:none;' data-toggle='modal'>
				<button class="btn btn-small" id="btn-change-receiver">
					<i class="icon-edit"></i>
					生成邀请码
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
					<th>时间</th>
					<th>邀请码条数</th>
                    <th>详情</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
        <!-- table content ends-->

		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
    <?php echo form_close();?>

    <!-- delete confirm dialog -->
    <div id="modal-delete-confirm" class="modal hide fade" tabindex="-1" style="width:380px;">
        <div class="modal-header no-padding">
            <div class="table-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                确认生成
            </div>
        </div>

        <div class="modal-body padding-12">
            <div class="row-fluid">
                <input type="hidden" id="sel_user" name="sel_user" value="">

                <div class="row-fluid" style="margin-top:14px;">
                    <div>
                        您确定要生成邀请码吗？
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-small btn-success pull-left" data-dismiss="modal" onclick="return generate_code();">
                <i class="icon-ok"></i>
                确认
            </button>
            <button class="btn btn-small btn-danger pull-left" data-dismiss="modal">
                <i class="icon-remove"></i>
                取消
            </button>
        </div>
    </div>
    <!-- delete confirm dialog -->

</div><!--/.row-fluid-->