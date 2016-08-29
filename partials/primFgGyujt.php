<?php
	class PrimFgGyujt{
		
		public function csvDarabolo($fileUt){
			$nagyi = Array();
			
			$file = fopen($fileUt, 'r');
			$tartalom = fread($file, filesize($fileUt));
			fclose($file);
			
			$sorok = explode("\r\n", $tartalom);
			
			for($s = 0; $s < count($sorok) - 1; $s++){
				$nagyi[$s] = str_replace('"', '', explode('";"', $sorok[$s]));
			}
			
			
			return $nagyi;
		}
		
	}

?>