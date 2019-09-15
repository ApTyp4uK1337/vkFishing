<?php

	session_start();

	if(isset($_COOKIE['uid']) AND isset($_COOKIE['code']) AND isset($_COOKIE['hash'])) {
		$user = mysql_fetch_assoc(mysql_query("SELECT `id`, `code` FROM `users` WHERE `id` = '".$_COOKIE['uid']."'"));
		$code = md5($user['code']);
		$hash = md5($_SERVER['REMOTE_ADDR'].':'.$user['id']);
		
		if($_COOKIE['uid'] != $user['id'] OR $_COOKIE['code'] != $code OR $_COOKIE['hash'] != $hash) {
			setcookie('uid', NULL, time()-3600*7);
			setcookie('code', NULL, time()-3600*7);
			setcookie('hash', NULL, time()-3600*7);
			header('Location: '.siteURL().'/apanel?act=auth');
		} else {
			$admin = mysql_fetch_assoc(mysql_query("SELECT `login`,`password`,`dark_theme`,`preloader` FROM `users` WHERE `id` = '".$_COOKIE['uid']."'"));
			
			if(isset($_POST['change_password'])) {
				if(isset($_POST['password']) AND isset($_POST['npassword']) AND isset($_POST['rpassword'])) {
					$password = trim($_POST['password']);
					$npassword = trim($_POST['npassword']);
					$rpassword = trim($_POST['rpassword']);
					$code = $_COOKIE['code'];
					
					if($password == $admin['password']) {
						if($npassword == $rpassword) {
							$query = "UPDATE `users`
									  SET `password` = '$npassword', `code` = '$code'
									  WHERE `id` = '".$_COOKIE['uid']."'
									 ";
							$res = mysql_query($query) or die (mysql_error());
							
							setcookie('code', md5($code), time()+3600*7);
							
							header('Location: '.siteURL().'/apanel?act=edit');
						}
					}
				}	
			}
		}
	} else {
		setcookie('uid', NULL, time()-3600*7);
		setcookie('code', NULL, time()-3600*7);
		setcookie('hash', NULL, time()-3600*7);
		header('Location: '.siteURL().'/apanel?act=auth');
	}
		
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo $page['title']; ?></title>
	<link href="<?php echo siteURL(); ?>/core/themes/dashboard/css/font-awesome.css" rel="stylesheet">
	<link href="<?php echo siteURL(); ?>/core/themes/dashboard/css/ionicons.css" rel="stylesheet">
	<link href="<?php echo siteURL(); ?>/core/themes/dashboard/css/chartist.css" rel="stylesheet">
	<link href="<?php echo siteURL(); ?>/core/themes/dashboard/css/rickshaw.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo siteURL(); ?>/core/themes/dashboard/css/dashboard.css">
	<?php if($config['dark_theme'] == True AND $admin['dark_theme'] == True) { echo '<link rel="stylesheet" href="<?php echo siteURL(); ?>/core/themes/dashboard/css/dashboard.css">'; } ?>
</head>
<body>
	<?php if($config['preloader'] == True AND $admin['preloader'] == True) { ?>
	<div id="loading">
		<div id="loading-center">
			<div id="loading-center-absolute">
				<div class="object" id="object_four"></div>
				<div class="object" id="object_three"></div>
				<div class="object" id="object_two"></div>
				<div class="object" id="object_one"></div>
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="slim-header">
		<div class="container">
			<div class="slim-header-left">
				<h2 class="slim-logo"><a href="<?php echo siteURL().'/apanel'; ?>">vkFishing</a></h2>
			</div>
			<div class="slim-header-right">
				<a href="<?php echo siteURL(); ?>/apanel?act=settings" class="header-notification">
					<i class="icon ion-gear-a"></i>
				</a>
				<a href="<?php echo siteURL(); ?>/apanel?act=stats" class="header-notification">
					<i class="icon ion-stats-bars"></i>
				</a>
				<div class="dropdown dropdown-c">
					<a href="#" class="logged-user" data-toggle="dropdown"> <img src="https://vk.com/images/gift/952/512.png" alt=""> <span><?php echo $admin['login']; ?></span> <i class="fa fa-angle-down"></i> </a>
					<div class="dropdown-menu dropdown-menu-right">
						<nav class="nav"><a href="<?php echo siteURL(); ?>/apanel?act=edit" class="nav-link"><i class="icon ion-ios-gear"></i> Настройки</a> <a href="<?php echo siteURL().'/apanel?act=logout&hash='.$_COOKIE['hash']; ?>" class="nav-link"><i class="icon ion-forward"></i> Выход</a> </nav>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="slim-mainpanel" style="position: relative;">
		<div class="container">
			<div class="row row-sm mg-t-20">
				<div class="col-lg-6">
					<div class="slim-pageheader">
						<ol class="breadcrumb slim-breadcrumb"></ol>
						<h6 class="slim-pagetitle">Смена пароля</h6>
					</div>
					<div class="card">
						<div class="card-body pd-30">
							<form method="POST">
								<span>Текущий пароль</span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="password" name="password" class="form-control" placeholder="Введите текущий пароль" required>
										</div>
									</div>
								</div>
								<span>Новый пароль</a></span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="password" name="npassword" class="form-control" placeholder="Придумайте новый пароль" required>
										</div>
									</div>
								</div>
								<span>Повторите пароль</a></span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="password" name="rpassword" class="form-control" placeholder="Повторите новый пароль" required>
										</div>
									</div>
								</div>
								<button class="btn btn-primary pd-x-20" type="submit" name="change_password">Изменить пароль</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="slim-footer">
		<div class="container">
			<p>Copyright 2019 © All Rights Reserved.</p>
			<p>Created with <font color="red">❤</font> by <a href="https://lolzteam.net/aptyp4uk1337" target="_blank">ApTyp4uK1337</a></p>
		</div>
	</div>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/jquery.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/popper.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/bootstrap.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/jquery.cookie.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/chartist.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/d3.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/rickshaw.min.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/jquery.sparkline.min.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/ResizeSensor.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/dashboard.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/slim.js"></script>
	<?php if($config['preloader'] == True AND $admin['preloader'] == True) { ?>
	<script type="text/javascript">
		$(window).load(function() {
			$("#loading").fadeOut(500);
			$("#loading-center").click(function() {
				$("#loading").fadeOut(500);
			})
		})
	</script>
	<?php } ?>
</body>
</html>