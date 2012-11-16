<?php
$filename = $_POST['deletebackupfile'];
if(file_exists("../".$filename)) unlink("../".$filename);
?>