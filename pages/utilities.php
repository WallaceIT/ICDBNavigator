<?php
session_start();
if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 'OK')
    return header('HTTP/1.0 401 Unauthorized');

include_once('../data/config.php');
if (!isset($_POST['action'])){?>
<div id="popup_header">
	<b>Utilities</b>
	<div id="tabHeader">
		<div class="tabLink" id="firsttab" title="categoriesTab">Categories</div>
		<div class="tabLink" title="manufacturersTab">Manufacturers</div>
		<div class="tabLink" title="backupTab">Backup/Restore</div>
		<div class="tabLink" title="miscTab">Miscellanea</div>
	</div>
	<div id="tabContainer"></div>
	<br>
	<input type="button" id="settingsOkButton" value="- Done! -">
	<input type="button" id="stopdownloadButton" value="STOP!" style="display: none">
	<br>
	<div id="message"></div>
</div>
	<script type="text/javascript">

	var stopdl = 0;

	function datasheetdownload(ID){
		$.ajax({
			type: "POST",
			data: {
				dlID : ID,
				type : 'datasheet'
				},
			dataType: "text",
			url: "pages/pdfdownload.php",
			success: function(response){
				if(stopdl == 0){
					if(response != 0){
						$('#message').append(ID+', ');
						datasheetdownload(response);
					}
					else{
						$('#message').append('<br><br>downloading appnotes, please wait...<br>IDs: ');
						appnotedownload(1);
					}
				}
				else{
					$('#message').append('<p><b>*STOPPED!*</b></p>');
					$('#stopdownloadButton').hide();
					$('#settingsOkButton').show();
				}
			}
		});
	};

	function appnotedownload(ID){
		$.ajax({
			type: "POST",
			data: {
				dlID : ID,
				type : 'appnote'
				},
			dataType: "text",
			url: "pages/pdfdownload.php",
			success: function(response){
				if(stopdl == 0){
					if(response != 0){
						$('#message').append(ID+', ');
						appnotedownload(response);
					}
					else{
						$('#message').append('<br><b>- ALL DONE! -</b>');
						$('#stopdownloadButton').hide();
						$('#settingsOkButton').show();
					}
				}
				else{
					$('#stopdownloadButton').hide();
					$('#settingsOkButton').show();
				}
			}
		});
	};
	
	$(document).ready(function() {

		$("#tabContainer").html( $("#categoriesTab").html() );
		$("#firsttab").css("color", "red");

		$('.tabLink').click( function(){
			$("#tabContainer").html( $( '#'+$(this).attr('title') ).html() );
			$('.tabLink').css("color", "black");
			$(this).css("color", "red");
		});

		$('#settingsOkButton').click(function(){
			if($('#backuphref').attr('href') != ''){
				$.ajax({
					type: "POST",
					data: {
						action : 'delete',
						deletebackupfile : $('#backuphref').attr('href')
						},
					dataType: "text",
					url: "pages/backup.php",
					success: function(){
						$.colorbox.close();
					}
				});
			}
		});

		/* categoriesTab */
		$('#tabContainer').on('submit', '#addcategoryForm', function(event){
			event.preventDefault();
			$.ajax({
				type: "POST",
				url: "pages/utilities.php",
				data: {
					categoryname: $(this).find( 'input[name="addcategoryName"]' ).val(),
					categoryparent: $(this).find( 'select[name="addcategoryParent"]' ).val(),
					action: 'addcategory'
					},
				dataType: "text",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		
		$('#tabContainer').on('submit', '#removecategoryForm', function(event){
			event.preventDefault();
			$.ajax({
				type: "POST",
				url: "pages/utilities.php",
				data: {
					categoryname: $(this).find( 'select[name="removecategoryName"]' ).val(),
					categoryreplace: $(this).find( 'select[name="removecategoryReplace"]' ).val(),
					action: 'removecategory'},
				dataType: "text",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		/* manufacturersTab */
		$('#tabContainer').on('submit', '#addmanufacturerForm', function(event){
			event.preventDefault();
			$('#message').html('Adding new manufacturer...');
			$.ajax({
				type: "POST",
				url: "pages/utilities.php",
				data: {
					manufacturername: $(this).find( 'input[name="addmanufacturerName"]' ).val(),
					manufacturerwebsite: $(this).find( 'input[name="addmanufacturerWebsite"]' ).val(),
					manufacturerimgurl: $(this).find( 'input[name="addmanufacturerLogoUrl"]' ).val(),
					action: 'addmanufacturer'},
				dataType: "text",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		$('#tabContainer').on('submit', '#removemanufacturerForm', function(event){
			event.preventDefault();
			$.ajax({
				type: "POST",
				url: "pages/utilities.php",
				data: {
					manufacturername: $(this).find( 'select[name="removemanufacturerName"]' ).val(),
					manufacturerreplace: $(this).find( 'select[name="removemanufacturerReplace"]' ).val(),
					action: 'removemanufacturer'},
				dataType: "text",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		/* backupTab */
		$('#tabContainer').on('click', '#backupButton', function(){
			$('#message').html('<br>Creating Backup archive, please wait...');
			$.ajax({
				type: "POST",
				data: {action : 'backup'},
				url: "pages/backup.php",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		$('#tabContainer').on('click', '#restoreselectButton', function(){
			$.ajax({
				type: "POST",
				data: {action : 'select'},
				url: "pages/backup.php",
				success: function(response){
					$.colorbox({
						html:response,
						width: "400px",
						height: "250px",
						});
				}
			});
		});

		/* miscTab */
		$('#tabContainer').on('click', '#pkgWBButton', function(){
			window.open('pages/pkggen/pkgwb.php');
		});
		
		$('#tabContainer').on('click', '#pkgautogenButton',function(){
			$.ajax({
				type: "POST",
				url: "pages/pkggen/pkgautogen.php",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		
		$('#tabContainer').on('click', '#libdownloadButton',function(){
			location.href = 'pages/kicadlib_all.php';
		});

		$('#tabContainer').on('click', '#pdfdownloadButton', function(){
			$('#settingsOkButton').hide();
			$('#stopdownloadButton').show();
			$('#message').html('downloading datasheets, please wait...<br>IDs: ');
			datasheetdownload(1);
		});
		
		$('#stopdownloadButton').click(function(){ stopdl = 1; });
		
	});
	</script>
<div class="hide">
	<!-- *** Tab for categories *** -->
	<div id="categoriesTab">
		<form action="#" id="addcategoryForm">
			Add Category: <input name="addcategoryName" type="text" size="40" placeholder=" - Name -" required><br>
			SubCategory of: <select name="addcategoryParent">
				<option value="NONE" selected> - None - </option>
				<?php
				$categories = $db -> query("SELECT category FROM categories ORDER BY category ASC");
				$curcategory = $categories -> fetchColumn();	
				do{
					echo '<option value="'.$curcategory.'">'.str_replace('@', ' > ', $curcategory).'</option>';
				} while ($curcategory = $categories -> fetchColumn())
				?>
			</select>
			<br><br>
			<input type="submit" value="Add!">
		</form>
		<hr>
		<form action="#" id="removecategoryForm">
			Remove Category: <select name="removecategoryName">
			<?php
				$categories = $db -> query("SELECT category FROM categories ORDER BY category ASC");
				$curcategory = $categories -> fetchColumn();	
				do{
					echo '<option value="'.$curcategory.'">'.str_replace('@', ' > ', $curcategory).'</option>';
				} while ($curcategory = $categories -> fetchColumn())
				?>
			</select>
			<br>
			Move parts to: <select name="removecategoryReplace">
				<?php
				$categories = $db -> query("SELECT category FROM categories ORDER BY category ASC");
				$curcategory = $categories -> fetchColumn();	
				do{
					echo '<option value="'.$curcategory.'">'.str_replace('@', ' > ', $curcategory).'</option>';
				} while ($curcategory = $categories -> fetchColumn())
				?>
			</select>
			<br><br>
			<input type="submit" value="Remove!">
		</form>
	</div>
	
	<!-- *** Tab for manufacturers *** -->
	<div id="manufacturersTab">
		<form action="#" id="addmanufacturerForm">
			Add Manufacturer: <input name="addmanufacturerName" type="text" size="22" placeholder=" - Name -" required>
			<br>
			<?php if( class_exists("Imagick") ){?>
			<input name="addmanufacturerLogoUrl" type="url" size="40" placeholder=" - Logo URL - (white2transparent)">
			<br>
			<?php ;}?>
			<input name="addmanufacturerWebsite" type="text" size="40" placeholder=" - Website -">
			<br>
			<input type="submit" value="Add!"><br>
		</form>
		<hr>
		<form action="#" id="removemanufacturerForm">
			Remove Manufacturer: <select name="removemanufacturerName">
				<?php
				$manufacturers = $db -> query("SELECT name FROM manufacturers ORDER BY name ASC");
				$curmanufacturer = $manufacturers -> fetchColumn();	
				do{
					echo '<option value="'.$curmanufacturer.'">'.$curmanufacturer.'</option>';
				} while ($curmanufacturer = $manufacturers -> fetchColumn());
				?>
			</select>
			<br>
			Move parts to: <select name="removemanufacturerReplace">
				<?php
				$manufacturers = $db -> query("SELECT name FROM manufacturers ORDER BY name ASC");
				$curmanufacturer = $manufacturers -> fetchColumn();	
				do{
					echo '<option value="'.$curmanufacturer.'">'.$curmanufacturer.'</option>';
				} while ($curmanufacturer = $manufacturers -> fetchColumn());
				?>
			</select>
			<br><br>
			<input type="submit" value="Remove!">
		</form>
	</div>
	
	<!-- *** Tab for DB backup and restore *** -->
	<div id="backupTab">
		Backup DB: <input type="button" id="backupButton" value="Backup">
		<br>
		<?php if(!$_CONFIG_DB_USE_SQLITE) echo '(<b>MySQL DB backup not yet supported!</b>)'; ?>
		<br>
		Restore DB backup: <input type="button" id="restoreselectButton" value="Restore">
	</div>
	
	<!-- *** Tab for miscellaneous utilities *** -->
	<div id="miscTab">
		Package Generation Workbench: <input type="button" id="pkgWBButton" value="Go!">
		<br>
		Auto-generate default packages: <input type="button" id="pkgautogenButton" value="Generate!">
		<br><br><hr><br>
		Download .lib file for kicad (all parts): <input type="button" id="libdownloadButton" value="Download!">
		<br><br><hr><br>
		Locally Download PDF files: <input type="button" id="pdfdownloadButton" value="Download!">
	</div>
</div>
<?php ;}
elseif ($_POST['action'] == 'addcategory') {	// ADD new category
	$categoryname = $_POST['categoryname'];
	if($_POST['categoryparent'] != 'NONE') $categoryname = "$_POST[categoryparent]@$categoryname";
	if($_CONFIG_DB_USE_SQLITE) $sql = "INSERT INTO categories ('category') VALUES (?)";
	else $sql = "INSERT INTO categories (category) VALUES (?)";
	$st = $db -> prepare($sql);
	$st->bindParam(1, $categoryname);
	$st -> execute();
	echo 'New Category <b>'.$_POST['categoryname'].'</b> Added!';
}
elseif ($_POST['action'] == 'removecategory') {// REMOVE a category
	if($_POST['categoryname'] == $_POST['categoryreplace'] || preg_match("/^($_POST[categoryname])/", $_POST['categoryreplace'])) exit('Your selections conflict, please select another Category to move parts!');
	$sql = "DELETE FROM categories WHERE category = ?";
	$st = $db -> prepare($sql);
	$st -> bindParam(1, $_POST['categoryname']);
	$st -> execute();
	
	$sql = "DELETE FROM categories WHERE category LIKE '".$_POST['categoryname']."@%'";
	$st = $db -> prepare($sql);
	$st -> execute();
	
	$sql = "UPDATE parts SET category = ? WHERE category = ?";
	$st = $db -> prepare($sql);
	$st->bindParam(1, $_POST['categoryreplace']);
	$st->bindParam(2, $_POST['categoryname']);
	$st -> execute();
	
	$sql = "UPDATE parts SET category = ? WHERE category LIKE '".$_POST['categoryname']."@%'";
	$st = $db -> prepare($sql);
	$st->bindParam(1, $_POST['categoryreplace']);
	$st -> execute();
	echo 'Category <b>'.str_replace('@', ' > ', $_POST['categoryname']).'</b> and all subcategories Removed!';
}	
elseif ($_POST['action'] == 'addmanufacturer') {// ADD new manufacturer
	if($_CONFIG_DB_USE_SQLITE) $sql = "INSERT INTO manufacturers ('name', 'website') VALUES (?, ?)";
	else $sql = "INSERT INTO manufacturers (name, website) VALUES (?, ?)";
	$st = $db -> prepare($sql);
	$st->bindParam(1, $_POST['manufacturername']);
	$st->bindParam(2, $_POST['manufacturerwebsite']);
	$st -> execute();
	//manufacturer logo
	if(isset($_POST['manufacturerimgurl']) && $_POST['manufacturerimgurl'] != ''){
		$dirname = '../data/logos/';
		$filepath = $dirname.str_replace(' ', '', strtolower($_POST['manufacturername']));
		$file = fopen($_POST['manufacturerimgurl'], "rb");
		if ($file){
			$fc = fopen($filepath, "wb");
			while (!feof ($file)) {
				$line = fread($file, 1028);
				fwrite($fc,$line);
			}
			fclose($fc);
		}
		
		if( class_exists("Imagick") ){	//Imagick is installed
			$logo = new Imagick();		
			$logo -> pingImage($filepath);
			$logo -> readImage($filepath);
			$logo -> resizeImage(0,100, imagick::FILTER_LANCZOS, 0.9);
			$logo -> paintTransparentImage('rgb(255,255,255)', 0.0, 7000);
			$logo -> setImageFormat( "gif" );
			$logopath = $dirname.str_replace(' ', '', strtolower($_POST['manufacturername'])).'.gif';
			$logo->writeImage( $logopath );
			if($logo -> clear() && file_exists($filepath)) unlink($filepath);
		}
	};
	echo "New Manufacturer <b>$_POST[manufacturername]</b> Added!";
}
elseif ($_POST['action'] == 'removemanufacturer') {// REMOVE manufacturer
if($_POST['manufacturername'] == $_POST['manufacturerreplace']) exit('Your selections conflict, please select another Manufaturer to move parts!');
	$sql = "DELETE FROM manufacturers WHERE name = ?";
	$st = $db -> prepare($sql);
	$st -> bindParam(1, $_POST['manufacturername']);
	$st -> execute();
	
	$sql = "UPDATE parts SET manufacturer = ? WHERE manufacturer = ?";
	$st = $db -> prepare($sql);
	$st->bindParam(1, $_POST['manufacturerreplace']);
	$st->bindParam(2, $_POST['manufacturername']);
	$st -> execute();
	
	$logofile = "../data/logos/".str_replace(' ', '', strtolower($_POST['manufacturername'])).".gif"; 
	if(file_exists($logofile)) unlink($logofile);
	
	echo "Manufacturer <b>$_POST[manufacturername]</b> Removed!";
}
?>