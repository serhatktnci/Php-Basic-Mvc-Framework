<?php
	class STable extends SObjectBase
	{

		public static  $_table = "";
		public static  $_key = "id";


		function setKey($val)
		{
			$keyName = $this->getKey();
			$this->$keyName = $val;
		}

		function delete($db)
		{
			if(!$db) {
				$db = Factory::getDBO();
			}

			$keyName = $this->getKey();
			if(!is_null($this->$keyName) && ((int) $this->$keyName )> 0 )
			{
				$q = "DELETE  FROM ".$this->getTableName()." where {$keyName}='{$this->$keyName}'";
				return $db->query($q);
			}
			else
			{
				return false;
			}
		}

		public function safeStore($db = false)
		{
			if(!$db) {
				$db = Factory::getDBO();
			}

			$key = $this->getKey();

			if(is_null($this->$key) OR ((int) $this->$key )<= 0 )
			{
				$db->insertObject($this->getTableName(),$this,$this->getKey());
			}
			else
			{
				$tb=  $this->getTableName();
				$key = $this->getKey();
				$q = "SELECT COUNT(*) FROM {$tb} where `{$key}`='{$this->$key}'";
				if($db->loadValue($q) >0) {
					$db->updateObject($this->getTableName(),$this,$this->getKey());
				}
				else {
					$db->insertObject($this->getTableName(),$this,$this->getKey());
				}
			}

		}

		public function store($db = false)
		{
			if(!$db) {
				$db = Factory::getDBO();
			}

			$key = $this->getKey();

			if(is_null($this->$key) OR ((int) $this->$key )<= 0 )
			{
				$db->insertObject($this->getTableName(),$this,$this->getKey());
			}
			else
			{
				$db->updateObject($this->getTableName(),$this,$this->getKey());
			}
		}

		public function insert($db = false)
		{
			if(!$db) {
				$db= Factory::getDBO();
			}

			$key = $this->getKey();
			$db->insertObject($this->getTableName(),$this,$this->getKey());
		}

		public function update($db = false)
		{

			if(!$db) {
				$db= Factory::getDBO();
			}

			$db= Factory::getDBO();
			$key = $this->getKey();
			$db->updateObject($this->getTableName(),$this,$this->getKey());
		}

		static function getTableName()
		{
			return static::$_table;
		}

		static function getKey()
		{
			return static::$_key;
		}

		public static function loadAll($bind=false,$value=false,$field=false, $db = false)
		{

			if(!$field)
				$field = self::getKey();
			$add = "";

			if($value != false or !is_bool($value))
			{
				$add = " WHERE {$field} = '{$value}'";
			}

			if(!$db) {
				$db = Factory::getDBO();
			}

			$table = self::getTableName();
			$q = "SELECT * FROM {$table} {$add}";
			$res = $db->loadObjectList($q);
			if($bind)
				return self::bindAll($res);
			return $res;
		}

		public function loadIn($value=false, $field=false)
		{
			if(!isset($this))
			{
				die("loadIn function must be called in object context, can not be used static");
			}

			if(!$field)
				$field = $this->getKey();

			if(!$value)
				$value = $this->$field;

			$db = Factory::getDBO();
			$table = self::getTableName();
			$q = "SELECT * FROM {$table} where {$field}='{$value}'";

			$a = $db->loadObject($q);
			$this->bind($a);
			return $this;
		}

		public static function load($value,$field=false, $db = false)
		{

			if(!$field)
				$field = self::getKey();

			if(!$db) {
				$db= Factory::getDBO();
			}

			$table = self::getTableName();

			$value = $db->getEscaped($value);
			$q = "SELECT * FROM {$table} where {$field}='{$value}'";
			$n = get_called_class();
			$tmp = new $n();
			$a = $db->loadObject($q);

			$tmp->bind($a);
			return $tmp;
		}

	}


?>