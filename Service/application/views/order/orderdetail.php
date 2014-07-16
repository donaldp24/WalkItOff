<div class="row-fluid">
	<div class="span12">
		<!--PAGE CONTENT BEGINS-->
		<div class="space-6"></div>

		<div class="row-fluid">
			<div class="span10 offset1">
				<div class="widget-box transparent invoice-box">
					<div class="widget-header widget-header-large">
						<h3 class="grey lighter pull-left position-relative">
							<i class="icon-leaf green"></i>
							订单详情
						</h3>

						<div class="widget-toolbar no-border invoice-info">
							<span class="invoice-info-label">订单号：</span>
							<span class="red"><?php echo $info['orderno']; ?></span>

							<br />
							<span class="invoice-info-label">下单日期：</span>
							<span class="blue"><?php echo date('Y年m月d日', $info['ordertime']); ?></span>
						</div>

						<div class="widget-toolbar hidden-480">
							<a href="javascript:window.print();">
								<i class="icon-print"></i>
							</a>
						</div>
					</div>

					<div class="widget-body">
						<div class="widget-main padding-24">
							<div class="row-fluid">
								<div class="row-fluid">
									<div class="span6">
										<div class="row-fluid">
											<div class="span12 label label-large label-info arrowed-in arrowed-right">
												<b>基本信息</b>
											</div>
										</div>

										<div class="row-fluid">
											<ul class="unstyled spaced">
												<li>
													<i class="icon-caret-right blue"></i>
													订单号：<?php echo $info['orderno']; ?>
													<input type="hidden" id="orderuid" name="orderuid" value="<?php echo $info['primaryuid']; ?>" />
												</li>

												<li>
													<i class="icon-caret-right blue"></i>
													下单时间：<?php echo date('Y年m月d日 H点i分', $info['ordertime']); ?>
												</li>
												<li>
													<i class="icon-caret-right blue"></i>
													订单状态：<span id="order_status"><?php echo $status; ?></span>
												</li>
												<li>
													<i class="icon-caret-right blue"></i>
													配送方式：<span id="send_type"></span>
												</li>
												<li>
													<i class="icon-caret-right blue"></i>
													发货单号：<font id="sendingno"><?php echo $info['sendingno']; ?></font>
												</li>
												<li>
													<i class="icon-caret-right blue"></i>
													送货时间：<?php echo $info['sendtime']; ?>
												</li>
												<li class="divider"></li>
												<li class="hidden-print">
													<?php 
													$btn_comp_style = "display:none;";
													if (trim($info['sendingno']) == "" && $info['status'] == ORDER_STATUS_WAIT_DELIVER) { ?>
													<button class="btn" id="btn-send-deliver">
														<i class="icon-pencil bigger-125"></i>
														发货
													</button>
													<?php } else if ($info['status'] == ORDER_STATUS_ALREADY_DELIVER) { $btn_comp_style = "display:block"; } ?>
													<button type="button" class="btn btn-success" id="btn-complete-deliver" style="<?php echo $btn_comp_style ?>" data-loading-text="Loading...">
														<i class="icon-pencil bigger-125"></i>
														订单已完成
													</button>
												</li>
											</ul>
										</div>
									</div><!--/span-->

									<!-- send deliver dialog -->
									<div id="modal-send-deliver" class="hide">
										<div id="sendno-loading">
										<p id="errorsendno" class="red" style="display:none">请输入发货单号</p>
										<label>发货单号：<label>
											<input type='text' name="sendno" id="sendno" placeholder="请输入发货单号" />
										</div>
									</div>
									<!-- send deliver dialog -->

									<div class="span6">
										<div class="row-fluid">
											<div class="span12 label label-large label-success arrowed-in arrowed-right">
												<b>收货人信息</b>
											</div>
										</div>

										<div class="row-fluid">
											<ul class="unstyled spaced">
												<li>
													<i class="icon-caret-right green"></i>
													收货人：<span id="receiver" name="receiver"><?php echo $memberpos['receivername']; ?></span>
													<input type="hidden" id="memposid" name="memposid" value="<?php echo $memberpos["uid"]; ?>" />
												</li>
												<li>
													<i class="icon-caret-right green"></i>
													手机:
													<b class="red"><span id="phonenum" name="phonenum"><?php echo $memberpos['phonenum']; ?></span></b>
												</li>
												<li>
													<i class="icon-caret-right green"></i>
													地址：<span id="show_addrprovince"><?php echo $memberpos['addrprovince'] ?></span>
													<span id="show_addrcity"><?php echo $memberpos['addrcity'] ?></span>
													<span id="show_addrarea"><?php echo $memberpos['addrarea'] ?></span>
													<span id="show_addrstreet"><?php echo $memberpos['addrstreet']; ?></span>
												</li>
												<li>
													<i class="icon-caret-right green"></i>
													邮编：<span id="postaddr"><?php echo $memberpos['postaddr']; ?></span>
												</li>
												<li>
													<i class="icon-caret-right green"></i>
													会员等级：<?php echo $memberlevel; ?>
												</li>
												<li class="hidden-print">
													<button class="btn btn-small" id="btn-change-receiver">
														<i class="icon-edit"></i>
														编辑收货人
													</button>
												</li>
											</ul>
										</div>
									</div><!--/span-->
								</div><!--row-->

								<!-- change receiver dialog -->
								<div id="modal-change-receiver" class="hide">
									<p id="errorreceiver" class="red" style="display:none"></p>
									<div>
										<label>收货人员：<label>
										<input type="text" id="edit_receiver" name="edit_receiver" />
									</div>
									<div>
										<label>手机号码：<label>
										<input type="text" id="edit_phonenum" name="edit_phonenum" />
									</div>
									<div>
										<label>地址：<label>
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
										<br>
									</div>
										<div>
										<input type="text" id="addrstreet" name="addrstreet" value="<?php echo $info["addrstreet"]; ?>" />
										</div>
									<div>
										<label>邮编地址：<label>
										<input type="text" id="edit_postaddr" name="edit_postaddr" />
									</div>
								</div>
								<!-- change receiver dialog -->

								<div class="space"></div>

								<div class="row-fluid">
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th class="center">编号</th>
												<th>商品名称</th>
												<th>货号</th>
												<th>属性</th>
												<th>数量</th>
												<th>价格</th>
												<th>库存</th>
												<th>合计</th>
											</tr>
										</thead>

										<tbody>
											<?php 
											if (isset($ordergoods['ordergoods']) && count($ordergoods['ordergoods'])) {
											foreach($ordergoods['ordergoods'] as $it => $goods) { ?>
											<tr>
												<td class="center"><?php echo $it+1; ?></td>

												<td>
													<a target="_blank" href="<?php echo $rootUri.'goods_add/'.$goods['goodsid'] ?>"><?php echo $goods['goodsname']; ?></a>
												</td>
												<td>
													<?php echo $info['orderno']; ?>
												</td>
												<td><?php echo $goods['size'].", ".$goods['color']; ?></td>
												<td><?php echo $goods['quantity'] ?></td>
												<td><?php echo "￥".$goods['reserveprice'] ?></td>
												<td><?php echo $goods['remain'] ?></td>
												<td><?php echo "￥".$goods['reserveprice'] * $goods['quantity'] ?></td>
											</tr>
											<?php } } ?>
										</tbody>
									</table>
								</div>

								<div class="hr hr8 hr-double hr-dotted"></div>

								<div class="row-fluid">
									<div class="span5 pull-right">
										<h4 class="pull-right">
											总数 :
											<span class="red"><?php echo $ordergoods['totalcount']; ?></span>
											&nbsp;&nbsp;&nbsp;&nbsp;
											总价 :
											<span class="red">￥<?php echo $ordergoods['totalprice']; ?></span>
										</h4>
									</div>
									<div class="span7 pull-left hidden-print"> 
									<?php if ($info['status'] != ORDER_STATUS_ALREADY_CANCEL && $info['status'] != ORDER_STATUS_ALREADY_RECEIVE) { ?>
									<button type="button" class="btn" id="btn-cancel-deliver"  data-loading-text="Loading...">
										<i class="icon-pencil bigger-125"></i>
										订单取消
									</button>
									<?php } ?>
									</div>
								</div>

								<!-- cancel deliver dialog -->
								<div id="modal-cancel-deliver" class="hide">
									<div id="cancel-loading">
									<p id="errorcancel" class="red" style="display:none">请输入取消原因</p>
									<label>取消原因：<label>
										<textarea id="cancelreason" name="cancelreason" rows="5" cols="40" ></textarea>
									</div>
								</div>
								<!-- cancel deliver dialog -->

								<div class="space-6"></div>

								<?php $reasonstyle = "display:none";
									if ($info['cancelreason'] != null && trim($info['cancelreason']) != "") {
									$reasonstyle = "display:block";
								} ?>
								<div id="reason-div" class="row-fluid" style="<?php echo $reasonstyle; ?>">
									<div class="span12 well">
										<h4>清单取消原因</h4>
										<span id="reason"><?php echo $info['cancelreason']; ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
</div><!--/.row-fluid-->