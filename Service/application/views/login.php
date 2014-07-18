<!DOCTYPE html>
<html lang="zh-cn" xmlns="http://www.w3.org/1999/xhtml">
	
<head>
		<meta charset="utf-8" />
		<title>布迪内衣系统管理平台</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!--basic styles-->

		<link href="<?php echo $rootUri; ?>/www/css/bootstrap.min.css" rel="stylesheet" />
		<link href="<?php echo $rootUri; ?>/www/css/bootstrap-responsive.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="<?php echo $rootUri; ?>/www/css/font-awesome.min.css" />

		<!--[if IE 7]>
		  <link rel="stylesheet" href="<?php echo $rootUri; ?>/www/css/font-awesome-ie7.min.css" />
		<![endif]-->

		<!--page specific plugin styles-->

		<!--fonts-->

		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" />

		<!--ace styles-->

		<link rel="stylesheet" href="<?php echo $rootUri; ?>/www/css/ace.min.css" />
		<link rel="stylesheet" href="<?php echo $rootUri; ?>/www/css/ace-responsive.min.css" />
		<link rel="stylesheet" href="<?php echo $rootUri; ?>/www/css/ace-skins.min.css" />

		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="<?php echo $rootUri; ?>/www/css/ace-ie.min.css" />
		<![endif]-->

		<!--inline styles related to this page-->
		<!--ace settings handler-->
		<script src="<?php echo $rootUri; ?>/www/js/ace-extra.min.js"></script>
		<script type="text/javascript">
			var rootUri = "<?php echo $rootUri; ?>";
		</script>
	</head>

	<body class="login-layout">
		<div class="main-container container-fluid">
			<div class="main-content">
				<div class="row-fluid">
					<div class="span12">
						<div class="login-container">
							<div class="row-fluid">
								<div class="center">
									<h1>
										<i class="icon-leaf green"></i>
										<span class="red">布迪内衣系统</span>
										<span class="white">管理平台</span>
									</h1>
									<h4 class="blue"></h4>
								</div>
							</div>

							<div class="space-6"></div>

							<div class="row-fluid">
								<div class="position-relative">
									<div id="login-box" class="login-box visible widget-box no-border">
										<div class="widget-body">
											<div class="widget-main">
												<h4 class="header blue lighter bigger">
													<i class="icon-coffee green"></i>
													请输入您的账户信息
												</h4>

												<div class="space-6"></div>

												<form action="<?php echo $base_url; ?>" method="post" name="login" id="loginform" >
													<fieldset>
														<p style="color:#FF0000;font-size:12px;font-weight:normal;"><?php if (isset($errormsg)) echo $errormsg; ?></p>
														<label>
															<span class="block input-icon input-icon-right">
																<input type="text" class="span12" placeholder="请输入用户名" name="username" id="username"
																	value="<?php echo set_value('username'); ?>" />
																<i class="icon-user"></i>
															</span>
														</label>
														<?php echo form_error('username'); ?>

														<label>
															<span class="block input-icon input-icon-right">
																<input type="password" class="span12" placeholder="请输入登录密码" name="password" id="password"
																value="" />
																<i class="icon-lock"></i>
															</span>
														</label>
														<?php echo form_error('password'); ?>

														<label>
															<div style="height:32px;">
															<span class="block input-icon input-icon-right">
																<input type="text" style="float:left;margin:0px; padding:0px; height:28px; line-height:30px; width:150px;" name="seccode" id="seccode" placeholder="请输入验证码" />
																<span style="float:right;" id="verific_code"><?php echo $captchaImg; ?></span>
															<span>
															</div>
														</label>
														<?php echo form_error('seccode'); ?>

														<div class="space"></div>

														<div class="clearfix">

															<button onclick="" class="width-35 pull-right btn btn-small btn-primary">
																<i class="icon-key"></i>
																登录
															</button>
														</div>

														<div class="space-4"></div>
													</fieldset>
												</form>
											</div><!--/widget-main-->

										</div><!--/widget-body-->
									</div><!--/login-box-->

									<div id="forgot-box" class="forgot-box widget-box no-border">
										<div class="widget-body">
											<div class="widget-main">
												<h4 class="header red lighter bigger">
													<i class="icon-key"></i>
													找回密码
												</h4>

												<div class="space-6"></div>
												<p>
													请填写您的邮箱地址然后接收指令
												</p>

												<form>
													<fieldset>
														<label>
															<span class="block input-icon input-icon-right">
																<input type="email" class="span12" placeholder="邮箱地址" />
																<i class="icon-envelope"></i>
															</span>
														</label>

														<div class="clearfix">
															<button onclick="return false;" class="width-35 pull-right btn btn-small btn-danger">
																<i class="icon-lightbulb"></i>
																发送邮件!
															</button>
														</div>
													</fieldset>
												</form>
											</div><!--/widget-main-->

											<div class="toolbar center">
												<a href="#" onclick="show_box('login-box'); return false;" class="back-to-login-link">
													返回登录
													<i class="icon-arrow-right"></i>
												</a>
											</div>
										</div><!--/widget-body-->
									</div><!--/forgot-box-->

									<div id="signup-box" class="signup-box widget-box no-border">
										<div class="widget-body">
											<div class="widget-main">
												<h4 class="header green lighter bigger">
													<i class="icon-group blue"></i>
													注册用户
												</h4>

												<div class="space-6"></div>
												<p> 请输入您的详细内容: </p>

												<form>
													<fieldset>
														<label>
															<span class="block input-icon input-icon-right">
																<input type="email" class="span12" placeholder="邮箱地址" />
																<i class="icon-envelope"></i>
															</span>
														</label>

														<label>
															<span class="block input-icon input-icon-right">
																<input type="text" class="span12" placeholder="用户名" />
																<i class="icon-user"></i>
															</span>
														</label>

														<label>
															<span class="block input-icon input-icon-right">
																<input type="password" class="span12" placeholder="请输入密码" />
																<i class="icon-lock"></i>
															</span>
														</label>

														<label>
															<span class="block input-icon input-icon-right">
																<input type="password" class="span12" placeholder="请确认密码" />
																<i class="icon-retweet"></i>
															</span>
														</label>

														<div class="space-24"></div>

														<div class="clearfix">
															<button type="reset" class="width-30 pull-left btn btn-small">
																<i class="icon-refresh"></i>
																重置
															</button>

															<button onclick="return false;" class="width-65 pull-right btn btn-small btn-success">
																注册
																<i class="icon-arrow-right icon-on-right"></i>
															</button>
														</div>
													</fieldset>
												</form>
											</div>

											<div class="toolbar center">
												<a href="#" onclick="show_box('login-box'); return false;" class="back-to-login-link">
													<i class="icon-arrow-left"></i>
													返回登录
												</a>
											</div>
										</div><!--/widget-body-->
									</div><!--/signup-box-->
								</div><!--/position-relative-->
							</div>
						</div>
					</div><!--/.span-->
				</div><!--/.row-fluid-->
			</div>
		</div><!--/.main-container-->

		<!--basic scripts-->

		<!--[if !IE]>-->

		<script src="<?php echo $rootUri; ?>/www/js/jquery-2.0.3.min.js"></script>

		<!--<![endif]-->

		<!--[if IE]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<![endif]-->

		<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='<?php echo $rootUri; ?>/www/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>

		<!--<![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='<?php echo $rootUri; ?>/www/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='<?php echo $rootUri; ?>/www/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="<?php echo $rootUri; ?>/www/js/bootstrap.min.js"></script>

		<!--page specific plugin scripts-->

		<!--ace scripts-->

		<script src="<?php echo $rootUri; ?>/www/js/ace-elements.min.js"></script>
		<script src="<?php echo $rootUri; ?>/www/js/ace.min.js"></script>

		<!--inline scripts related to this page-->

		<script type="text/javascript">
			function show_box(id) {
			 jQuery('.widget-box.visible').removeClass('visible');
			 jQuery('#'+id).addClass('visible');
			}
		</script>
		<script src="<?php echo $rootUri; ?>/www/js/captcha.js"></script>
	</body>

</html>
