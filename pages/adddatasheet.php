<?php 
include('../data/config.php');	
if (isset($_POST['partID'])){
	include_once('../data/config.php');
	$st = $db -> prepare("UPDATE parts SET datasheeturl = ? WHERE ID = ?");
	$st->bindParam(1, $_POST['url']);
	$st->bindParam(2, $_POST['partID']);
	$st -> execute();
}

if($_CONFIG_PDFDOWNLOAD){
	$dirname = '../data/datasheets/';
	$filename = $_POST['partname'].'.pdf';
	
	$file = fopen($_POST['url'], "rb");
	if (!$file) return false;
	else {
		$fc = fopen($dirname."$filename", "wb");
		while (!feof ($file)) {
			$line = fread($file, 1028);
			fwrite($fc,$line);
		}
		fclose($fc);
	}
}
?>
