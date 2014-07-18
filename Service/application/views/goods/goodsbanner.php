<div class="page-header position-relative">
	<h1>
		<?php echo $mainmenu; ?>
		<small>
			<i class="icon-double-angle-right"></i>
			首页产品列表
		</small>
	</h1>
</div><!--/.page-header-->

<div class="row-fluid">
    <?php echo form_open($rootUri.'goodsbanner/deleteitems', array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
	<div class="span12">
		<!--PAGE CONTENT BEGINS-->
  		<?php if (isset($notice)) { ?>
		<div class="alert alert-<?php echo $succ ?>">
			<button type="button" class="close" data-dismiss="alert">
				<i class="icon-remove"></i>
			</button>
			<strong>
				<i class="icon-ok"></i>
				<?php if (isset($succ) && $succ == "success") { ?>
				操作成功！
				<?php } else if (isset($succ) && $succ == "fail") { ?>
				操作失败！
				<?php } ?>
			</strong>
			<?php echo $notice; ?>
			<br>
		</div>
		<?php } ?>
		<div class="search" style="padding:5px;">
            <input type="hidden" name="del_ids" id="del_ids" value="">
            <a href='#modal-delete-confirm' role='button' style='text-decoration:none;' data-toggle='modal'>
				<button id="btn_delete" class = "btn btn-app btn-danger btn-mini">
					<i class="icon-trash bigger-200"></i>
					删除
				</button>
			</a>
			<button id="btn_add" class = "btn btn-app btn-primary btn-mini">
				<i class="icon-plus bigger-200"></i>
				 新增 
			</button>
        </div>
		<table id="sample-table-2" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="center">
						<label>
							<input type="checkbox" class="ace" />
							<span class="lbl"></span>
						</label>
					</th>
					<th>产品名称</th>
					<th>首页图片</th>
					<th>显示排序</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
    <?php echo form_close();?>

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
                <input type="hidden" id="sel_user" name="sel_user" value="">

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