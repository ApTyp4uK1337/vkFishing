<?php

	ob_start();

	session_start();
	
	define('YProtect', True);
	
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/brain.php';
	
?>