<?php

	defined('YProtect') or die('Вы не имеете доступа к этому файлу.');

	// ================================================================= //
	// =================== [ ЗАПРОСЫ К БАЗЕ ДАННЫХ ] =================== //
	// ================================================================= //

	// ЗАПРОСЫ К БАЗЕ ДАННЫХ » Получение информации » Основные настройки сайта
	$config = mysql_fetch_assoc(mysql_query("SELECT * FROM `config`"));
	
	// ================================================================= //
	// ===================== [ ПОСЕТИТЕЛИ САЙТА ] ====================== //
	// ================================================================= //
	
	if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) { // Если подключен CloudFlare, получаем настоящий IP
		$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	
	function visitors() {
		$ip = getIp();
		$os = getOS($_SERVER['HTTP_USER_AGENT']);
		$browser = getBrowser($_SERVER['HTTP_USER_AGENT']);
		$url = getenv("HTTP_REFERER");
		$date = serverCurrentDate();
		
		$query = "INSERT INTO `visitors`
				  (`ip`,`os`,`browser`,`url`,`date`)
				  VALUES ('$ip','$os','$browser','$url','$date')
				 ";
		$res = mysql_query($query) or die(mysql_error());
	}

	// ================================================================= //
	// ================= [ ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ ] ================== //
	// ================================================================= //

	class Switcher{
        private static $switch = array(
            "а" => "f", "А" => "F",
            "б" => ",", "Б" => "<",
            "в" => "d", "В" => "D",
            "г" => "u", "Г" => "D",
            "д" => "l", "Д" => "L",
            "е" => "t", "Е" => "T",
            "ё" => "`", "Ё" => "~",
            "ж" => ";", "Ж" => ":",
            "з" => "p", "З" => "P",
            "и" => "b", "И" => "B",
            "й" => "q", "Й" => "Q",
            "к" => "r", "К" => "R",
            "л" => "k", "Л" => "K",
            "м" => "v", "М" => "V",
            "н" => "y", "Н" => "Y",
            "о" => "j", "О" => "J",
            "п" => "g", "П" => "G",
            "р" => "h", "Р" => "H",
            "с" => "c", "С" => "C",
            "т" => "n", "Т" => "N",
            "у" => "e", "У" => "E",
            "ф" => "a", "Ф" => "A",
            "х" => "[", "Х" => "{",
            "ц" => "w", "Ц" => "W",
            "ч" => "x", "Ч" => "X",
            "ш" => "i", "Ш" => "I",
            "щ" => "o", "Щ" => "O",
            "ъ" => "]", "Ъ" => "}",
            "ы" => "s", "Ы" => "S",
            "ь" => "m", "Ь" => "M",
            "э" => "'", "Э" => "\"",
            "ю" => ".", "Ю" => ">",
            "я" => "z", "Я" => "Z",
            "," => "?", "." => "/"
        );
        
        public static function fromCyrillic( $string ){
            return
                strtr( $string, self::$switch  );
        }
        
        public function toCyrillic( $string ){
            return
                strtr( $string, array_flip( self::$switch )  );
        }
    }

	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Отображение системных ошибок на сайте
	if($config['display_errors'] == True) {
		if(isset($_COOKIE['vkid']) AND isset($_COOKIE['code'])) {
			$account = mysql_fetch_assoc(mysql_query("SELECT `rank` FROM `accounts` WHERE `vkid` = '".$_COOKIE['vkid']."' AND `code` = '".$_COOKIE['code']."'"));
			if($account['rank'] >= 5) {
				ini_set('display_errors','On');
				#error_reporting('E_ALL');
			}
		}
	}
	
	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » VK API Method
	function apiMethod($method, $params) {
		$url = 'https://api.vk.com/method/'.$method.'?'.$params.'';
		return $url;
	}

	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » CURL GET CONTENTS
	function curl_get_contents($url) {
	  $curl = curl_init($url);
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	  $data = curl_exec($curl);
	  curl_close($curl);
	  return $data;
	}
	
	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » Удобочитаемая дата
	function new_time($date_str){
		if(strtotime($date_str) < strtotime(date('Y-m-d H:i:s'))) {
			$time = time();
			$date_str = strtotime($date_str);
			$tm = date('H:i', $date_str);
			$d = date('j', $date_str);
			$m = date('m', $date_str);
			$y = date('Y', $date_str);
			if($m == 1) $nmonth = 'янв';
			if($m == 2) $nmonth = 'фев';
			if($m == 3) $nmonth = 'мар';
			if($m == 4) $nmonth = 'апр';
			if($m == 5) $nmonth = 'мая';
			if($m == 6) $nmonth = 'июн';
			if($m == 7) $nmonth = 'июл';
			if($m == 8) $nmonth = 'авг';
			if($m == 9) $nmonth = 'сен';
			if($m == 10) $nmonth = 'окт';
			if($m == 11) $nmonth = 'ноя';
			if($m == 12) $nmonth = 'дек';
			$last = round(($time - $date_str)/60);
			if($last == 0) return "только что";
			elseif($last == 1) return "минуту назад";
			elseif( $last < 55 ) return "$last ".wordEndings($last, "минуту", "минуты", "минут")." назад";
			elseif($d.$m.$y == date('dmY',$time)) return "Сегодня в $tm";
			elseif($d.$m.$y == date('dmY', strtotime('-1 day'))) return "Вчера в $tm";
			elseif($y == date('Y',$time)) return "$d $nmonth в $tm";
			else return "$d $nmonth $y в $tm";
		} else {
			$date_str = strtotime($date_str);
			$tm = date('H:i', $date_str);
			$d = date('j', $date_str);
			$m = date('m', $date_str);
			$y = date('Y', $date_str);
			if($m == 1) $nmonth = 'янв';
			if($m == 2) $nmonth = 'фев';
			if($m == 3) $nmonth = 'мар';
			if($m == 4) $nmonth = 'апр';
			if($m == 5) $nmonth = 'мая';
			if($m == 6) $nmonth = 'июн';
			if($m == 7) $nmonth = 'июл';
			if($m == 8) $nmonth = 'авг';
			if($m == 9) $nmonth = 'сен';
			if($m == 10) $nmonth = 'окт';
			if($m == 11) $nmonth = 'ноя';
			if($m == 12) $nmonth = 'дек';
			
			return "$d $nmonth $y в $tm";
		}
	}
	
	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Дата в формате 1 января 1970
	function nameDate($date) {
		$day = date("j", strtotime($date));
		$month = date("m", strtotime($date));
		$year = date("Y", strtotime($date));
		
		$months = Array(
			"01" => "января",
			"02" => "февраля",
			"03" => "марта",
			"04" => "апреля",
			"05" => "мая",
			"06" => "июня",
			"07" => "июля",
			"08" => "августа",
			"09" => "сентября",
			"10" => "октября",
			"11" => "ноября",
			"12" => "декабря"
		);
		 
		$new_date = str_replace($month, $months[$month], $month);
		
		return $day .' '. $new_date .' '. $year;
	}
	
	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Отображение текущего UTC
	function currentUTC() {
		$hours = (date('Z')/3600);
		
		if(preg_match("/^(-\d+)$/", $hours)) {
			$response = '(UTC+'.$hours.':00)';
		} elseif(mb_strlen($hours) >= 2) {
			$response = '(UTC+'.$hours.':00)';
		} else {
			$response = '(UTC+0'.$hours.':00)';
		}
		
		return $response;
	}
	
	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » Определение URL адрес сайта
	function siteURL() {
		global $config;

		if((isset($_SERVER['REQUEST_SCHEME']) AND $_SERVER['REQUEST_SCHEME'] === 'https') OR (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] === 'on')) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}

		$siteURL = $protocol . $_SERVER['SERVER_NAME'];

		return $siteURL;
	}

	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » Текущий URL адрес посетителя
	function userCurrentURL() {
		global $config;

		$currentURL = siteURL() . $_SERVER['REQUEST_URI'];

		return $currentURL;
	}

	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » Текущая дата на сервере
	function serverCurrentDate() {
		date_default_timezone_set('Europe/Tallinn');
		$today = date("Y-m-d H:i:s");

		return $today;
	}
	
	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » Склонение числительных
	function wordEndings($n, $n1, $n2, $n5) {
		if($n >= 11 and $n <= 19) {
			return $n5;
		}
		$n = $n % 10;
		if($n == 1) {
			return $n1;
		}
		if($n >= 2 and $n <= 4) {
			return $n2;
		}
		return $n5;
	}

	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » IP адрес пользователя
	function getIp() {
		$client  = $_SERVER['HTTP_CLIENT_IP'];
		$forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		 
		if(filter_var($client, FILTER_VALIDATE_IP)) {
			$ip = $client;
		} elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}
		 
		return $ip;
	}
	
	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » ОС пользователя
	function getOS($userAgent) {
  		$oses = array (
  			'Windows 3.11' => '(Win16)',
  			'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
  			'Windows 98' => '(Windows 98)|(Win98)',
  			'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
  			'Windows 2000 Service Pack 1' => '(Windows NT 5.01)',
  			'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
  			'Windows Server 2003' => '(Windows NT 5.2)',
  			'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
  			'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
  			'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
  			'Windows 8.1' => '(Windows NT 6.3)',
  			'Windows 10' => '(Windows NT 10.0)|(Windows NT 6.4)',
  			'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
  			'Windows ME' => '(Windows ME)|(Windows 98; Win 9x 4.90 )',
  			'Windows CE' => '(Windows CE)',
			'iOS' => '(iPad)',
			'iOS' => '(iPhone)',
  			'Mac OS X Kodiak (beta)' => '(Mac OS X beta)',
  			'Mac OS X Cheetah' => '(Mac OS X 10.0)',
  			'Mac OS X Puma' => '(Mac OS X 10.1)',
  			'Mac OS X Jaguar' => '(Mac OS X 10.2)',
  			'Mac OS X Panther' => '(Mac OS X 10.3)',
  			'Mac OS X Tiger' => '(Mac OS X 10.4)',
  			'Mac OS X Leopard' => '(Mac OS X 10.5)',
  			'Mac OS X Snow Leopard' => '(Mac OS X 10.6)',
  			'Mac OS X Lion' => '(Mac OS X 10.7)',
  			'Mac OS X' => '(Mac OS X)',
  			'Mac OS' => '(Mac_PowerPC)|(PowerPC)|(Macintosh)',
  			'Open BSD' => '(OpenBSD)',
  			'SunOS' => '(SunOS)',
  			'Solaris 11' => '(Solaris/11)|(Solaris11)',
  			'Solaris 10' => '((Solaris/10)|(Solaris10))',
  			'Solaris 9' => '((Solaris/9)|(Solaris9))',
  			'CentOS' => '(CentOS)',
  			'QNX' => '(QNX)',
  			'UNIX' => '(UNIX)',
  			'Ubuntu 12.10' => '(Ubuntu/12.10)|(Ubuntu 12.10)',
  			'Ubuntu 12.04 LTS' => '(Ubuntu/12.04)|(Ubuntu 12.04)',
  			'Ubuntu 11.10' => '(Ubuntu/11.10)|(Ubuntu 11.10)',
  			'Ubuntu 11.04' => '(Ubuntu/11.04)|(Ubuntu 11.04)',
  			'Ubuntu 10.10' => '(Ubuntu/10.10)|(Ubuntu 10.10)',
  			'Ubuntu 10.04 LTS' => '(Ubuntu/10.04)|(Ubuntu 10.04)',
  			'Ubuntu 9.10' => '(Ubuntu/9.10)|(Ubuntu 9.10)',
  			'Ubuntu 9.04' => '(Ubuntu/9.04)|(Ubuntu 9.04)',
  			'Ubuntu 8.10' => '(Ubuntu/8.10)|(Ubuntu 8.10)',
  			'Ubuntu 8.04 LTS' => '(Ubuntu/8.04)|(Ubuntu 8.04)',
  			'Ubuntu 6.06 LTS' => '(Ubuntu/6.06)|(Ubuntu 6.06)',
  			'Red Hat Linux' => '(Red Hat)',
  			'Red Hat Enterprise Linux' => '(Red Hat Enterprise)',
  			'Fedora 17' => '(Fedora/17)|(Fedora 17)',
  			'Fedora 16' => '(Fedora/16)|(Fedora 16)',
  			'Fedora 15' => '(Fedora/15)|(Fedora 15)',
  			'Fedora 14' => '(Fedora/14)|(Fedora 14)',
  			'Chromium OS' => '(ChromiumOS)',
  			'Google Chrome OS' => '(ChromeOS)',
			'Android' => '(Android)',
  			'Linux' => '(Linux)|(X11)',
  			'OpenBSD' => '(OpenBSD)',
  			'FreeBSD' => '(FreeBSD)',
  			'NetBSD' => '(NetBSD)',
  			'iPod' => '(iPod)',
  			'iPhone' => '(iPhone)',
  			'iPad' => '(iPad)',
  			'OS/8' => '(OS/8)|(OS8)',
  			'Older DEC OS' => '(DEC)|(RSTS)|(RSTS/E)',
  			'WPS-8' => '(WPS-8)|(WPS8)',
  			'BeOS' => '(BeOS)|(BeOS r5)',
  			'BeIA' => '(BeIA)',
  			'OS/2 2.0' => '(OS/220)|(OS/2 2.0)',
  			'OS/2' => '(OS/2)|(OS2)',
  			'Search engine or robot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(msnbot)|(Ask Jeeves/Teoma)|(ia_archiver)'
  		);

  		foreach($oses as $os=>$pattern){
  			if(preg_match("/$pattern/i", $userAgent)) {
  				return $os;
  			}
  		}
  			return 'Unknown';
  	}


	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » Браузер пользователя
	function getBrowser($userAgent) {
  		$browsers = array (
  			'Mozilla Firefox' => 'Firefox',
  			'Opera' => 'Opera',
  			'Yandex Browser' => 'YaBrowser',
  			'Google Chrome' => 'Chrome',
  			'Internet Explorer' => 'MSIE',
  			'Apple Safari' => 'Safari',
  			'Konqueror' => 'Konqueror',
  			'Debian Iceweasel' => 'Iceweasel',
  			'SeaMonkey' => 'SeaMonkey',
  			'Microsoft Edge' => 'Edge',
  			'MyIE' => 'myie',
  			'Netscape' => 'netscape',
  			'Mozilla' => 'mozilla',
  			'Opera Mini' => 'opera mini',
  			'Sylera' => 'sylera',
  			'Songbird' => 'songbird',
  			'Firebird' => 'firebird',
  			'GranParadiso' => 'paradiso',
  			'Phoenix' => 'phoenix',
  			'Powermarks' => 'powermarks',
  			'FreeBSD' => 'freebsd',
  			'Lynx' => 'Lynx',
  			'PlayStation' => 'playstation',
  			'NetPositive' => 'netpositive',
  			'Minimo' => 'minimo',
  			'Links' => 'links',
  			'K-Meleon' => 'k-meleon',
  			'IceCat' => 'icecat',
  			'Flock' => 'Flock',
  			'Epiphany' => 'Epiphany',
  			'Camino' => 'Camino',
  			'Avant Browser' => 'avant browser',
  			'America Online' => 'america online',
  			'Amaya' => 'amaya'
  		);

  		foreach($browsers as $browser=>$pattern){
  			if(preg_match("/$pattern/i", $userAgent)) {
  				return $browser;
  			}
  		}
  			return 'Unknown';
  	}
	
	// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ » Функция » Генерация кода для Email
	function rcode($number) {
		$arr = array('A','B','C','D','E','F',
					 'G','H','I','J','K','L',
					 'M','N','O','P','R','S',
					 'T','U','V','X','Y','Z',
					 '1','2','3','4','5','6',
					 '7','8','9','0');
		$pass = "";
		for($i = 0; $i < $number; $i++)
		{
		  $index = rand(0, count($arr) - 1);
		  $pass .= $arr[$index];
		}
		return $pass;
	}

?>
