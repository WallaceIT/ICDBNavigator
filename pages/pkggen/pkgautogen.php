<?php
include_once('../../data/config.php');
$packagefolder = "../../data/packages/";
echo '<b>Package files auto generation</b>';
include 'dipgen.php';
include 'soicgen.php';
include 'ssopgen.php';
include 'qfpgen.php';
include 'to92gen.php';
include 'to220gen.php';
include 'othergen.php';
?>