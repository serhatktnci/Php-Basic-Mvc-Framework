<?php
	class SObject extends STable
	{
		public static $dbVars = null; 
	
		public function __construct()
		{
			/*
			$dbVars = array();
			static::$_table = "";
			static::$_key = "id";
			*/
		}
		
		
		public function store($db = false)
		{
			self::checkDatabase();
			parent::store($store);
		}
		
		public function getDbVars()
		{	
		
			$vars = $this::$dbVars;
			
			$all = get_object_vars($this);
			
			$res = array();
			foreach($vars as $v)
			{
				$res[$v] = $all[$v];
			}
			
			return $res;
		}
	
		
		private static function checkDatabase()
		{
			if(static::$dbVars == null)
				die("\$dbVars array must be defined to use database binders");
		}
		
		public function __get($name) 
		{
			die('__get');
			return('dynamic!');
		}
		public function __set($name, $value) 
		{
			$this->internalData[$name] = $value;
		}
		
		public function getDatabase()
		{
			return $this->_database;
		}
		
		public function getDateString($field)
		{
			
			if(isset($this->$field))
			{
				return Date("d-m-Y",$this->$field);
			}
		}
		
	}


?>