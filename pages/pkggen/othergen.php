<?php
$file = $packagefolder."other.js";
if (file_exists($file)) unlink($file) ;
$file_data = "
// ICDBN - package 'other' (no package drawing available)
function drawpkg_other(paper, partname, scalefactor){

var name = paper.text(115, 300, 'NO PACKAGE DRAWING')
	.attr({fill: '#bfbfbf',
	'font-size': 40})	
	.rotate(270);
}
";
file_put_contents($file, $file_data);

echo '<p>Package file generated for <b>OTHER</b></p>';
?>





