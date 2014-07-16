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
		<?php echo form_open($rootUri.'goods_add/'.$ID, array('class' => 'form-horizontal', 'id' => 'validation-form')); ?>
			<div class="control-group">
				<label class="control-label" for="name">产品名称 *</label>
				<div class="controls">
					<input id="name" name="name" type="text" placeholder="请输入产品名称" value="<?php if(isset($name)) echo $name;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="goodsno">产品编号 *</label>
				<div class="controls">
					<input id="goodsno" name="goodsno" type="text" placeholder="请输入产编号" value="<?php if(isset($no)) echo $no;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="order_price">预定价格 *</label>
				<div class="controls">
					<input id="order_price" name="order_price" type="text" placeholder="请输入预定价格" value="<?php if(isset($order_price)) echo $order_price;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="price">零售价格 *</label>
				<div class="controls">
					<input id="price" name="price" type="text" placeholder="请输入零售价格" value="<?php if(isset($price)) echo $price;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="firstlevel">一级分类 *</label>
				<div class="controls">
					<select id="firstlevel" name="firstlevel">
					<?php
						foreach( $first_level as $records ) {
							if(isset($info) && $records['uid'] == $info['level1id'])
								echo "<option value='".$records['uid']."' selected>".$records['name']."</option>";
							else
								echo "<option value='".$records['uid']."'>".$records['name']."</option>";
						}
					?></select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="secondlevel">二级分类 *</label>
				<div class="controls">
					<select id="secondlevel" name="secondlevel">
					<?php
						$second_level = explode("@@@", $second_level_data);
						foreach( $second_level as $records ) {
							$values = explode("|||", $records);
							if(isset($info) &&  $values[0] == $info['level2id'] )
								echo "<option value='".$values[0]."' selected>".$values[1]."</option>";
							else
								echo "<option value='".$values[0]."'>".$values[1]."</option>";
						}
					?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="price">款式 *</label>
				<div class="controls">
					<label>
						<input class="ace" name="pattern" type="radio" value="0"<?php if(isset($pattern) && $pattern == 0) echo "checked";?> >
						<span class="lbl"> 新款</span>
					</label>

					<label>
						<input class="ace" name="pattern" type="radio" value="1"<?php if( isset($pattern) && $pattern == 1 ) echo "checked";?>>
						<span class="lbl"> 经典款</span>
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="showorder">显示排序 *</label>
				<div class="controls">
					<input type="text" class="input-medium spinner-input" id="showorder" name="showorder" maxlength="9" value="<?php if(isset($showorder)) echo $showorder;?>">
                    <span class="help-inline">数字越大排在越前面, 现在最大是 "<?php echo $max_showorder; ?>"</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="upload_btn1">推荐图片 *</label>
				<div class="controls">
					&nbsp;<input type=button class="btn btn-small btn-primary" id='upload_btn1' value="选择图片">&nbsp;&nbsp;&nbsp;只能上传一张图片<b>&nbsp;,&nbsp;&nbsp;</b>建议大小为<b>&nbsp;:&nbsp;</b>300<b>&nbsp;*&nbsp;</b>300<img src="<?php echo $rootUri; ?>www/images/ajax_loader.gif" style="display:none;" id="loading_photo1">
					<input id="img_name1" name="img_name1" type="hidden" value="<?php if( isset($img1) ) echo $img1; ?>" />
					<div id="img1" style=" padding:5px;">
						<?php
							if( isset($img1) && $img1 != "" ) {
								echo "<img src='".$rootUri."www/images/uploads/products/image/".$img1."' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
								echo "<img src='".$rootUri."www/images/image_close.png' class='close_btn' onclick='removeMe1(\"".$img1."\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
							}
						?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="upload_btn2">展示图片 *</label>
				<div class="controls">
					&nbsp;<input type="button" class="btn btn-small btn-primary" id='upload_btn2' value="选择图片">&nbsp;&nbsp;&nbsp;可上传多张图片<b>&nbsp;,&nbsp;&nbsp;</b>建议图片大小为<b>&nbsp;:&nbsp;</b>640<b>&nbsp;*&nbsp;</b>640<img src="<?php echo $rootUri; ?>www/images/ajax_loader.gif" style="display:none;" id="loading_photo2">
					<input id="img_name2" name="img_name2" type="hidden" value="<?php if( isset($img2) ) echo $img2; ?>" />
					<div id="img2"><?php
					if( isset($img2) && $img2 != "" ) {
						$arr_img = explode(",",$img2);
						for($i = 0; $i<count($arr_img); $i++)
						{
							echo "<div style='float:left; padding:5px;'>";
							echo "<img src='".$rootUri."www/images/uploads/products/image/".$arr_img[$i]."' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
							echo "<img src='".$rootUri."www/images/image_close.png' class='close_btn' onclick='removeMe2(this, \"".$arr_img[$i]."\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
							echo "</div>";
						}
					}
					?></div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="upload_btn3">产品详情 *</label>
				<div class="controls">
					&nbsp;<input type=button class="btn btn-small btn-primary" id='upload_btn3' value="选择图片">&nbsp;&nbsp;&nbsp;可上传多张图片<b>&nbsp;,&nbsp;&nbsp;</b>建议图片大小为<b>&nbsp;:&nbsp;</b>640<b>&nbsp;*&nbsp;</b>914<img src="<?php echo $rootUri; ?>www/images/ajax_loader.gif" style="display:none;" id="loading_photo3">
					<input id="img_name3" name="img_name3" type="hidden" value="<?php if( isset($img3) ) echo $img3; ?>" />
					<div id="img3"><?php
						if( isset($img3) && $img3 != "" ) {
							$arr_img = explode(",",$img3);
							for($i = 0; $i<count($arr_img); $i++)
							{
								echo "<div style='float:left; padding:5px;'>";
								echo "<img src='".base_url()."www/images/uploads/products/image/".$arr_img[$i]."' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
								echo "<img src='".base_url()."www/images/image_close.png' class='close_btn' onclick='removeMe3(this, \"".$arr_img[$i]."\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
								echo "</div>";
							}
						}
						?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="">更多参数 *</label>
				<div class="controls">
					<span >&nbsp;尺码&nbsp;<input id="txt_size" class="input-small" name="txt_size" type="text" value="" /></span>&nbsp;&nbsp;
					<span >颜色&nbsp;<input id="txt_color" class="input-small" name="txt_color" type="text" value=""  /></span> &nbsp;&nbsp;
					<span >库存&nbsp;<input id="txt_count" class="input-small" name="txt_count" type="text" value="" /></span>&nbsp;&nbsp;
					<span ><button type="button" class="btn btn-small btn-primary" id="insert_pro">增加</button></span>
					<div style="width:60%; margin-top:10px;">	
						<table id="kind_tbl" border="1" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-hover">
							<colgroup><col width="150" /><col width="130" /><col width="80" /><col width="80" /></colgroup>
							<thead><tr><th>尺码</th><th>颜色</th><th>库存</th><th>选择</th></tr></thead>
							<tbody id="property_tbl">
							<?php
								if( isset($kind) && count($kind) > 0 ) {
									$kind_str = "";
									foreach( $kind as $record ) {
										$kind_str .= $record['size']."|||".$record['color']."|||".$record['remain']."@@@";
										echo "<tr><td>".$record['size']."</td><td>".$record['color']."</td><td>".$record['remain']."</td><td><a href='javascript:void(0);' onclick='remove_td(this)'>删除</a></tr>";
									}
									$kind_str = substr($kind_str, 0, strlen($kind_str)-3);
								}
							?>
							</tbody>
						</table>
						<input id="kind" name="kind" type="hidden" value="<?php if(isset($kind) && count($kind)>0) echo $kind_str;?>" />
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="">关联商品 *</label>
				<div class="controls">
					<h4 class="pink" style="margin:5px;">
						<i class="icon-hand-right icon-animated-hand-pointer blue"></i>
						<a href="#modal-relgoods" role="button" class="green" data-toggle="modal"> 请选择套装关联商品 </a>
					</h4>

					<input id="sel_r_id" name="sel_r_id" type="hidden" value="<?php if(isset($relative_goods)) echo $relative_goods;?>" />
					<div id="gallery">
					<?php
						if( isset($relative_goods_name) && $relative_goods_name != "" ) {
							$sel_r_name_ary = explode('@@@', $relative_goods_name);
							foreach( $sel_r_name_ary as $sel_r_name ) {
								$relative_data = explode("|||",$sel_r_name);
								echo "<div style='float:left; padding:5px;'>";
								echo "<img src='".base_url()."www/images/uploads/products/image/".$relative_data[1]."' width='100' height='70'>";
								echo '<h5 class="ui-widget-header"><span title="产品详情" class="ui-icon ui-icon-zoomin" name="'.$relative_data[2].'">&nbsp;</span><span>'.$relative_data[0].'</span></h5>';
								echo '</div>';
							}
						}
					?>
					</div>
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
				&nbsp; &nbsp; &nbsp;
				<button class="btn btn-info" id="cancel_btn" >
					<i class="icon-arrow-left bigger-110"></i>
					取消
				</button>
			</div> 

			<!-- relative dialog -->
			<div id="modal-relgoods" class="modal hide fade" tabindex="-1" style="width:650px;">
				<div class="modal-header no-padding">
					<div class="table-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						套装关联
					</div>
				</div>

				<div class="modal-body padding-12">
					<div class="row-fluid">
						<input type="hidden" id="sel_r_goods" name="sel_r_goods" value="<?php if(isset($sel_r_ids)) echo $sel_r_ids?>">
						<div class="r_search_cond" style="background:#EEE; padding:5px;">&nbsp;
							<select id="r_firstlevel" name="r_firstlevel" class="input-medium">
								<?php
									echo "<option value='0'>一级分类(全部)</option>";
									foreach( $first_level as $records ) {
										echo "<option value='".$records['uid']."'>".$records['name']."</option>";
									}
								?>
							</select>
							<select id="r_secondlevel" name="r_secondlevel" class="input-medium">
								<?php
									/*
									$second_level = explode("@@@", $second_level_data);
									echo "<option value=''></option>";
									foreach( $second_level as $records ) {
										$values = explode("|||", $records);
										echo "<option value='".$values[0]."'>".$values[1]."</option>";
									}
									*/
								?>
							</select>&nbsp;&nbsp;&nbsp;
							<span class="input-icon">
								<i class="icon-search"></i>
								<input type="text" class="input-medium search-query" placeholder="请输入搜索商品名称" id="r_search_name" name="r_search_name" >
							</span>
							<button onclick="return false;" class="btn btn-purple btn-small" id="r_search_btn">
								搜索
								<i class="icon-search icon-on-right bigger-110"></i>
							</button>
						</div>
						<div class="row-fluid" style="margin-top:14px;">
							<div class="span5">
								<div class="widget-box">
									<div class="widget-header widget-header-flat">
										<h4 class="smaller">
											可选商品
										</h4>
									</div>

									<div class="widget-body">
										<div class="widget-main">
											<select id="l_name" name="l_name" multiple="multiple" style="height:200px;">
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="span1" style="margin:25px 30px;">
								<br/><br/>
								<button style="margin:4px;" type="button" class="btn btn-small btn-primary" id="send_right_all">>></button>
								<button style="margin:4px;" type="button" class="btn btn-small btn-primary" id="send_right">&nbsp;>&nbsp;</button>
								<button style="margin:4px;" type="button" class="btn btn-small btn-primary btn-inverse" id="send_left">&nbsp;<&nbsp;</button>
								<button style="margin:4px;" type="button" class="btn btn-small btn-primary btn-inverse" id="send_left_all"><<</button>
							</div>
							<div class="span5">
								<div class="widget-box">
									<div class="widget-header widget-header-flat">
										<h4 class="smaller">
											流管的商品
										</h4>
									</div>

									<div class="widget-body">
										<div class="widget-main">
											<select id="r_name" name="r_name" multiple="multiple" style="height:200px;">
											<?php
												if(isset($relative_goods))
												{
													$arr_relative = explode(",",$relative_goods);
													$arr_relative_name = explode("@@@",$relative_goods_name);
													for($i = 0; $i < count($arr_relative); $i++)
													{
														$relative_value = explode("|||",$arr_relative_name[$i]);
														echo "<option value='".$arr_relative[$i]."' name='".$relative_value[1]."'>".$relative_value[0]."</option>";
													}
												}
											?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button class="btn btn-small btn-success pull-left" data-dismiss="modal" onclick="return set_r_goods();">
						<i class="icon-ok"></i>
						确定
					</button>
					<button class="btn btn-small btn-danger pull-left" data-dismiss="modal" onclick="return onCloseDlg();">
						<i class="icon-remove"></i>
						关闭
					</button>
				</div>
			</div>
			<!-- relative dialog -->
		<?php echo form_close();?>
		<div>
		<!--PAGE CONTENT ENDS-->
	</div><!--/.span-->
</div><!--/.row-fluid-->