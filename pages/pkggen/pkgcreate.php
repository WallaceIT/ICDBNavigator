<?php
$file_contents = "// ICDBN - package $_POST[code]".PHP_EOL;
$file_contents .= "function drawpkg_$_POST[code](paper, partname, scalefactor){".PHP_EOL;
$file_contents .= "$_POST[code]".PHP_EOL;
$file_contents .= "}";

$filename = "../../data/packages/$_POST[name].js";
if(!file_exists($filename)){
	file_put_contents($filename, $file_contents);
	echo "<br><br>Package $_POST[name] added!<br><br><br>";
	echo '<input type="button" class="OkButton" value="Close">';
}
else {
	echo "<br><br>Package $_POST[name] already exists!<br><br>";
	echo '<input type="button" class="OkButton" value="Close">';
}
?>