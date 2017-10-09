<?php
	class Template
	{
		var $dir;
		function __construct($template)
		{
			if(!file_exists("templates/{$template}.php"))
			{
				die("Template {$template} does not exist");
			}

			$this->dir = "templates/{$template}.php";
		}


		function render($view)
		{
			ob_start();
			require_once($this->dir);
			$tpl = ob_get_contents();
			ob_end_clean();
			$reg = "|<sloc>(.*?)<\/sloc>|";
			$var = preg_match_all($reg,$tpl,$matches,PREG_SET_ORDER);

                        foreach($matches as $match)
			{
				$rep = $match[0];
				$loc =  $match[1];
                               
				if($loc=="content")//this is main content
				{
					$tpl = str_replace($rep,$view->getContent(),$tpl);
				}
				else //this is module
				{
					$mod  =  $view->getModuleContent($loc);
					$tpl = str_replace($rep,$mod,$tpl);
				}
			}
			echo $tpl;
		}
	}
?>