<?php
	class Database extends SObject
	{
		var $query;
		var $conn ;
		var $server= null;
		var $dbuser = null;
		var $dbpass= "";
		var $db = null;
		var $error;

		var $_nameQuote		= '`';

		function __construct($server, $dbuser,$dbpass,$db)
		{
			$this->server = $server;
			$this->dbuser = $dbuser;
			$this->dbpass = $dbpass;
			$this->db = $db;
			$this->connect();
		}

		function connect()
		{
			$this->conn = mysql_connect($this->server, $this->dbuser, $this->dbpass) or die ("Baglanamadi " .mysql_error() ) ;
			mysql_select_db($this->db);
			mysql_query("SET NAMES 'utf8'", $this->conn);
		}

		function loadObjectList($query,$type=false)
		{
			$result = $this->query($query) ;

			$arr = array();
			while($row = mysql_fetch_object($result))
			{
				if($type)
				{
					$r = new $type();
					$r->bind($row);
					$row = $r;
				}
				$arr[] = $row;


				unset($row);
			}
			return $arr;
		}

		function loadArrayList($query)
		{
			$result = $this->query($query) ;

			$arr = array();
			while($row = mysql_fetch_array($result))
			{
				$arr[] = $row;

				unset($row);
			}
			return $arr;
		}

		function loadColArr($query, $index=0)
		{
			$result = $this->query($query) ;

			$arr = array();
			while($row = mysql_fetch_row($result))
			{
				$arr[] = $row[$index];
				unset($row);
			}
			return $arr;
		}



		function lastInsertId()
		{
			return mysql_insert_id($this->conn);
		}

		function query($query)
		{
			$startTime = microtime(true);

			$res = mysql_query($query);
			//file_put_contents("query.log",mysql_affected_rows()."\t".$query."\r\n",FILE_APPEND);
			if($res)
			{
				$this->error = false;

			}
			else
			{
				$this->error = mysql_error();


				$c  = Factory::getConfig();
				$u = Factory::getUser();
				$a->userId = $u->id;
				$a->time = time();
				$a->ip  = $_SERVER['REMOTE_ADDR'];
				$a->get = $_GET;
				$a->post = $_POST;
				$a->session = $_SESSION;
				$a->user_agent = $_SERVER['HTTP_USER_AGENT'];
				$a->query = $query;
				$text = json_encode($a);


				if($c->logQueryErrors)
				{

					file_put_contents($c->queryErrorLogPath,$text."\r\n",FILE_APPEND);
				}
				if($c->reportQueryErrors) {
					$mail = new SMail();
					$mail->to = 'admin@sekizbit.com.tr';
					// $mail->to = 'nepjua@gmail.com';
					$mail->subject = 'Possible Attack';

					ob_start();

					echo "<!doctype html>";
					echo "<html>";
					echo "<body>";
					echo "<p>A Possible Attack has been detected</p>";
					echo "<pre>";
					print_r($a);
					echo "</pre>";
					echo "<p> as json </p>";
					echo "<br>";
					echo "<pre>{$text}</pre>";
					echo "</body>";
					echo "</html>";

					$mail->body = ob_get_clean();

					$mail->send();
				}
				if($c->showQueryErrors) {
					echo $query;
					echo "<br/>";
					echo ($this->error);
					echo "<br/>";
				} else {
					echo "<p>Ooops!!!</p>";
					echo "<p>Something went really wrong!!!</p>";
				}
				if($c->banPossibleAttacks) {

					@$cnt = $_SESSION['dbErrorCount'];

					if($cnt) {
						$cnt += 1;
						if($cnt >= 3) {
							$cnt = 0;
							$il = new IpLog();
							$il->ip = $a->ip;
							$il->logTime = time();
							$il->logObj = $text;
							$il->store();
						}
					} else {
						$cnt = 1;
					}

					$_SESSION['dbErrorCount'] = $cnt;

				}

				die();
			}

			if(!isset($_SESSION['queryLog']))
			{
				$_SESSION['queryLog'] = array();
			}

			$endTime = microtime(true);

			/*

			$log = null;
			$log->query = $query;
			$log->timeTook = ($endTime - $startTime) * 1000  ;
			$_SESSION['queryLog'][$_SERVER['REQUEST_TIME']][] =  $log;
			*/
			return $res;
		}

		function loadObject($query,$type=false)
		{
			return $this->loadSingleObject($query,$type);
		}

		function loadSingleObject($query,$type=false)
		{
			$result = $this->query($query) ;
			$row = mysql_fetch_object($result);

			if($type)
			{
				$r = new $type();
				$r->bind($row);
				$row = $r;
			}

			return $row;
		}

		function loadValue($query)
		{
			$res = $this->query($query)or mysql_error();
			if( mysql_num_rows( $res  ) > 0	)
			{
				$row = mysql_fetch_row($res);
				return $row[0];
			}
			else
				return null;
		}


		function updateObject($table, &$object, $keyName, $updateNulls=true )
		{
			$fmtsql = 'UPDATE '.$this->nameQuote($table).' SET %s WHERE %s';
			$tmp = array();
			$vars = method_exists($object,"getDbVars") ? $object->getDbVars() : get_object_vars( $object );
			foreach ($vars as $k => $v)
			{
				if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
					continue;
				}
				if( $k == $keyName )
				{ // PK not to be updated
					$where = $keyName . '=' . $this->Quote( $v );
					continue;
				}
				if ($v === null)
				{
					if ($updateNulls)
					{
						$val = 'NULL';
					}
					else
					{
						continue;
					}
				} else {
					$val = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
				}
				$tmp[] = $this->nameQuote( $k ) . '=' . $val;
			}
			//$this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
			$query = sprintf( $fmtsql, implode( ",", $tmp ) , $where );
			//echo $query;
			return $this->query($query);
		}




		function insertObject( $table, &$object, $keyName = NULL )
		{
			$fmtsql = 'INSERT INTO '.$this->nameQuote($table).' ( %s ) VALUES ( %s ) ';
			$fields = array();
			$vars = method_exists($object,"getDbVars") ? $object->getDbVars() : get_object_vars( $object );
			foreach ($vars as $k => $v)
			{
				if (is_array($v) or is_object($v) or $v === NULL)
				{
					continue;
				}
				if ($k[0] == '_')
				{ // internal field
					continue;
				}
				$fields[] = $this->nameQuote( $k );
				$values[] = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
			}
			//$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
			if (!$this->query(sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) )))
			{
				return false;
			}
			$id = $this->lastInsertId();
			if ($keyName && $id) {
				$object->$keyName = $id;
			}
			return true;
		}

		function insertObjects($table, &$objectArray, $keyName = NULL )
		{
			$fmtsql = 'INSERT INTO '.$this->nameQuote($table).' ( %s ) VALUES %s  ';

			$fields = array();
			$valueStr = "";
			$vars = method_exists($object,"getDbVars") ? $object->getDbVars() : get_object_vars( $object );
			foreach ($vars as $k => $v)
			{

				$object = $objectArray[$i];
				$values = array();
				foreach (get_object_vars( $object ) as $k => $v)
				{
					if (is_array($v) or is_object($v))
					{
						continue;
					}
					if ($k[0] == '_')
					{ // internal field
						continue;
					}
					if($i==0)
						$fields[] = $this->nameQuote( $k );

					$values[] = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
				}
				//$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
				$valueStr .= " ( ". implode( ",", $values ) .")" ;
				if($i != count($objectArray) -1)
					$valueStr .=  ",";
			}
			$query = sprintf( $fmtsql, implode( ",", $fields ) ,  $valueStr  );

			if (!$this->query($query))
			{
				return false;
			}
			$id = $this->lastInsertId();
			if ($keyName && $id) {
				$object->$keyName = $id;
			}
			return true;

		}


		function isQuoted( $fieldName )
		{
			return true;
			/*
			if ($this->_hasQuoted)
			{
				return in_array( $fieldName, $this->_quoted );
			}
			else
			{
				return true;
			}
			*/
		}


		function Quote( $text, $escaped = true )
		{
			return '\''.($escaped ? $this->getEscaped( $text ) : $text).'\'';
		}

		function getEscaped( $text, $extra = false )
		{
			$result = mysql_real_escape_string( $text, $this->conn );
			if ($extra)
			{
				$result = addcslashes( $result, '%_' );
			}
			return $result;
		}

		function nameQuote( $s )
		{
			// Only quote if the name is not using dot-notation
			if (strpos( $s, '.' ) === false)
			{
				$q = $this->_nameQuote;
				if (strlen( $q ) == 1) {
					return $q . $s . $q;
				} else {
					return $q{0} . $s . $q{1};
				}
			}
			else {
				return $s;
			}
		}
	}
?>