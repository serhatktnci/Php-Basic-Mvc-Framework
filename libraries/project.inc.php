<?php
	function __autoload($name) 
	{
		
		$n = lcfirst($name);
		include "project/{$n}.php";
	}
?>