<?php include_once './partials/fejlec.php'; ?>

<?php 
	$strat = new Strategia($db);
	
	class Strategia{
		private $db;
		
		function __construct($db){
			$this -> db = $db;
		}
		
		public function option($ba){
			$resultHtml = '';
			$constArray = null;
			$postKey = null;
			if($ba == 'metod'){
				$constArray = $this -> db -> getMdListByGroupName('METODUS');
				$postKey = $_POST['metod'];
			} else if($ba == 'inter'){
				$constArray = $this -> db -> getMdListByGroupName('TARTOMANY');
				$postKey = $_POST['inter'];
			}
			
			foreach($constArray as $row){
				if($row['seq'] == $postKey){
					$resultHtml .= '<option value="'.$row['seq'].'" selected="selected">'.$row['txt'].'</option>';
				} else {
					$resultHtml .= '<option value="'.$row['seq'].'">'.$row['txt'].'</option>';
				}
			}
			
			return $resultHtml;
		}
		
	}
?>

<h1>Stratégiák</h1>

<form method="post">
	<fieldset>
	
		<label for = "metod">Metódus:</label>
		<select name = "metod" id = "metod">
			<option></option>
			<?php echo $strat -> option('metod'); ?>
		</select>
		
		<label for = "inter">Intervallum:</label>
		<select name = "inter" id = "inter">
			<option></option>
			<?php echo $strat -> option('inter'); ?>
		</select>
		
		<label for = "szalak">Szálak száma:</label>
		<input type = "number" name = "szalak" id = "szalak" min = "1" max="15" value = "<?php if(isset($_POST['szalak'])) echo $_POST['szalak']; else echo '1'; ?>" />
		
		<br />
		
		<input type = "submit" name = "btn_betoltes" value = "Betöltés" />
		
	</fieldset>
</form>

<?php include_once './partials/lablec.php'; ?>