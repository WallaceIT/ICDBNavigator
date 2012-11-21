<?php 
include_once('../data/config.php');
if (!isset($_POST['action'])){?>
<div id="popup_header">
	<b>Utilities</b>
	<br><br>
	<form action="#" id="addcategoryForm">
		<input name="addcategoryName" type="text" size="30" placeholder="Add New Category..." required>
		<input type="submit" id="addcategoryButton" value="Add!">
	</form>
	<hr>
	<form action="#" id="addmanufacturerForm">
		<input name="addmanufacturerName" type="text" size="38" placeholder="Add New Manufacturer..." required><br>
		<?php if( class_exists("Imagick") ){?>
		<input name="addmanufacturerLogoUrl" type="url" size="38" placeholder="Manufacturer Logo URL - white2transparent"><br>
		<?php ;}?>
		<input name="addmanufacturerWebsite" type="text" size="30" placeholder="Manufacturer's Website...">
		<input type="submit" value="Add!"><br>
	</form>
	<hr>
	Backup DB: <input type="button" id="backupButton" value="Backup">
	<br>
	<?php if(!$_CONFIG_DB_USE_SQLITE) echo '(<b>MySQL DB backup not yet supported!</b><br>)'; ?>
	<br>
	Restore DB backup: <input type="button" id="restoreselectButton" value="Restore">
	<br><hr>
	Package Generation Workbench: <input type="button" id="pkgWBButton" value="Go!">
	<br>
	Auto-generate default packages: <input type="button" id="pkgautogenButton" value="Generate!">
	<br><hr>
	Download .lib file for kicad (all parts): <input type="button" id="libdownloadButton" value="Download!">
	<br><hr>
	Locally Download PDF files: <input type="button" id="pdfdownloadopenButton" value="Open Dialog">
	<br><hr>
	<input type="button" id="settingsOkButton" value="- Done! -">
	<br>
	<div id="message"></div>
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
						$('#downloadMessage').append(ID+', ');
						datasheetdownload(response);
					}
					else{
						$('#downloadMessage').append('<br><br>downloading appnotes, please wait...<br>IDs: ');
						appnotedownload(1);
					}
				}
				else{
					$('#downloadMessage').append('<br><b>*STOPPED!*</b>');
					$('#downloadControls').html('<input type="button" class="OkButton" value="- Close -">');
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
						$('#downloadMessage').append(ID+', ');
						appnotedownload(response);
					}
					else{
						$('#downloadMessage').append('<br><b>- ALL DONE! -</b>');
						$('#downloadControls').html('<input type="button" class="OkButton" value="- Close -">');
					}
				}
				else{
					$('#downloadMessage').append('<br><b>*STOPPED!*</b>');
					$('#downloadControls').html('<input type="button" class="OkButton" value="- Close -">');
				}
			}
		});
	};
	
	$(document).ready(function() {
		$('#addcategoryForm').submit(function(event){
			event.preventDefault();
			var categoryname = $(this).find( 'input[name="addcategoryName"]' ).val();
			$.ajax({
				type: "POST",
				url: "pages/settings.php",
				data: {categoryname: categoryname, action: 'addcategory'},
				dataType: "text",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		$('#addmanufacturerForm').submit(function(event){
			event.preventDefault();
			var manufacturername = $(this).find( 'input[name="addmanufacturerName"]' ).val();
			var manufacturerwebsite = $(this).find( 'input[name="addmanufacturerWebsite"]' ).val();
			var manufacturerimgurl = $(this).find( 'input[name="addmanufacturerLogoUrl"]' ).val();
			$('#message').html('Adding new manufacturer...');
			$.ajax({
				type: "POST",
				url: "pages/settings.php",
				data: {manufacturername: manufacturername, manufacturerwebsite:manufacturerwebsite, manufacturerimgurl:manufacturerimgurl, action: 'addmanufacturer'},
				dataType: "text",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		$('#backupButton').click( function(){
			var includepdf = 0;
			if( $('#backupIncludepdf').is(':checked') ) includepdf = 1;
			$('#message').html('<br>Creating Backup archive, please wait...');
			$.ajax({
				type: "POST",
				data: {action : 'backup', includepdf: includepdf},
				url: "pages/backup.php",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		$('#restoreselectButton').click( function(){
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
		$('#pkgWBButton').click(function(){
			window.open ('pages/pkggen/pkgwb.php');
		});
		$('#pkgautogenButton').click(function(){
			$.ajax({
				type: "POST",
				url: "pages/pkggen/pkgautogen.php",
				success: function(response){
					$('#message').html(response);
				}
			});
		});
		$('#libdownloadButton').click(function(){
			location.href = 'pages/kicadlib_all.php';
		});
		$('#pdfdownloadopenButton').click( function(){
				$.colorbox({
					html: '<div id="popup_header">'+$('#pdfdownloadPopup').html()+'</div>',
					width: "400px",
					height: "400px",
					overlayClose: false,
					escKey: false
				});
		});

		$('#pdfdownloadButton').live('click', function(){
			$('#downloadControls').html('<input type="button" id="stopdownloadButton" value="STOP!">');
			$('#downloadMessage').html('downloading datasheets, please wait...<br>IDs: ');
			datasheetdownload(1);
		});
		
		$('#stopdownloadButton').live('click', function(){
			stopdl = 1;
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
	});
	</script>
</div>
<div id="pdfdownloadPopup" class="hide">
		Download all non-existent PDF files<br><br>
		<div id="downloadMessage"></div>
		<div id="downloadControls">
			<input type="button" class="OkButton" value="- Cancel -"> 
			<input type="button" id="pdfdownloadButton" value="Download!">
		</div>
</div>
<?php ;}
else if ($_POST['action'] == 'addcategory') {
	if($_CONFIG_DB_USE_SQLITE) $sql = "INSERT INTO categories ('category') VALUES (?)";
	else $sql = "INSERT INTO categories (category) VALUES (?)";
	$st = $db -> prepare($sql);
	$st->bindParam(1, $_POST['categoryname']);
	$st -> execute();
	echo 'New Category <b>'.$_POST['categoryname'].'</b> Added!';}
	
else if ($_POST['action'] == 'addmanufacturer') {
	if($_CONFIG_DB_USE_SQLITE) $sql = "INSERT INTO manufacturers ('name', 'website') VALUES (?, ?)";
	else $sql = "INSERT INTO manufacturers (name, website) VALUES (?, ?)";
	$st = $db -> prepare($sql);
	$st->bindParam(1, $_POST['manufacturername']);
	$st->bindParam(2, $_POST['manufacturerwebsite']);
	$st -> execute();
	//manufacturer logo
	if($_POST['manufacturerimgurl'] != ''){
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
?>
New Manufacturer <b><?php echo $_POST['manufacturername']; ?></b> Added!
<?php ;} ?>