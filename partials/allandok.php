<?php
	class allandok{
		const DB_HOST = 'localhost';
		const DB_USER = 'root';
		const DB_PASSWORD = '';
		const IDOZONA = 'Europe/Budapest';
		
		private static $dbc;
		
		private function dbc_conn($db_name){
			date_default_timezone_set(allandok::IDOZONA);
			self::$dbc = mysqli_connect(allandok::DB_HOST, allandok::DB_USER, allandok::DB_PASSWORD, $db_name) 
				or die ('<H1>Adatbázis hiba! Kérem, próbálja meg később!</H1>');
			mysqli_query(self::$dbc, "SET NAMES 'utf8'");
			mysqli_query(self::$dbc, "SET CHARACTER SET 'utf8'");
		}
		
		public function getDbc($db_name){
			$this -> dbc_conn($db_name);
			return self::$dbc;
		}
		
		public function dbcClose(){
			try {
				mysqli_close(self::$dbc);
			} catch (Exception $e) {
				echo '<p class="hiba">HIBA: ',  $e -> getMessage(), '</p>';
			}
		}
		
	}
?>