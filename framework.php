<?php
	session_start();
	defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
	date_default_timezone_set("GMT");
	//framework file.
	require_once "config.php";
	require_once "libraries/core.inc.php";       
	//project depended files.
	require_once "libraries/project.inc.php";

?>