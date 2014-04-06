<?php
if( isset($_POST['dlID']) ){
	
	include_once('../data/config.php');
	if(is_numeric($_POST['dlID'])) $ID = $_POST['dlID'];
	else exit('An error occurred :(');
	
	if($_POST['type'] == 'datasheet'){
		
		$sql = "SELECT MAX(ID) FROM parts";
		$maxID = $db -> query($sql);
		$maxID = $maxID -> fetchColumn();
		
		$sql = "SELECT name, datasheeturl FROM parts WHERE ID = $ID";
		$results = $db -> query($sql);
		$row_results = $results -> fetch(PDO::FETCH_ASSOC);
		
		while(count($row_results['name']) == 0){	
			if ($ID < $maxID) $ID++;
			else exit('0');
			$sql = "SELECT name, datasheeturl FROM parts WHERE ID = $ID";
			$results = $db -> query($sql);
			$row_results = $results -> fetch(PDO::FETCH_ASSOC);		
		}
		
		$dirname = '../data/datasheets/';
		$filename = $row_results['name'].'.pdf';
		if(!file_exists($dirname.$filename) && $row_results['datasheeturl'] != ''){			
			if (fopen($row_results['datasheeturl'], "rb") && !strpos($http_response_header[0], '404')){
				$file = fopen($row_results['datasheeturl'], "rb");
				$fc = fopen($dirname."$filename", "wb");
				while (!feof ($file)) {
					$line = fread($file, 1028);
					fwrite($fc,$line);
				}
				fclose($fc);
			}
		}
	
		$ID++;
		echo $ID;
	}
	elseif($_POST['type'] == 'appnote'){
		
		$sql = "SELECT MAX(ID) FROM appnotes"; //retrieve max part ID in the DB
		$maxID = $db -> query($sql);
		$maxID = $maxID -> fetchColumn();
		
		$sql = "SELECT ID, name, url FROM appnotes WHERE ID = $ID";
		$results = $db -> query($sql);
		$row_results = $results -> fetch(PDO::FETCH_ASSOC);
		
		while(count($row_results['name']) == 0){	//if this part ID does not exist in the DB...			
			if ($ID < $maxID) $ID++;
			else exit('0');
			$sql = "SELECT ID, name, url FROM appnotes WHERE ID = $ID";
			$results = $db -> query($sql);
			$row_results = $results -> fetch(PDO::FETCH_ASSOC);		
		}		
		
		$dirname = '../data/appnotes/';

		$filename = "$row_results[ID]_$row_results[name].pdf";
		if(!file_exists($dirname.$filename) && $row_results['url'] != ''){			
			if (fopen($row_results['url'], "rb") && !strpos($http_response_header[0], '404')){
				$file = fopen($row_results['url'], "rb");
				$fc = fopen($dirname."$filename", "wb");
				while (!feof ($file)) {
					$line = fread($file, 1028);
					fwrite($fc,$line);
				}
				fclose($fc);
			}
		}	
		$ID++;
		echo $ID;
	}
}
?>