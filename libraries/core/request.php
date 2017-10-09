<?php
class Request
{
	static function get($name,$type=false)
	{
		$source = $_GET;
		if(isset($source[$name]))
		{
			return $source[$name];
		}
		else
		{
			return null;
		}
	}

	static function post($name)
	{
		$source = $_POST;
		if(isset($source[$name]))
		{
			return $source[$name];
		}
		else
		{
			return null;
		}
	}

	static function getInt($name)
	{
		$source = $_REQUEST;
		if(isset($source[$name]))
		{
			return (int) $source[$name];
		}
		else
		{
			return null;
		}
	}

	static function getVar($name, $default = false)
	{
		$source = $_REQUEST;
		if(isset($source[$name]))
		{
			return $source[$name];
		}
		else
		{
			return $default;
		}
	}

	static function printAll()
	{
		echo '<pre>';
		print_r($_REQUEST);
	}

	static function isAjax()
	{
		return self::getType() == "ajax";
	}

	static function getType()
	{

		$headers = apache_request_headers();

		if( (!empty($headers['X-Requested-With']) && ($headers['X-Requested-With'] == 'XMLHttpRequest')) or (!empty($headers['x-requested-with']) && ($headers['x-requested-with'] == 'XMLHttpRequest'))  )
		{
			return "ajax";
		}
	}
}
?>