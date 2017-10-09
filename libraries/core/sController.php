<?php
	class SController
	{
		var $defaultTemplate="default";

		function __construct()
		{
			$this->defaultTemplate = "default";
		}

		function getTemplate()
		{


			return $this->defaultTemplate;
		}



		function defaultTask()
		{
			echo "override this";
		}

		function get($name)
		{
			require_once "sections/{$name}/{$name}.php";
			$a =  "SController_{$name}";
			$b=  new $a();
			return $b;
		}

		function getPath()
		{
			$rc = new ReflectionClass(get_class($this));
			$dir = dirname($rc->getFileName());
			return $dir;
		}


		function execute()
		{
			$arr = get_class_methods($this);
			$t=  Request::getVar('t');
			if(in_array($t,$arr))
			{
				$this->$t();
			}
			else
			{
				$this->defaultTask();
			}
		}
	}



?>