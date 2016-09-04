<?php include_once './partials/fejlec.php'; ?>

<?php
	$fel = new Feltolt($db, $fgLex);

	class Feltolt{
		private $db;
		private $fgLex;
		private $dbc;
		private $nagyi;



		function __construct($db, $fgLex){
			$this -> db = $db;
			$this -> fgLex = $fgLex;
			$this -> dbc = $this -> db -> getDbc();
		}

		public function isFeltolt(){
			$hiba = '';


			if($_FILES['feltoltFile']['name'] == '')
				return '<div class="hiba"><p>Nincs CSV fájl kiválasztva!</p></div>';
			else{
				$maxMeret = $this -> db -> getMaxFileSize();
				$mdObjectum = $this -> db -> getMdListByGroupName('ELFOG_FILE_NEV');
				$okNev = null;
				foreach ($mdObjectum as $value){
					$okNev[$value['val']] = $value['txt'];
				}

				if(!in_array($_FILES['feltoltFile']['name'], $okNev))
					$hiba .= '<p>A fájlnév nem engedélyezett, csak a programjaink által generált név elfogadható!</p>';
				if($_FILES['feltoltFile']['size'] > $maxMeret)
					$hiba .= '<p>A fájlméret nem lehet nagyobb '.(int)($maxMeret / 1048576).' MB-nál!</p>';

				if($hiba != '')
					return '<div class="hiba">'.$hiba.'</div>';
				else{
					$ujNev = rand(1, 9).date("ymdHi").rand(1000, 9999); //hossza: 1 random + 10 dátum + 4 random

					$progCd = $progNyelv = array_search($_FILES['feltoltFile']['name'], $okNev);;
					$sqlInsFelt = 'INSERT INTO prim_feltolt (uuid, nyelv_cd, meret) VALUES ("'.$ujNev.'", '.$progCd.', '.$_FILES['feltoltFile']['size'].')';

					if (mysqli_query($this -> dbc, $sqlInsFelt))
						 return $this -> trueFeldolgozo(mysqli_insert_id($this -> dbc), $_FILES['feltoltFile']['tmp_name']);
					else
						return '<div class="hiba"><p>Az adatbázisba nem sikerült az adatok mentése. Kérem, próbálja meg később.<br />'.mysqli_error($this -> dbc).'<p></div>';
				}
			}
		}

		public function trueFeldolgozo($id, $fileTmp){
			$this -> nagyi = $this -> fgLex -> csvDarabolo($fileTmp);

			$ig = Array();
			$modszer = Array();
			$igMd = $this -> db -> getMdListByGroupName('TARTOMANY');
			foreach ($igMd as $value){
				$ig[$value['seq']] = $value['txt'];
			}
			$modszerMd = $this -> db -> getMdListByGroupName('METODUS');
			foreach ($modszerMd as $value){
				$modszer[$value['seq']] = $value['val'];
			}
			
			$hibak = '';
			$osszefoglaloSor = Array();
			$i = 0;
			for($s = 0; $s < count($this -> nagyi); $s++){
				if($this -> nagyi[$s][6] == 'true'){
					if($this -> nagyi[$s][0] < 0 || $this -> nagyi[$s][0] > 13)
						$hibak .= '<p>A szálak száma a megengedett tartományon kívül esik, a '.($s + 1).'. sorban!</p>';
					if ($this -> nagyi[$s][1] != 1)
						$hibak .= '<p>Szabálytalan tartomány kiosztás a '.($s + 1).'. sorban, csak 1-től kezdődhet!</p>';
					if (!in_array($this -> nagyi[$s][2], $ig))
						$hibak .= '<p>Szabálytalan tartomány kiosztás a '.($s + 1).'. sorban!</p>';
					if (!in_array($this -> nagyi[$s][7], $modszer))
						$hibak .= '<p>Ismeretlen módszer a '.($s + 1).'. sorban!</p>';
						
					
					
					if($hibak == ''){
						$osszefoglaloSor[$i][0] = array_search($this -> nagyi[$s][7], $modszer);
						$osszefoglaloSor[$i][1] = array_search($this -> nagyi[$s][2], $ig);
						$osszefoglaloSor[$i][2] = $this -> nagyi[$s][0];
						$osszefoglaloSor[$i][3] = $this -> nagyi[$s][4];
						$osszefoglaloSor[$i][4] = $this -> nagyi[$s][5];
						$osszefoglaloSor[$i][5] = $s;
						
						$i++;
					}
				}
			}

			if($hibak != ''){
				if(strlen($hibak) >= 253)
					$hibaDbBe = substr($hibak, 0, 253).'...';
				else
					$hibaDbBe = $hibak;
				$sqlUpdFelt = 'UPDATE prim_feltolt SET elfogadva_10 = false, hiba = "'.$hibaDbBe.'" WHERE id = '.$id;
				mysqli_query($this -> dbc, $sqlUpdFelt);
				return '<div class="hiba">'.$hibak.'</div>';
			} else {
				foreach($osszefoglaloSor as $value){
					$sqlOsszFel = 'INSERT INTO prim_osszefoglalo (prim_feltolt_id, metodus_cd, max_tartomany_cd, max_szal, indulas_ido, teljes_futasi_ido)';
					$sqlOsszFel .= 'VALUES'; 
					$sqlOsszFel .= '('.$id.', '.$value['0'].', '.$value['1'].', '.$value['2'].', '.$value['3'].', '.$value['4'].')';
					if (mysqli_query($this -> dbc, $sqlOsszFel))
						$this -> osszefoglalo(mysqli_insert_id($this -> dbc), ($value['0'] == 1 ? 0 : $value['2']), $value['5']);
					else{
						$sqlUpdFelt = 'UPDATE prim_feltolt SET elfogadva_10 = false, hiba = "'.substr(mysqli_error($this -> dbc), 0, 253).'...'.'" WHERE id = '.$id;
						mysqli_query($this -> dbc, $sqlUpdFelt);
						return '<div class="hiba"><p>Az adatbázisba nem sikerült az adatok mentése. Kérem, próbálja meg később!<p></div>';
					}
				}
			}
		}
		
		public function osszefoglalo($osszefoglaloId, $visszaSorDb, $sortol){
			echo 'Ossz id: '.$osszefoglaloId;
			echo '<br/>Vissza '.$visszaSorDb.' sort.<br/>';
			echo 'Sortól '.$sortol.'.<br/>';
			echo '<hr />';
		}

		public function megtekintheto(){
			$htmlResult = '';

			$sqlSelFel = '	SELECT
								uuid, feltoltes_datum, meret,
								( SELECT md.leiras FROM prim_md md WHERE md.csoport = "P_NYELV" AND md.sequence = nyelv_cd) AS nyelv
							FROM prim_feltolt
							WHERE
								elfogadva_10 = true
								AND torolt_10 = false';
			$sqlSelFel = mysqli_query($this -> dbc, $sqlSelFel);
			while($sor = mysqli_fetch_assoc($sqlSelFel)){
				$htmlResult .= '<tr>';
					$htmlResult .= '<td>'.$sor['uuid'].'</td>';
					$htmlResult .= '<td>'.$sor['feltoltes_datum'].'</td>';
					$htmlResult .= '<td>'.$sor['nyelv'].'</td>';
					$htmlResult .= '<td>'.$sor['meret'].'</td>';
				$htmlResult .= '</tr>';
			}

			return $htmlResult;
		}

		/*public function alma(){
			$query = "SELECT csoport FROM prim_md";
			$res = mysqli_query($this -> dbc, $query);
			while($sor = mysqli_fetch_assoc($res)){
				echo $sor['csoport'].'<br/>';
			}
		}*/

	}
