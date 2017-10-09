<?php
	class SForm extends SView
	{
		private $__tableName;
		private $__ignore;
		
		function __construct($content, $ignore=null, $tableName=false)
		{
			$this->display = "html";
			$this->__dir = "null";
			$this->__content = $content;	
		}
		
		public function setTableName($tb)
		{
			$this->__tableName = $tb;
		}
		
		public function setIgnoredKeys($keys)
		{
			$this->__ignore= $keys;
		}
		
		function getContent()
		{
			$data = $this->__content;
			extract(get_object_vars($this));
			ob_start();
			
			$this->formView();
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
		private function formView()
		{
			$c = Factory::getConfig();
			$data = $this->__content;
			if(is_object($data) && !$this->__tableName )
			{	
				$table = $data->getTableName();
			}
			else
			{
				$table = $this->__tableName;
			}
			$q = "select COLUMN_NAME,COLUMN_COMMENT from information_schema.columns where table_name='{$table}' and TABLE_SCHEMA='{$c->databaseName}'";
			
			$db=Factory::getDBO();
			$res =$db->loadObjectList($q);
			$this->printForm($res,$data);
		}
		
		private function printForm($res,$row)
		{
			echo ("<div>");
			echo ("	<form class=\"sform\" action=\"\" method=\"POST\">");
			foreach($res as $r)
			{
				echo ("<div class=\"row\">");
				$str = "<div class=\"col1\"><label>{$r->COLUMN_NAME}</label></div>";
				echo  ($str);
				$add="";
				if($r->COLUMN_COMMENT =='checkbox')
				{
					if(1==1)
					{
						$add = $row->{$r->COLUMN_NAME}== '1' ?  "checked" :  "";
					}
					$str = "<div class=\"col2\"><input type=\"checkbox\" {$add} name=\"{$r->COLUMN_NAME}\" value='1'></div>";
				}
				else
				{
					if(1==1)
					{
						$k = $r->COLUMN_NAME ;
						$add = "value='{$row->$k}'";
					}
					$dateint ="";
					if(Request::getVar('dateint') && $r->COLUMN_COMMENT == 'dateint')
					{
						$dateint ="class=\"dateint\"";
					}
					$str = "<div class=\"col2\"><input type=\"text\" {$dateint} name=\"{$r->COLUMN_NAME}\" {$add}></div>";
				}
				echo  ($str);
				echo ("</div>");
			}
			?>
			<div class="row">
				<div class="col1"><input type="Reset" name="Reset" value="Reset"/> </div>
				<div class="col2"><input type="Submit" name="submit" value="Submit"/> </div>
			</div>
			<?php
			echo ("	</form>");
			echo ("</div>");
		}
	}
	
	



?>