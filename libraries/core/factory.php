<?php
class Factory
{
	static $weekdays, $months;
	static function getUser($id=false)
	{

		$user = new User();
		if($id)
		{
			$user = $user->loadUser($id);
			return $user;
		}
		else
		{
			$tmp = NULL;
			$ses = Factory::getSession();
			$tmp = $ses->get('user');
			if($tmp)
			{
				$user = $tmp;
			}
			else
			{
				$user->id = 0;
				$user->_guest = true;
			}
		}
		return $user;
	}
	static function getSession()
	{
		return new Session();
	}



	static function getApp()
	{
		return Factory::getApplication();
	}

	static function getApplication()
	{
		return new Application();
	}

	function getData()
	{
		$data = Data::getInstance();
		return $data;
	}

	function dropdownDOB($date=FALSE)
	{

		if($date)
		{
			$date = explode('-', $date);
		}

		Factory::loadDateStr();

		$days 	= range (1, 31);
		$years 	= range (date('Y')-1, 1950);

		$selected ="";
		echo "<select name='birthday' style='float:left;width:40px;'>";
		foreach ($days as $value) {
			if($date) $selected = ($date[2] == $value) ? 'selected' : '';
			echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>\n';
		} echo '</select>';

		echo "<select name='birthmonth' style='float:left;width:120px;'>";
		foreach (self::$months as $key => $value) {
			if($date) $selected = ($date[1] == $key) ? 'selected' : '';
			echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>\n';
		} echo '</select>';

		echo "<select name='birthyear' style='float:left;width:120px;'>";
		foreach ($years as $value) {
			if($date) $selected = ($date[0] == $value) ? 'selected' : '';
			echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>\n';
		}
		echo '</select>';
	}