?>

<script type="text/javascript">
	$( document ).ready(function() {
		$('#tbl_megtekinthet tfoot th').each(function() {
			$(this).html( '<input type="search" placeholder="Search" style="width: 50%;" />' );
		});

		var table = $('#tbl_megtekinthet').DataTable({
			"sScrollX": "100%",
			"bScrollCollapse": true,

			"order": [[ 1, "desc" ]],

			"fnRowCallback" : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).attr("id", aData[0]);
			}
		});

		table.columns().every(function() {
			var that = this;
			$('input', this.footer()).on('keyup change', function() {
				if ( that.search() !== this.value ) {
					that.search( this.value ).draw();
				}
			});
		});

		$('#tbl_megtekinthet tbody').on( 'click', 'tr', function () {
			$('tr').removeClass('selected');
			$(this).addClass('selected');
			selectedId = $(this).attr('id');
			console.log(selectedId);
		});


		$("#tbl_megtekinthet tbody").dblclick(function() {
			if(selectedId != undefined){
				window.location = '.?fileId=' + selectedId;
			}
		});

	});

</script>

<h1>Feltöltés</h1>

<?php if(isset($_POST['btn_fel'])) echo $fel -> isFeltolt(); ?>

<form method="post" enctype="multipart/form-data">
	<input type="file" id="feltoltFile" name="feltoltFile" />
	<input type="submit" id="btn_fel" name="btn_fel" value="Feltöltés"/>
</form>

<br />

<div>Megtekinthető feltöltések</div>

<table id="tbl_megtekinthet" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>Azonosító</th>
			<th>Dátum</th>
			<th>Nyelv</th>
			<th>Méret</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</tfoot>
	<tbody>
		<?php echo $fel -> megtekintheto(); ?>
	</tbody>
</table>

<?php include_once './partials/lablec.php'; ?>
