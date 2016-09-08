<?php include_once './partials/fejlec.php'; ?>

<?php
	//print_r($_POST);
	
	$indx = new Index($db, $fgLex);
	class Index{
		const P_NYELV = array(	'J0' => 'Java', 'J1' => 'JavaScript', 
								'C0' => 'C#', 'C1' => 'C++', 
								'D0' => 'Delphi', 
								/*'P0' => 'PHP',*/ 'P1' => 'Python', 
								'V0' => 'Visual Basic');
		const P_NYTSZ = array(	'C#', 'C++', 'Delphi', 'Java', 'JavaScript', 'Perl', 'PHP', 'PHP7', 'Python', 'Ruby', 'Visual Basic');
		const METODUS = array(	'Prim1' => 'Szál nélküli', 'Prim2' => 'Normál', 'Prim3' => 'Hatvány', 'Prim4' => 'Fibonacci', 
								'Prim50' => 'Pascal normál', 'Prim51' => 'Pascal optimális', 'Prim52' => 'Pascal páratlan', 
								'Prim6' => 'Arányos', 'Prim70' => 'Öszetett', 'Prim71' => 'Prím', 'Prim8' => 'Félprím', 
								'Prim90' => 'Koch-görbe', 'Prim91' => 'Inverz négyzetes');
		const INTVALL = array(	1000 => '1 -    1000', 10000 => '1 -   10000', 
								100000 => '1 -  100000', 1000000 => '1 - 1000000');
								
		private static $teljesIdo = 0;
		private static $teljesDarab = 0;
		private static $sorok;
		
		private $fgLex;
		private $dbc;
		private $feltoltott10;
		private $maxSzal = 15;

		
		
		function __construct($db, $fgLex){
			$this -> dbc = $db -> getDbc();
			$this -> fgLex = $fgLex;
			
			if(!isset($_GET['fileId']))
				$this -> feltoltott10 = false;
			else if($_GET['fileId'] == '')
				$this -> feltoltott10 = false;
			else{
				$sqlSelFel= 'SELECT true FROM prim_feltolt WHERE uuid = "'.$_GET['fileId'].'"';
				$sqlSelFel = mysqli_query($this -> dbc, $sqlSelFel);
				if(mysqli_num_rows($sqlSelFel) == 1)
					$this -> feltoltott10 = true;
				else{
					//echo '<div class="hiba">Nem található ilyen azonosítójú feltöltés!</div>';
					$this -> feltoltott10 = false;
				}
			}
		}
								
		public function option($ba){
			$optVissza = '';
			
			if($this -> feltoltott10){
				/*$sqlSelOss = '	SELECT 
								( SELECT md.leiras 
								  FROM prim_md md 
								  WHERE md.csoport = "P_NYELV" AND md.sequence = 
									( SELECT u.nyelv_cd 
									  FROM prim_feltolt u 
									  WHERE u.uuid = "'.$_GET['fileId'].'")
								) AS nyelv,
								( SELECT md.leiras
								  FROM prim_md md 
								  WHERE md.csoport = "METODUS" AND md.sequence = s.metodus_cd) AS metodus,
								( SELECT md.leiras 
								  FROM prim_md md 
								  WHERE md.csoport = "TARTOMANY" AND md.sequence = s.max_tartomany_cd) AS max_tartomany,
								s.max_szal
								FROM prim_osszefoglalo s
								WHERE s.prim_feltolt_id = 
									( SELECT u.id 
									  FROM prim_feltolt u 
									  WHERE u.uuid = "'.$_GET['fileId'].'")';
				*/
				$sqlSelOss = '	SELECT 
									(SELECT md.leiras 
									 FROM prim_md md 
									 WHERE md.csoport = "P_NYELV" AND md.sequence = 
										(SELECT u.nyelv_cd 
										 FROM prim_feltolt u 
										 WHERE u.uuid = "'.$_GET['fileId'].'")
									) AS nyelv, 
									s.metodus_cd, 
									(SELECT md.leiras 
									 FROM prim_md md 
									 WHERE md.csoport = "METODUS" AND md.sequence = s.metodus_cd) AS metodus_neve 
								FROM prim_osszefoglalo s 
								WHERE 
									s.prim_feltolt_id = 
										(SELECT u.id 
										 FROM prim_feltolt u 
										 WHERE u.uuid = "'.$_GET['fileId'].'") 
								GROUP BY s.metodus_cd';
				$sqlSelOss = mysqli_query($this -> dbc, $sqlSelOss);
				$pNyelv = null;
				$metodus = Array();
				while($sor = mysqli_fetch_assoc($sqlSelOss)){
					if($pNyelv == null){
						$pNyelv = $sor['nyelv'];
					}
					$metodus[$sor['metodus_cd']] = $sor['metodus_neve'];
				}
				if($ba == 'nyelv'){
					$optVissza = '<option value="1" selected="selected">'.$pNyelv.'</option>';
				} else if($ba == 'metod'){
					foreach($metodus as $key => $value){
						if($_POST['metod'] == $key){
							$optVissza .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
						} else {
							$optVissza .= '<option value="'.$key.'">'.$value.'</option>';
						}
					}
				} else if($ba == 'inter' && !empty($_POST['metod'])){
					$sqlSelOss = '	SELECT 
										s.max_tartomany_cd, 
										(SELECT md.leiras 
										 FROM prim_md md 
										 WHERE md.csoport = "TARTOMANY" AND md.sequence = s.max_tartomany_cd) AS max_tartomany 
									FROM prim_osszefoglalo s 
									WHERE 
										s.prim_feltolt_id = 
											(SELECT u.id 
											 FROM prim_feltolt u 
											 WHERE u.uuid = "'.$_GET['fileId'].'") AND 
										s.metodus_cd = '.$_POST['metod'].'
									GROUP BY s.max_tartomany_cd';
					$sqlSelOss = mysqli_query($this -> dbc, $sqlSelOss);
					while($sor = mysqli_fetch_assoc($sqlSelOss)){
						if($_POST['inter'] == $sor['max_tartomany_cd']){
							$sqlSelOssMaxSzal = '	SELECT 
														MAX(max_szal) AS max_szal
													FROM prim_osszefoglalo s 
													WHERE 
														s.prim_feltolt_id = 
															(SELECT u.id 
															 FROM prim_feltolt u 
															 WHERE u.uuid = "'.$_GET['fileId'].'") AND 
														s.metodus_cd = '.$_POST['metod'].' AND 
														s.max_tartomany_cd = '.$_POST['inter'];
							$sqlSelOssMaxSzal = mysqli_query($this -> dbc, $sqlSelOssMaxSzal);
							$sors = mysqli_fetch_assoc($sqlSelOssMaxSzal);
							$this -> maxSzal = $sors['max_szal'];
							
							$optVissza .= '<option value="'.$sor['max_tartomany_cd'].'" selected="selected">'.$sor['max_tartomany'].'</option>';
						} else {
							$optVissza .= '<option value="'.$sor['max_tartomany_cd'].'">'.$sor['max_tartomany'].'</option>';
						}
					}
				}
			} else {
				$constArray = null;
				$postKey = null;
				if($ba == 'nyelv'){
					$constArray = index::P_NYELV;
					$postKey = $_POST['nyelv'];
				} else if($ba == 'metod'){
					$constArray = index::METODUS;
					$postKey = $_POST['metod'];
				} else if($ba == 'inter'){
					$constArray = index::INTVALL;
					$postKey = $_POST['inter'];
				}
				
				foreach($constArray as $key => $value){
					if($key == $postKey){
						$optVissza .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
					} else {
						$optVissza .= '<option value="'.$key.'">'.$value.'</option>';
					}
				}
				if($ba == 'nyelv'){
					$optVissza .= '<optgroup label="Prímtesztelők">';
					foreach(index::P_NYTSZ as $value){
						$optVissza .= '<option value="'.$key.'" disabled="disabled">'.$value.'</option>';
					}
					$optVissza .= '</optgroup>';
				}
			}
			
			return $optVissza;
		}
		
		public function validator(){
			$hibak = '';
			if(empty($_POST['nyelv']) && !$this -> feltoltott10){
				$hibak .= '<p>Kérem, válassza ki a programozási nyelvet!</p>';
			}
			
			if(empty($_POST['metod'])){
				$hibak .= '<p>Kérem, válassza ki a metódust!</p>';
			}
			
			if(empty($_POST['inter'])){
				$hibak .= '<p>Kérem, válassza ki az intervallumot!</p>';
			}
			
			if(empty($_POST['szalak'])){
				$hibak .= '<p>Kérem, határozza meg a szálak számát!</p>';
			}
			
			if($_POST['metod'] == 'Prim1' && $_POST['szalak'] > 1){
				$hibak .= '<p>Szál nélküli módban csak 1 lehet a szálak száma!</p>';
			}
			
			
			
			
			if($hibak != ''){
				return '<div class="hiba">'.$hibak.'</div>';
			} else {
				if($this -> feltoltott10)
					return $this -> getOsszefoglaloId();
				else
					return $this -> fileMeghatarozo();
			}
			
		}
		
		private function fileMeghatarozo(){
			if($_POST['nyelv'] == 'J0'){
				$fileUt = './csvs/primJava.csv';
                return $this -> fajlMegnyit($fileUt);
			}
			
			else {
				return '<div class="hiba"><p>A fájl nem elérhető, kérem próbálja meg később!</p></div>';
			}
		}
		
		private function fajlMegnyit($fileUt){
			if(file_exists($fileUt)){
				$kiir = $this -> sorKivalaszt($this -> fgLex -> csvDarabolo($fileUt));
				return $kiir;
			} else 
				return '<div class="hiba"><p>A fájl nem elérhető, kérem próbálja meg később!</p></div>';
		}
		
		private function sorKivalaszt($nagyi){
			$sorunk = -1;
			
			for($s = 0; $s < count($nagyi); $s++){
				if($nagyi[$s][6] == 'true' && $nagyi[$s][2] == $_POST['inter'] && $nagyi[$s][0] == $_POST['szalak'] && $nagyi[$s][7] == $_POST['metod'])
					$sorunk = $s;
			}
			
			if($sorunk > -1){
				$selSor = array();
				$vissza = '';
				
				$osszIdo = 0;
				if($_POST['metod'] == 'Prim1')
					$selSor[] = array('Szal' => $nagyi[$sorunk][0], 'Tol' => $nagyi[$sorunk][1], 'Ig' => $nagyi[$sorunk][2], 'Darab' => $nagyi[$sorunk][3], 'Start' => $nagyi[$sorunk][4], 'Ido' => $nagyi[$sorunk][5]);
				else{
                    for($i = $sorunk - $_POST['szalak']; $i < $sorunk; $i++){
						$selSor[]  = array('Szal' => $nagyi[$i][0], 'Tol' => $nagyi[$i][1], 'Ig' => $nagyi[$i][2], 'Darab' => $nagyi[$i][3], 'Start' => $nagyi[$i][4], 'Ido' => $nagyi[$i][5]);
                    }
				}
				
				self::$teljesIdo = $nagyi[$sorunk][5];
				self::$teljesDarab = $nagyi[$sorunk][3];
				
				foreach($selSor as $key => $row){
					$szalRendez[$key] = $row['Szal'];
				}
				array_multisort($szalRendez, SORT_ASC, $selSor);
				self::$sorok = $selSor;
				
				$vissza .= '<div name = "sorok">';
				for($i = 0; $i < count($selSor); $i++){
					$vissza .= $selSor[$i]['Szal'].';'.$selSor[$i]['Tol'].';'.$selSor[$i]['Ig'].';'.$selSor[$i]['Darab'].';'.$selSor[$i]['Start'].';'.$selSor[$i]['Ido'].';<br />';
				}
				$vissza .= '</div>';
				
				return $vissza;
				
			}
			if($sorunk == -1)
                return '<div class="hiba"><p>Nincsenek a keresésnek megfelelő elemek.</p></div>';
			
		}
		
		public function getOsszefoglaloId(){
			$sqlSelOss = '	SELECT s.id
							FROM prim_osszefoglalo s
							WHERE 
								s.prim_feltolt_id = 
									(SELECT u.id 
									 FROM prim_feltolt u 
									 WHERE u.uuid = "'.$_GET['fileId'].'") AND 
								s.metodus_cd = '.$_POST['metod'].' AND 
								s.max_tartomany_cd = '.$_POST['inter'].' AND 
								s.max_szal = '.$_POST['szalak'].'
							GROUP BY s.indulas_ido DESC
							LIMIT 1';
			$sqlSelOss = mysqli_query($this -> dbc, $sqlSelOss);
			$osszefoglaloId = mysqli_fetch_assoc($sqlSelOss)['id'];
			
			$sqlSelEre = '	SELECT * 
							FROM prim_eredmenyek e 
							WHERE e.prim_osszefoglalo_id = '.$osszefoglaloId;
			$sqlSelEre = mysqli_query($this -> dbc, $sqlSelEre);
			$vissza = '<div name = "sorok">';
			while($sor = mysqli_fetch_assoc($sqlSelEre)){
				$vissza .= $sor['szal'].';'.$sor['int_tol'].';'.$sor['int_ig'].';'.$sor['megtalalt_prim_darab'].';'.$sor['szal_indulas_ido'].';'.$sor['szal_futas_ido'].';<br />';
			}
			$vissza .= '</div>';
			
			return $vissza;
		}
		
		public function getTeljesIdo(){
			return self::$teljesIdo;
		}
				
		public function getTeljesDarab(){
			return self::$teljesDarab;
		}
		
		public function getSorok(){
			return self::$sorok;
		}
		
		public function isFeltoltott10(){
			return $this -> feltoltott10 ? 'true' : 'false';
		}
		
		public function getMaxSzal(){
			return $this -> maxSzal;
		}
		
	}
