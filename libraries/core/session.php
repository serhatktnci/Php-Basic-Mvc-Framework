<?php
	class Session
	{
		function set($name, $data)
		{
			$_SESSION[$name] = serialize($data);
		}
		function get($name)
		{
			if(isset($_SESSION[$name]))
			{
				return unserialize($_SESSION[$name]);
			}
			else
				return false;
		}
		
		function delete($name)
		{
			unset($_SESSION[$name]);
		}
		function deleteAll()
		{
		
		}
		
		function printAll()
		{
			echo '<pre>';
			var_dump($_SESSION);
		}
	}
?>