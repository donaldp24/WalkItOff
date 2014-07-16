<div class="page-header position-relative">
	<h1>
		<?php echo $mainmenu; ?>
		<small>
			<i class="icon-double-angle-right"></i>
            产品统计
		</small>
	</h1>
</div><!--/.page-header-->

<div class="row-fluid">
    <?php echo form_open($rootUri.'goodsstatistic/export', array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
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

		<div style="width:100%; margin:5px;">
		<font style="height:30px; line-height:30px">&nbsp;时间：</font>
		<font id="ordertime" style=""></font>
		<font style="height:30px; line-height:30px">&nbsp;产品名称：</font>
		<font id="goodsname" style=""></font>
		<font style="height:30px; line-height:30px">&nbsp;会员：</font>
		<font id="membername" style=""></font>
			<button type="submit" class="btn btn-small">
				导出
			</button>
		</div>
        <!-- table content begins-->
		<table id="sample-table-2" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>产品名称</th>
					<th>尺码</th>
                    <th>颜色</th>
                    <th>销售量</th>
                    <th>购买会员</th>
                    <th>销售时间</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
        <!-- table content ends-->

		<!-- send deliver dialog -->
		<div id="modal-send-deliver" class="hide">
			<div id="sendno-loading">
			</div>
			<div id="piechart-placeholder"></div>
		</div>
		<!-- send deliver dialog -->

		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
    <?php echo form_close();?>

</div><!--/.row-fluid-->