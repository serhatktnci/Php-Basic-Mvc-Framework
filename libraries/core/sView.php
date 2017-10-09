<?php
	class SView
	{
		private $__dir;
		private $__modules;
		protected $__content;
		public $display;
		public $controller;

		function __construct($content,$owner=false)
		{                    
			$this->display = "html";
			if(!$owner)
			{
				echo "send controller also";
				die();
			}

			if( get_called_class() == "SList")
			{
				global $app;
				$owner = $app->getActiveController();
			}

			$this->controller = $owner;


			if( get_called_class() != "SList")
				$this->__dir = $owner->getPath();


                        $this->__content = $content;
		
		}

		function getContent()
		{                                    
                        // Import variables into the current symbol table from an array
			extract(get_object_vars($this));
			ob_start();
			require_once($this->__dir."/html/{$this->__content}.php");
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}

		static function displayJson($obj)
		{
			echo json_encode($obj);
		}

		function display($template=false,$format=false)
		{
			if(!$template)
			{
				$template = $this->controller->getTemplate();
			}

			if(!$format)
			{
				$format = Request::getVar('display');
				if(!$format)
					$format = $this->display;
			}
			$this->display = $format;
			if($format == "xml")
			{
				echo "xml render not installed";
			}
			else if ($format == "json")
			{
				echo json_encode($this) ;
			}
			else if($format == "raw")
			{
				echo $this->getContent();
			}
			else
			{
				$t = new Template($template);
				$t->render($this);
			}
		}
		function getModuleContent($pos)
		{
			if(isset($this->__modules->$pos))
			{

				return $this->__modules->$pos->getContent();
			}
			else
			{
				return "";
				return "no module set for <b>{$pos}</b> position, you may hide this error in sView.php";
			}

		}
		function setModule($pos,$mod)
		{
			$this->__modules->$pos = $mod;
		}
	}
?>