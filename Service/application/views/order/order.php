<div class="page-header position-relative">
	<h1>
		<?php echo $mainmenu; ?>
		<small>
			<i class="icon-double-angle-right"></i>
			订单列表
		</small>
	</h1>
</div><!--/.page-header-->

<div class="row-fluid">
	<div class="span12">
		<!--PAGE CONTENT BEGINS-->
		<table id="sample-table-2" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>订单号</th>
					<th>下单时间</th>
					<th>收货人</th>
					<th>总金额</th>
					<th>订单状态</th>
					<th>会员等级</th>
					<th>历史信息</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>

		<!-- smssend deliver dialog -->
		<div id="modal-send-deliver" class="hide">
			<div id="cancel-loading">
			<p id="errorcancel" class="red" style="display:none">请输入短信内容</p>
			<label>To: <span id="receiver"></span><label>
				<textarea id="smscontent" name="smscontent" rows="8" cols="160" style="width:450px;" ></textarea>
			</div>
		</div>
		<!-- smssend deliver dialog -->

		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
</div><!--/.row-fluid-->