<?php
	
	require_once "framework.php";
	
	$user = Factory::getUser();
	$section = Request::getVar('s');

        /*if($user->guest())
	{
		
		$section = "login";
	}*/
	

	$app = Factory::getApplication();
	
        global $app;
	        
	$app->getSection($section);
	
?>

