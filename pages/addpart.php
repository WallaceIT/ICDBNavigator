<?php
include_once('../data/config.php');
if(!isset($_POST['addpartName'])){?>
<div id="resultstitle">Step 1: Add New Part</div>
<div id="popup_header">
	<form id="addpartForm">
		<input name="addpartName" type="text" size="40" placeholder="Name..." required pattern="[-0-9-a-z-A-Z]+">
		<br>
		<select name="addpartCategory" required>
			<option value="" disabled="disabled"> Category... </option>
			<?php
			$categories = $db -> query("SELECT category FROM categories ORDER BY category ASC");
			$curcategory = $categories -> fetchColumn();	
			do{
				echo '<option value="'.$curcategory.'">'.$curcategory.'</option>';
			} while ($curcategory = $categories -> fetchColumn())
			?>
		</select>
		<select name="addpartManufacturer" required>
			<option value="" disabled="disabled"> Manufacturer... </option>
			<?php 
			$manufacturers = $db -> query("SELECT name FROM manufacturers ORDER BY name ASC");
			$curmanufacturer = $manufacturers -> fetchColumn();	
			do{
				echo '<option value="'.$curmanufacturer.'">'.$curmanufacturer.'</option>';
			} while ($curmanufacturer = $manufacturers -> fetchColumn());
			?>
		</select>
		<br>
		<select name="addpartPackage" required>
			<option value="" disabled="disabled"> Package... </option>
			<?php 
			$packages = $db -> query("SELECT * FROM packages ORDER BY pinsnum ASC");	
			while ($row_packages = $packages -> fetch(PDO::FETCH_ASSOC)){
				echo '<option value="'.$row_packages['pkgname'].'">'.$row_packages['pinsnum'].'pin - '.$row_packages['pkgname'].'</option>';
			};
			?>
			<option value="other">other...</option>
		</select>
		<input name="addpartQuantity" size="15" placeholder="Quantity (blank if 0)" pattern="[-0-9]+"><br>
		<input name="addpartDescription" type="text" size="78" placeholder="Description..." required><br>
		<input name="addpartDatasheetUrl" type="url" size="78" placeholder="Datasheet URL..."><br>
		<div align=center><textarea name="addpartSummary" id="clearea">Summary...</textarea></div>
		<br>
		<input type="button" class="OkButton" value="Cancel"> 
		<input type="submit" id="addpartSubmit" value="Next ->">
	</form>
</div>
<?php ;}
else{
	$sqlquery = "SELECT COUNT(*) FROM parts WHERE name = '".$_POST['addpartName']."'";
	$st = $db -> query($sqlquery);	
	
	if(!$st -> fetchColumn()){
	
		if($_CONFIG_PDFDOWNLOAD && $_POST['addpartDatasheetUrl']){;
			$dirname = '../data/datasheets/';
			$filename = $_POST['addpartName'].'.pdf';
			$file = fopen($_POST['addpartDatasheetUrl'], "rb");
			if ($file){
				$fc = fopen($dirname.$filename, "wb");
				while (!feof ($file)) {
					$line = fread($file, 1028);
					fwrite($fc,$line);
				}
				fclose($fc);
			}
		}
		$null = '';
		$_POST['addpartQuantity'] ? $qty = $_POST['addpartQuantity'] : $qty = 0;
		$st = $db -> prepare("INSERT INTO parts ('name','description','manufacturer','package','appnotes','category','quantity','datasheeturl','summary') VALUES (?,?,?,?,?,?,?,?,?)");
		$st->bindParam(1, $_POST['addpartName']);
		$st->bindParam(2, $_POST['addpartDescription']);
		$st->bindParam(3, $_POST['addpartManufacturer']);
		$package = $_POST['addpartPackage'].':'.$qty.';';
		$st->bindParam(4, $package);
		$st->bindParam(5, $null);
		$st->bindParam(6, $_POST['addpartCategory']);
		$st->bindParam(7, $qty);
		$st->bindParam(8, $_POST['addpartDatasheetUrl']);
		$st->bindParam(9, $_POST['addpartSummary']);
		$st -> execute();
		
		return 0;
	}
	else echo '<div id="popup_header">Part <b>'.$_POST['addpartName'].'</b> already present in the DB!<br><br><input type="submit" class="OkButton" value="Ok"></div>';
}
?>