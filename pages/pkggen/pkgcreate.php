<?php
require('../../data/config.php');
$file_contents = "// ICDBN - package $_POST[name]".PHP_EOL;
$file_contents .= "function drawpkg_$_POST[name](paper, partname, scalefactor){".PHP_EOL;
$file_contents .= "$_POST[code]".PHP_EOL;
$file_contents .= "}";

$filename = "../../data/packages/$_POST[name].js";
if(!file_exists($filename)){
	$st = $db -> prepare("INSERT INTO packages ('pkgname',pinsnum) VALUES (?,?)");
	$st->bindParam(1, $_POST['name']);
	$st->bindParam(2, $_POST['pinsnum']);
	$st -> execute();
	file_put_contents($filename, $file_contents);	
	echo "<br><br>Package $_POST[name] added!<br><br><br>";
	echo '<input type="button" class="OkButton" value="Close">';
}
else {
	echo "<br><br>Package $_POST[name] already exists!<br><br>";
	echo '<input type="button" class="OkButton" value="Close">';
}
?>