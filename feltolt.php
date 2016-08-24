<?php include_once './partials/fejlec.php'; ?>

<?php
	$fel = new feltolt($db);
	
	class feltolt{
		private $db;
		private $dbc;
		
		function __construct($db){
			$this -> db = $db;
			$this -> dbc = $this -> db -> getDbc();
		}
		
		public function isFeltolt(){
			$hiba = '';
			
			
			if($_FILES['feltoltFile']['name'] == '')
				return '<div class="hiba"><p>Nincs CSV fájl kiválasztva!</p></div>';
			else{
				$maxMeret = $this -> db -> getMaxFileSize();
				$mdObjectum = $this -> db -> getMdByGroupName('ELFOG_FILE_NEV');
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
					if(!is_dir('./upload_csvs'))
						mkdir('./upload_csvs');
					$teljesNev = './upload_csvs/'.$ujNev;
					move_uploaded_file($_FILES['feltoltFile']['tmp_name'], $teljesNev);
					
					$progCd = $progNyelv = array_search($_FILES['feltoltFile']['name'], $okNev);;
					$sqlInsFelt = 'INSERT INTO prim_feltolt (uuid, nyelv_cd) VALUES ("'.$ujNev.'", '.$progCd.')';
					if (!mysqli_query($this -> dbc, $sqlInsFelt))
						return '<div class="hiba"><p>Az adatbázisba nem sikerült az adatok mentése. Kérem, próbálja meg később.<br />'.mysqli_error($this -> dbc).'<p></div>';
				}
			}
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
				window.location = 'http://hunjoy.esy.es/tea?id=' + selectedId;
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
			<th>Programozási nyelv</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>65347834766535</td>
			<td>2016-01-16 13:20:09</td>
			<td>Java</td>
		</tr>
		<tr>
			<td>65892673252373</td>
			<td>2016-01-16 13:20:09</td>
			<td>Python</td>
		</tr>
		<tr>
			<td>15121609505061</td>
			<td>2016-01-16 13:20:09</td>
			<td>C#</td>
		</tr>
	</tbody>
</table>

<?php include_once './partials/lablec.php'; ?>