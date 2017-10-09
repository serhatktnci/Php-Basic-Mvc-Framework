<?php
class Application
{

	var $section ;
	var $controller;


	function getActiveSection()
	{
		return $this->section;
	}

	function getActiveController()
	{
		return $this->controller;
	}

	function displayAjax($data)
	{
		if(is_object($data))
			echo json_encode($data);
		else
			echo $data;
	}

	function loadModule($name)
	{
		require_once("modules/{$name}.php");
	}

	static function display($filePath,$dir=false)
	{
		$data = Factory::getData();
		require_once "includes/header.php";
		if(!$dir)
		{
			$dir = "sections/".Request::get('s');
		}
		include $dir."/".$filePath;
		require_once "includes/footer.php";
	}
	function getSection($section)
	{
		$dir = "sections";
		$path =  $section."/".$section.".php";
		if(!file_exists($dir."/".$path))
		{
			return $this->getSection("home");
		}
		require_once($dir."/".$path);
		$this->section = $section;

		$name = "SController_".ucfirst($section) ;
		$cl = new $name();

		$this->controller = $cl;
		$cl->execute();

	}

	function getController($section)
	{


		$dir = "sections";
		$path =  $section."/".$section.".php";
		if(!file_exists($dir."/".$path))
		{
			return $this->getSection("home");
		}
		require_once($dir."/".$path);

		$name = "SController_".ucfirst($section) ;
		$cl = new $name();
		return $cl;
	}

	function cookieLogin()
	{
		global $db;
		$cookieName = Config::cookieName;
		if($_COOKIE['secureKey'] == md5($_SERVER['REMOTE_ADDR'].$_COOKIE['userEmail']."*_*{$cookieName}"))
		{
			$user = new User();
			$user->loadUserbyEmail($_COOKIE['userEmail']);

			$ses = Factory::getSession();
			$ses->set('user',$user);

			return true;
		} else
		return false;
	}

	function login($email,$password,$useNickName=false)
	{

		$db = Factory::getDBO();
		$user = new User();

		if($useNickName)
		{

			$user = User::load($email,'username');
		}
		else
		{

			$user =  User::load($email,'email');
		}
		if($user->id > 0 && $password == $user->password)
		{
			$ses = Factory::getSession();
			$ses->set('user',$user);
			//$user->lastLogin = time();
			//$db->updateObject("users",$user,"id");
			if(Request::post('rememberMe'))
			{
				$this->createCookie($email);
			}
			return true;
		} else
		return false;
	}

	function createCookie($email)
	{
		$cookieName = Config::cookieName;
		setcookie ("userEmail", $email, time()+3600*12);
		setcookie ("secureKey", md5($_SERVER['REMOTE_ADDR'].$email."*_*{$cookieName}"), time()+3600*12);
	}

	function destroyCookie()
	{
		setcookie ("userEmail", "", time() - 3600);
		setcookie ("secureKey", "", time() - 3600);
	}

	function setMessage($isError = 0,$text,$showIn="default")
	{
		$message->type	= $isError;
		$message->text	= $text;
		$message->showIn = $showIn;
		$s = Factory::getSession();
		$s->set('message',$message);
	}

	function getMessage()
	{
		$s = Factory::getSession();
		$m = $s->get('message');
		$this->destroyMessage();
		return $m;
	}

	function destroyMessage()
	{
		$s = Factory::getSession();
		$s->delete('message');
	}

	function logout()
	{
		$ses = Factory::getSession();
		$ses->delete('user');
		$ses->delete('uid');
	}

	function redirect($url=false)
	{
		if(!$url)
		{
			$url = "/";
		}

		header('Location: '.$url);
		die();
	}




	function getFileUrl($file=false)
	{
		if($file)
		{
			$dir = dirname($file);

			///echo $dir;
			$pos = strrpos($dir, "sections");
			$rest = $rest = substr($dir, $pos);


			$rest = str_replace("\\","/",$rest);
			return $rest."/";
		}
		else
		return "/";
	}

	function getBaseUrl()
	{
		return "http://dw.sekizbit.net/";
	}

	function timeDiff($timestamp)
	{
		$difference = time() - $timestamp;
		$periods = array("Saniye", "Dakika", "Saat", "Gün", "Hafta", "Ay", "Yıl", "On yıl");
		$lengths = array("60", "60", "24", "7", "4.35", "12", "10");

		if ($difference > 0) { // this was in the past
			$ending = "önce";
		} else { // this was in the future
			$difference = -$difference;
			$ending = "sonra";
		}

		$j = 0;
		while ($difference >= $lengths[$j] && $j < count($lengths))
		{
			$difference /= $lengths[$j];
			$j++;
		}

		$difference = round($difference);
		//if($difference != 1) $periods[$j].= ($periods[$j]!="mes")?"s":"es";
		$text = "$difference $periods[$j] $ending";
		return $text;
	}

	function r_implode( $glue, $pieces )
	{
		foreach( $pieces as $r_pieces )
		{
			if( is_array( $r_pieces ) )
			{
				$retVal[] = $this->r_implode( $glue, $r_pieces );
			}
			else
			{
				$retVal[] = $r_pieces;
			}
		}
		return implode( $glue, $retVal );
	}

}
class App extends Application
{

}
?>