?>

<!-- TODO: Norbi, ez csak teszt miatt kell, majd kiemelheted fájlba... -->
<style>
	select{
		 width: 150px;
	}
</style>

<script type="text/javascript">
	$( document ).ready(function() {
		//$("#szalak").attr('max', <?=$indx -> getMaxSzal()?>);
		
		var metodVal = $("#metod option:selected" ).val();
		if(metodVal == 'Prim1' || (<?=$indx -> isFeltoltott10()?> && metodVal == 1)){
			$("#szalak").attr('readonly', true);
			$("#szalak").val(1);
		}
		
		$("#nyelv").select2({
			placeholder: "Kérem válasszon!",
			allowClear: true
		});
		
		$("#metod").select2({
			allowClear: true,
			placeholder : "Kérem válasszon!",
			allowClear : true
		}).on("change", function(e) {
			if(this.value == 'Prim1'){
				$("#szalak").attr('readonly', true);
				$("#szalak").val(1);
			} else {
				$("#szalak").attr('readonly', false);
			}
		});
		
		$("#inter").select2({
			placeholder: "Kérem válasszon!",
			allowClear: true
		});
		
		if(<?=$indx -> isFeltoltott10()?>){
			feltoltottMod();
		}
		
	});
	
	function feltoltottMod(){
		$("#cim").text('Animáció - Feltöltés mód');
		$("#nyelv").attr('disabled', true);
		$("#metod").attr('onchange', "this.form.submit()");
		$("#inter").attr('onchange', "this.form.submit()");
	}
	
