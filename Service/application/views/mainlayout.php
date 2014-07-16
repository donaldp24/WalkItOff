<!DOCTYPE html>
<html lang="zh-cn">
	
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>布迪内衣管理系统</title>

		<meta name="description" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!--basic styles-->

		<link href="<?php echo $rootUri; ?>www/css/bootstrap.min.css" rel="stylesheet" />
		<link href="<?php echo $rootUri; ?>www/css/bootstrap-responsive.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="<?php echo $rootUri; ?>www/css/font-awesome.min.css" />

		<!--[if IE 7]>
		  <link rel="stylesheet" href="<?php echo $rootUri; ?>www/css/font-awesome-ie7.min.css" />
		<![endif]-->

		<!--page specific plugin styles-->

		<?php if (isset($inlinecss)) echo $inlinecss; ?>

		<!--ace styles-->

		<link rel="stylesheet" href="<?php echo $rootUri; ?>www/css/ace.min.css" />
		<link rel="stylesheet" href="<?php echo $rootUri; ?>www/css/ace-responsive.min.css" />
		<link rel="stylesheet" href="<?php echo $rootUri; ?>www/css/ace-skins.min.css" />

		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="<?php echo $rootUri; ?>www/css/ace-ie.min.css" />
		<![endif]-->

		<!--inline styles related to this page-->

		<!--ace settings handler-->

		<script src="<?php echo $rootUri; ?>www/js/ace-extra.min.js"></script>

		<script type="text/javascript">
			var rootUri = "<?php echo $rootUri; ?>";
		</script>
	</head>

	<body>
		<div class="navbar" id="navbar">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-inner hidden-print">
				<div class="container-fluid">
					<a href="#" class="brand">
						<small>
							<i class="icon-heart-empty"></i>
							布迪内衣管理系统
						</small>
					</a><!--/.brand-->

					<ul class="nav ace-nav pull-right">
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<img class="nav-user-photo" src="<?php echo $rootUri; ?>www/avatars/user.jpg" alt="Jason's Photo" />
								<span class="user-info">
									<small>您好,</small>
									<?php echo $this->session->userdata['realname']; ?>
								</span>

								<i class="icon-caret-down"></i>
							</a>

							<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret	dropdown-closer">
								<li>
									<a href="<?php echo base_url()."account/changepass"; ?>">
										<i class="icon-user"></i>
										修改密码
									</a>
								</li>

								<li>
									<a href="<?php echo base_url()."account/logout"; ?>">
										<i class="icon-off"></i>
										注销
									</a>
								</li>
							</ul>
						</li>
					</ul><!--/.ace-nav-->
				</div><!--/.container-fluid-->
			</div><!--/.navbar-inner-->
		</div>

		<div class="main-container container-fluid">
			<a class="menu-toggler" id="menu-toggler" href="#">
				<span class="menu-text"></span>
			</a>

			<div class="sidebar hidden-print" id="sidebar">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>


				<ul class="nav nav-list">
					<?php echo $leftmenu; ?>
				</ul><!--/.nav-list-->

				<div class="sidebar-collapse" id="sidebar-collapse">
					<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
				</div>

				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
			</div>

			<div class="main-content">
				<div class="breadcrumbs hidden-print" id="breadcrumbs">
					<script type="text/javascript">
						try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
					</script>

					<ul class="breadcrumb">
						<li>
							<i class="icon-home home-icon"></i>
							<a href="<?php echo base_url(); ?>">首页</a>

							<span class="divider">
								<i class="icon-angle-right arrow-icon"></i>
							</span>
						</li>

						<?php if ($submenu == "") { ?>
							<li class="active"><?php echo $mainmenu; ?></li>
						<?php } else { ?>
						<li>
							<a href="<?php if (isset($specificmenu)) echo $specificmenu; else echo 'javascript:void(0);' ?>"><?php echo $mainmenu; ?></a>

							<span class="divider">
								<i class="icon-angle-right arrow-icon"></i>
							</span>
						</li>
						<li class="active"><?php echo $submenu; ?></li>
						<?php } ?>
					</ul><!--.breadcrumb-->
				</div>

				<div class="page-content">
					<?php echo $contents; ?>
				</div><!--/.page-content-->

				<div class="ace-settings-container" id="ace-settings-container">
					<div class="btn btn-app btn-mini btn-warning ace-settings-btn" id="ace-settings-btn">
						<i class="icon-cog bigger-150"></i>
					</div>

					<div class="ace-settings-box" id="ace-settings-box">
						<div>
							<div class="pull-left">
								<select id="skin-colorpicker" class="hide">
									<option data-skin="default" value="#438EB9">#438EB9</option>
									<option data-skin="skin-1" value="#222A2D">#222A2D</option>
									<option data-skin="skin-2" value="#C6487E">#C6487E</option>
									<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
								</select>
							</div>
							<span>&nbsp; 请点击来选择皮肤</span>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" />
							<label class="lbl" for="ace-settings-navbar"> 固定导航栏</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
							<label class="lbl" for="ace-settings-sidebar"> 固定左边菜单</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />
							<label class="lbl" for="ace-settings-breadcrumbs"> 固定面包屑</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />
							<label class="lbl" for="ace-settings-rtl"> 右到左 (rtl)</label>
						</div>
					</div>
				</div><!--/#ace-settings-container-->
			</div><!--/.main-content-->
		</div><!--/.main-container-->

		<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-small btn-inverse">
			<i class="icon-double-angle-up icon-only bigger-110"></i>
		</a>

		<!--basic scripts-->

		<!--[if !IE]>-->

		<script src="<?php echo $rootUri; ?>www/js/jquery-2.0.3.min.js"></script>

		<!--<![endif]-->

		<!--[if IE]>
			<script src="<?php echo $rootUri; ?>www/js/jquery-1.10.2.min.js"></script>
<![endif]-->

		<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='<?php echo $rootUri; ?>www/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>

		<!--<![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='<?php echo $rootUri; ?>www/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='<?php echo $rootUri; ?>www/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="<?php echo $rootUri; ?>www/js/bootstrap.min.js"></script>

		<!--page specific plugin scripts-->
		<?php echo $js_plugins; ?>

		<!--ace scripts-->

		<script src="<?php echo $rootUri; ?>www/js/ace-elements.min.js"></script>
		<script src="<?php echo $rootUri; ?>www/js/ace.min.js"></script>

		<!--inline scripts related to this page-->
		<?php echo $inlinejs; ?>
	</body>

</html>
