<?php

	defined('YProtect') or die('Вы не имеете доступа к этому файлу.');

	define('MYSQL_HOST','localhost');
	define('MYSQL_USER','root');
	define('MYSQL_PASS','');
	define('MYSQL_BASE','vkfish');

	$connection = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS) or die ('Не удалось подключиться к серверу MySQL: '.mysql_error());
	$database = mysql_select_db(MYSQL_BASE, $connection) or die ('Не удалось соединиться с базой данных: '.mysql_error());
	mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $connection);



?>
