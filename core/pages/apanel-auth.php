<?php

	session_start();

	if(empty($_COOKIE['uid']) OR empty($_COOKIE['code']) OR empty($_COOKIE['hash'])) {
		if(isset($_POST['enter'])) {
			if(isset($_POST['login']) AND isset($_POST['password']) AND isset($_POST['ip_h'])) {
				if($_POST['ip_h'] == strrev(md5($_SERVER['REMOTE_ADDR']))) {
					$login = $_POST['login'];
					$password = $_POST['password'];
					
					$result = mysql_query("SELECT `id` FROM `users` WHERE `login` = '$login'");
					if(mysql_num_rows($result) > 0) {
						$result = mysql_query("SELECT `id` FROM `users` WHERE `login` = '$login' AND `password` = '$password'");
						if(mysql_num_rows($result) > 0) {
							$user = mysql_fetch_assoc(mysql_query("SELECT `id`, `code` FROM `users` WHERE `login` = '$login' AND `password` = '$password'"));
							$code = md5($user['code']);
							$hash = md5($_SERVER['REMOTE_ADDR'].':'.$user['id']);
							
							setcookie('uid', $user['id'], time()+3600*7);
							setcookie('code', $code, time()+3600*7);
							setcookie('hash', $hash, time()+3600*7);
							
							header('Location: '.siteURL().'/apanel');
						}
					}
				}
			}
		}
	} else {
		header('Location: '.siteURL().'/apanel');
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
	<div class="signin-wrapper">
		<div class="signin-box">
			<h2 class="slim-logo"><a href="<?php echo siteURL().'/apanel'; ?>">Панель управления</span></a></h2>
			<h2 class="signin-title-primary">Авторизация</h2>
			<h3 class="signin-title-secondary">Добро пожаловать!</h3>
			<form method="POST">
				<div class="form-group">
					<input type="hidden" class="form-control" name="ip_h" value="<?php echo strrev(md5($_SERVER['REMOTE_ADDR'])); ?>">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="login" placeholder="Введите свой логин" required>
				</div>
				<div class="form-group mg-b-50">
					<input type="password" class="form-control" name="password" placeholder="Введите свой пароль" required>
				</div>
				<button class="btn btn-primary btn-block btn-signin" name="enter" type="submit">Войти</button>
			</form>
		</div>
	</div>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/jquery.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/popper.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/dashboard/js/bootstrap.js"></script>
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