<div class="page-header position-relative">
	<h1>
		<?php echo $mainmenu; ?>
		<small>
			<i class="icon-double-angle-right"></i>
            订单统计
		</small>
	</h1>
</div><!--/.page-header-->

<div class="row-fluid">
    <?php echo form_open($rootUri.'', array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
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
					<th>订单号</th>
					<th>下单时间</th>
                    <th>收货人</th>
                    <th>总金额</th>
                    <th>会员等级</th>
                    <th>订单提交区域</th>
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