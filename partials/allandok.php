<?php
	class Allandok{
		const DB_HOST = 'localhost';
		const DB_USER = 'root';
		const DB_PASSWORD = '';
		const IDOZONA = 'Europe/Budapest';
		
		const MAX_FILE_SIZE = 5242880; 		//5 Megabyte a maximális méret
		
		
		private static $dbc;
		
		
		
		
		function __construct(){
			$this -> dbc_conn();
		}
		
		public function getMaxFileSize(){
			return allandok::MAX_FILE_SIZE;
		}
		
		public function getDbc(){
			return self::$dbc;
		}
		
		
		private function dbc_conn(){
			date_default_timezone_set(allandok::IDOZONA);
			self::$dbc = mysqli_connect(allandok::DB_HOST, allandok::DB_USER, allandok::DB_PASSWORD, 'prim') 
				or die ('<H1>Adatbázis hiba! Kérem, próbálja meg később!</H1>');
			mysqli_query(self::$dbc, "SET NAMES 'utf8'");
			mysqli_query(self::$dbc, "SET CHARACTER SET 'utf8'");
		}
		
		public function dbcClose(){
			try {
				mysqli_close(self::$dbc);
			} catch (Exception $e) {
				echo '<p class="hiba">HIBA: ',  $e -> getMessage(), '</p>';
			}
		}
		
		public function getMdListByGroupName($csoportNeve, $torolt = 'false'){
			$result = null;
			$queryMd = 'SELECT * FROM prim_md WHERE csoport = "'.$csoportNeve.'" AND torolt_10 = '.$torolt;
			$queryMd = mysqli_query(self::$dbc, $queryMd);
			$i = 0;
			while($sor = mysqli_fetch_assoc($queryMd)){
				$result[$i]['grp'] = $csoportNeve;
				$result[$i]['seq'] = $sor['sequence'];
				$result[$i]['txt'] = $sor['leiras'];
				$result[$i]['val'] = $sor['ertek'];
				$i++;
			}
			
			return $result;
		}
		
		public function getEntityMdByGroupNameAndSeq($csoportNeve, $seq){
			$sql = 'SELECT * FROM prim_md WHERE csoport = "'.$csoportNeve.'"';
		}
		
	}
?>