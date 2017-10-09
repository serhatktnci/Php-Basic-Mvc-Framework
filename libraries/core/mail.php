<?php
	class Smail extends SObject
	{
		var $smtp = '';
		var $port = 465;
		var $auth = true;
		var $username = '';	// api key
		var $password = '';	// secret key
		var $crypto = 'SSL';

		var $from = null;
		var $to = null;

		var $subject = null;
		var $body = null;
		var $headers = "";
		var $handler;

		function __construct()
		{
			$this->from = "";
			$this->subject = "";
		}

		function setSubject($subject)
		{
			$this->subject = $subject ;
		}

		function validateEmail($mail=false)
		{
			if($mail)
				$test = $mail;
			else
				$test = $this->to;


			if (filter_var($test, FILTER_VALIDATE_EMAIL))
			{
				return true;
			}
			return false;
		}

		function sendAlive()
		{
			if(!$this->validateEmail($this->to))
				return false;

			if($this->handler == null)
			{
				$this->handler = new SMTP($this->smtp,$this->port,$this->crypto,$this->username,$this->password);
			}
		
			$from = $this->from;
			$header="MIME-Version: 1.0\r\n"; // bu kisim tanimlama kismi
			$header.="Content-type: text/html; charset=utf-8\r\n";
			$header.="From: Finansal Ajanda <".$from.">\r\n";
			$header.="Reply-To: ".$from."\r\n";
			$header.="Return-Path: ".$from."\r\n";

			$res = $this->handler->send($this->from, $this->to, $this->subject, $this->body, $header);
			$this->headers = "";
			return $res;
		}

		function send()
		{
			if(!$this->validateEmail($this->to))
				return false;

			$this->handler = new SMTP($this->smtp,$this->port,$this->crypto,$this->username,$this->password);



			$from = $this->from;
			$header="MIME-Version: 1.0\r\n"; // bu kisim tanimlama kismi
			$header.="Content-type: text/html; charset=utf-8\r\n";
			$header.="From: Finansal Ajanda <".$from.">\r\n";
			$header.="Reply-To: ".$from."\r\n";
			$header.="Return-Path: ".$from."\r\n";


			return $this->handler->send($this->from, $this->to, $this->subject, $this->body, $header);
			//mail($this->to,$this->subject,$this->body,$this->headers);
		}

		function addHeader($header)
		{
			$this->headers .= $header;
		}
}
?>