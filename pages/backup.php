<?php
if($_POST['action'] == 'backup'){
	include('../data/config.php');
	$zip = new ZipArchive();
	date_default_timezone_set('Europe/Rome');
	$date = date("Ymd_Gi");
	$filename = "ICDBN_".$date.".zip";
		
	if ($zip->open("../".$filename, ZIPARCHIVE::CREATE)!==TRUE) {
	    exit("cannot open ../$filename");
	}
	
	if($_CONFIG_DB_USE_SQLITE) $zip -> addFile("../data/icdb.sqlite", "data/icdb.sqlite");
	
	$zip -> addFile("../data/config.php", "data/config.php");
	
	$handler = opendir("../data/pindescs");
	while ($file = readdir($handler)) {
		if ($file != "." && $file != "..")
			$zip->addFile("../data/pindescs/".$file, "data/pindescs/".$file);
	}
	
	$handler = opendir("../data/logos");
	while ($file = readdir($handler)) {
		if ($file != "." && $file != "..")
			$zip->addFile("../data/logos/".$file, "data/logos/".$file);
	}
	
	$handler = opendir("../data/packages");
	while ($file = readdir($handler)) {
		if ($file != "." && $file != "..")
			$zip->addFile("../data/packages/".$file, "data/packages/".$file);
	}
	
	$zip->addEmptyDir("data/datasheets");
	$zip->addEmptyDir("data/appnotes");
	
	closedir($handler);
	$zip->addFromString("backupinfo.txt", "ICDB Backup\n".$_SERVER['SERVER_NAME'].", ".date("d M Y - G:i"));
	echo 'Backup done!<br><b><a id="backuphref" href="'.$filename.'">'.$filename.'</a></b>';
	$zip->close();
}
elseif ($_POST['action'] == 'select'){
	?>
	<div id="popup_header">
		<br>
		<b>PLEASE NOTE:</b> Restoring a backup file will replace ALL current data and files!
		<br><br>
		<form id="restorebackupForm" action="pages/backup.php" method="POST" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="file" name="upfile">
			<br><br>
			<input type="button" class="OkButton" value="Cancel">
			<input type="hidden" name="action" value="restore">
			<input type="hidden" name="MAX_FILE_SIZE" value="10000">
			<input type="submit" value="Restore!">
		</form>
	</div>
	<?php 
;}
elseif ($_POST['action'] == 'restore'){

	function rrmdir($dir) {
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				rrmdir($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}

	if(@is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
		$zip = new ZipArchive;
		if ($zip->open($_FILES["upfile"]["tmp_name"]) === TRUE) {

			rrmdir('../data/');
			$zip->extractTo('..');
			$zip->close();
			header("location: ../index.php"); 
			
		}
		else echo 'Backup restoring failed - can\'t open uploaded zip file! :(';
	}

}
elseif ($_POST['action'] == 'delete'){
	$filename = $_POST['deletebackupfile'];
	if(file_exists("../".$filename)) unlink("../".$filename);
}
?>