	function formatDate($date, $format='d/m/Y')
	{
		$months = array (1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık');
		$weekdays =  array("Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi");

		if(is_numeric($date))
		{
			$time = $date;
		}
		else
			$time	= strtotime($date);

		$day_	= date("w", $time);
		$day 	= date("d", $time);
		$month	= (int) date("m", $time);
		$year	= date("Y", $time);

		return ($day. " ".$months[$month]. " ".$year. ", ".$weekdays[$day_]);
	}

	function isValidEmail($email)
	{
		// First, we check that there's one @ symbol,
		// and that the lengths are right.
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters
			// in one section or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++)
		{
			if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
			↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
			$local_array[$i])) {
			  return false;
			}
		}
		// Check if domain is IP. If not,
		// it should be valid domain name
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1]))
		{
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
			  if
			(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
			↪([A-Za-z0-9]+))$",
			$domain_array[$i])) {
				return false;
			  }
			}
		}
		return true;
	}

	static function turkishDate($f, $zt = 'now')
	{

		if(is_numeric($zt))
		{

			$z = date("$f",$zt);
		}
		else
			$z = date("$f", strtotime($zt));


		$donustur = array(
		'Monday'	=> 'Pazartesi',
		'Tuesday'	=> 'Salı',
		'Wednesday'	=> 'Çarşamba',
		'Thursday'	=> 'Perşembe',
		'Friday'	=> 'Cuma',
		'Saturday'	=> 'Cumartesi',
		'Sunday'	=> 'Pazar',
		'January'	=> 'Ocak',
		'February'	=> 'Şubat',
		'March'		=> 'Mart',
		'April'		=> 'Nisan',
		'May'		=> 'Mayıs',
		'June'		=> 'Haziran',
		'July'		=> 'Temmuz',
		'August'	=> 'Ağustos',
		'September'	=> 'Eylül',
		'October'	=> 'Ekim',
		'November'	=> 'Kasım',
		'December'	=> 'Aralık',
		'Mon'		=> 'Pts',
		'Tue'		=> 'Sal',
		'Wed'		=> 'Çar',
		'Thu'		=> 'Per',
		'Fri'		=> 'Cum',
		'Sat'		=> 'Cts',
		'Sun'		=> 'Paz',
		'Jan'		=> 'Oca',
		'Feb'		=> 'Şub',
		'Mar'		=> 'Mar',
		'Apr'		=> 'Nis',
		'Jun'		=> 'Haz',
		'Jul'		=> 'Tem',
		'Aug'		=> 'Ağu',
		'Sep'		=> 'Eyl',
		'Oct'		=> 'Eki',
		'Nov'		=> 'Kas',
		'Dec'		=> 'Ara',
	);
		foreach($donustur as $en => $tr){
			$z = str_replace($en, $tr, $z);
		}
		if(strpos($z, 'Mayıs') !== false && strpos($f, 'F') === false) $z = str_replace('Mayıs', 'May', $z);
		return $z;
	}

	function loadDateStr()
	{
		self::$months = array (1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık');
		self::$weekdays =  array("Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi");

}

	static function getConfig()
	{
		return new Config();
	}

	static function getDBO()
	{
		$config = self::getConfig();
		$database = new Database($config->databaseHost, $config->databaseUser, $config->databasePass, $config->databaseName);
		return $database;
	}

	function getUrl($section, $task)
	{
		return "/?s={$section}&t={$task}";
	}

	function turkishReplace($sData)
	{
		$newphrase=$sData;
	$newphrase = str_replace("Ãœ","U",$newphrase);
	$newphrase = str_replace("Å","S",$newphrase);
	$newphrase = str_replace("Ä","G",$newphrase);
	$newphrase = str_replace("Ã‡","C",$newphrase);
	$newphrase = str_replace("Ä°","I",$newphrase);
	$newphrase = str_replace("Ã–","O",$newphrase);
	$newphrase = str_replace("Ã¼","u",$newphrase);
	$newphrase = str_replace("ÅŸ","s",$newphrase);
	$newphrase = str_replace("Ã§","c",$newphrase);
	$newphrase = str_replace("Ä±","i",$newphrase);
	$newphrase = str_replace("Ã¶","o",$newphrase);
	$newphrase = str_replace("ÄŸ","g",$newphrase);

	$newphrase = str_replace("Ü","U",$newphrase);
	$newphrase = str_replace("Ş","S",$newphrase);
	$newphrase = str_replace("Ğ","G",$newphrase);
	$newphrase = str_replace("Ç","C",$newphrase);
	$newphrase = str_replace("İ","I",$newphrase);
	$newphrase = str_replace("Ö","O",$newphrase);
	$newphrase = str_replace("ü","u",$newphrase);
	$newphrase = str_replace("ş","s",$newphrase);
	$newphrase = str_replace("ç","c",$newphrase);
	$newphrase = str_replace("ı","i",$newphrase);
	$newphrase = str_replace("ö","o",$newphrase);
	$newphrase = str_replace("ğ","g",$newphrase);

	$newphrase = str_replace("%u015F","s",$newphrase);
	$newphrase = str_replace("%E7","c",$newphrase);
	$newphrase = str_replace("%FC","u",$newphrase);
	$newphrase = str_replace("%u0131","i",$newphrase);
	$newphrase = str_replace("%F6","o",$newphrase);
	$newphrase = str_replace("%u015E","S",$newphrase);
	$newphrase = str_replace("%C7","C",$newphrase);
	$newphrase = str_replace("%DC","U",$newphrase);
	$newphrase = str_replace("%D6","O",$newphrase);
	$newphrase = str_replace("%u0130","I",$newphrase);
	$newphrase = str_replace("%u011F","g",$newphrase);
	$newphrase = str_replace("%u011E","G",$newphrase);

	return $newphrase;
	}


	static function getRandomPassword($length=9, $strength=0)
	{
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}

		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

	static function getPageUrl()
	{
		$pageURL = 'http';
		 if (@$_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		 $pageURL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		 } else {
		  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 }
		 return $pageURL;
	}

}
?>