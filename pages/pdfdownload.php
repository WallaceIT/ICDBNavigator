<?php
include_once('../data/config.php');
$sqlquery = "SELECT name, datasheeturl FROM parts";
$results = $db -> query($sqlquery);
$row_results = $results -> fetch(PDO::FETCH_ASSOC);
$dirname = '../data/datasheets/';
echo '<b>Dowloading Datasheets...</b><br>';
do{
	$filename = $row_results['name'].'.pdf';
	if(!file_exists($dirname.$filename) && $row_results['datasheeturl'] != ''){
		echo $filename.' downloaded!<br>';
		$file = fopen($row_results['datasheeturl'], "rb");
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
	elseif (file_exists($dirname.$filename)) echo $filename.' already exists!<br>';
	else echo 'No '.$row_results['name'].' Datasheet in DB!<br>';
}
while($row_results = $results -> fetch(PDO::FETCH_ASSOC));

$sqlquery = "SELECT ID,name, url FROM appnotes";
$results = $db -> query($sqlquery);
$row_results = $results -> fetch(PDO::FETCH_ASSOC);
$dirname = '../data/appnotes/';
echo '<b>Dowloading Application Notes...</b><br>';
do{
	$filename = "$row_results[ID]_$row_results[name].pdf";
	if(!file_exists($dirname.$filename)){
		echo $filename.' downloaded!<br>';
		$file = fopen($row_results['url'], "rb");
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
	else echo $filename.' already exists!<br>';
}
while($row_results = $results -> fetch(PDO::FETCH_ASSOC));
?>