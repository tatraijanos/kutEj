<?php
	$eredm = new eredmenyek();
	
	class eredmenyek{
		private $indx;
		private $alapAdatokArray;
		
		function __construct(){
			$this -> indx = new index();
			
			$this -> alapAdatokArray[0] = $this -> indx -> getTeljesDarab();
			$this -> alapAdatokArray[1] = $this -> indx -> getTeljesIdo();
		}
		
		public function tablazat(){
			$nagyi = $this -> indx -> getSorok();
			$resultHtml = '';
			$osszIdo = 0;
            if(!empty($nagyi)){
				$minimumIdo = null;
				$osszIdo = 0;
				foreach($nagyi as $sor){
					$osszIdo += $sor['Ido'];
					if($minimumIdo > $sor['Start'] || $minimumIdo == null)
						$minimumIdo = $sor['Start'];
				}
				$this -> alapAdatokArray[2] = bcdiv($osszIdo, count($nagyi), 2);
				
				foreach($nagyi as $sor){
					$resultHtml .= '<tr>';
						$resultHtml .= '<td>'.$sor['Szal'].'</td>';
						$resultHtml .= '<td>'.$sor['Ido'].' ms</td>';
						$resultHtml .= '<td>'.($sor['Start'] - $minimumIdo).' ms</td>';
						$resultHtml .= '<td>'.$sor['Tol'].' - '.$sor['Ig'].'</td>';
						$resultHtml .= '<td>'.$sor['Darab'].' db</td>';
					$resultHtml .= '</tr>';
				}
				
            } else {
				$this -> alapAdatokArray[2] = 0;
			}
			
			return $resultHtml;
		}
		
		public function getAlapAdatokArray(){
			return $this -> alapAdatokArray;
		}
	}
	
	$htmlTablazat = $eredm -> tablazat();
	$adatok = $eredm -> getAlapAdatokArray();

?>

<script type="text/javascript">
	$( document ).ready(function() {
		var teljIdoOrigin = <?=$adatok[1]; ?>;
		var atlIdoOrigin = <?=$adatok[2]; ?>;
		
		$('#teljIdo').click(function(){
			if($('#teljIdoMertek').text() == 'milliszekundum'){
				$('#teljIdo').text($('#teljIdo').text() / 1000);
				$('#teljIdoMertek').text('másodperc');
			} else if($('#teljIdoMertek').text() == 'másodperc'){
				$('#teljIdo').text($('#teljIdo').text() / 60);
				$('#teljIdoMertek').text('perc');
			} else if($('#teljIdoMertek').text() == 'perc'){
				$('#teljIdo').text(teljIdoOrigin);
				$('#teljIdoMertek').text('milliszekundum');
			}
		});
		
		$('#atlIdo').click(function(){
			if($('#atlIdoMertek').text() == 'milliszekundum'){
				$('#atlIdo').text($('#atlIdo').text() / 1000);
				$('#atlIdoMertek').text('másodperc');
			} else if($('#atlIdoMertek').text() == 'másodperc'){
				$('#atlIdo').text($('#atlIdo').text() / 60);
				$('#atlIdoMertek').text('perc');
			} else if($('#atlIdoMertek').text() == 'perc'){
				$('#atlIdo').text(atlIdoOrigin);
				$('#atlIdoMertek').text('milliszekundum');
			}
		});
		
	
	
	});
</script>

<h1>Eredmények</h1>

<p>Összes prím: <span><?php echo $adatok[0]; ?></span> darab</p>
<p>Teljes idő: <span id="teljIdo"><?php echo $adatok[1]; ?></span> <span id="teljIdoMertek">milliszekundum</span></p>
<p>Szálak átlagos ideje: <span id="atlIdo"><?php echo $adatok[2]; ?></span> <span id="atlIdoMertek">milliszekundum</span></p>

<div>
	<table>
		<thead>
			<tr>
				<th>Szál</th>
				<th>Idő</th>
				<th>Kezdőidő</th>
				<th>Tartomány</th>
				<th>Megtalált prím</th>
			</tr>
			<?php echo $htmlTablazat; ?>
		</thead>
	</table>
</div>