</script>

<h1 id="cim">Animáció</h1>

<?php if(isset($_POST['btn_betoltes'])) $eredmeny = $indx -> validator(); ?>

<form method="post">
	<fieldset>
		<label for = "nyelv">Program nyelv:</label>
		<select name = "nyelv" id = "nyelv" >
			<option></option>
			<?php echo $indx -> option('nyelv'); ?>
		</select>
		
		<label for = "metod">Metódus:</label>
		<select name = "metod" id = "metod">
			<option></option>
			<?php echo $indx -> option('metod'); ?>
		</select>
		
		<label for = "inter">Intervallum:</label>
		<select name = "inter" id = "inter">
			<option></option>
			<?php echo $indx -> option('inter'); ?>
		</select>
		
		<label for = "szalak">Szálak száma:</label>
		<input type = "number" name = "szalak" id = "szalak" min = "1" max="<?php echo $indx -> getMaxSzal(); ?>" value = "<?php if(isset($_POST['szalak'])) echo $_POST['szalak']; else echo '1'; ?>" />
		
		<br />
		
		<input type = "submit" name = "btn_betoltes" value = "Betöltés" />
	</fieldset>
</form>

<?php if(isset($eredmeny)) echo $eredmeny; ?>

<div><?php include_once './eredmenyek.php'; ?></div>


<?php include_once './partials/lablec.php'; ?>