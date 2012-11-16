<?php
$file = '../'.$_GET['url'];
$filename = $_GET['part'].'.pdf'; /* Note: Always use .pdf at the end. */

header('Content-type: application/pdf');
header('Content-Disposition: inline');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file));
header('Accept-Ranges: bytes');

@readfile($file);
?>