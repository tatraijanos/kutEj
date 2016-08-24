<?php include_once './partials/fejlec.php'; ?>

<?php
	$fel = new feltolt($dbc);
	
	class feltolt{
		private $dbc;
		
		function __construct($dbc){
			$this -> dbc = $dbc;
		}
		
		public function isFeltolt(){
			$hiba = '';
			$maxmeret = 5242880;	//5 Megabyte a maximális méret
			
			if($_FILES['CSV_fel']['name'] == '')
				return $hibak.'Nincs CSV fájl kiválasztva!</div>';
			
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

<form method="post">
	<input type="file" id="feltoltFile" name="feltoltFile" class="CSV_fel"/>
	<input type="submit" id="btn_fel" name="btn_fel" value="Feltöltés"/>

</form>

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