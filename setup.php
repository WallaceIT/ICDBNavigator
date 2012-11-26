<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>ICDB Navigator - SETUP</title>
	<link rel="icon" type="image/ico" href="favicon.ico">
	<link rel="stylesheet" href="css/colorbox.css" />
	<script src="js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.colorbox-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.qtip.min.js" type="text/javascript" charset="utf-8"></script>	
	<script type="text/javascript" charset="utf-8">
	window.onload = function() {

		$('#tdMySQL').hide();
		
		$('.radio_setupDB_Type').change(function(){
			if ($('#radio_setupDB_Type_MySQL').is(':checked')) $('#tdMySQL').slideDown();
			else $('#tdMySQL').slideUp();
		});

	};
	</script>
	<style type="text/css">
		body {
	    background: url(../../images/metal.gif) repeat;
	    color: #000;
		}
		div#setupcontainer{
			width: 550px;
			border: dashed 1px #000000;
			margin: 30px auto;
			padding: 20px;
		}
		table#dbtable{
		width: 100%;
		border: 0px;
		margin: 0 auto;
		text-align: left;
		}
		table#dbtable td{
		padding: 10px;
		}
		div.center{
		text-align: center;
		}
	</style>
</head>
<body>
	<div id="setupcontainer">
<?php
if(!isset($_POST['gosetup'])){?>
	
		<div class="center"><p>ICDBNavigator SETUP</p></div>
		<hr>
		<form id="setupForm" action="setup.php" method="post">			
			<table id="dbtable">
				<tr>
					<td><b>Select DB type:</b></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="radio" class="radio_setupDB_Type" id="radio_setupDB_Type_MySQL" name="setupDB_Type" value="mysql"> MySQL</td>
					<td>
						<div id="tdMySQL">
							<input type="text" size="30" name="setupDB_MySQLHost" placeholder="MySQL Hostname"><br>
							<input type="text" size="30" name="setupDB_MySQLDBName" placeholder="MySQL Database Name"><br>
							<input type="text" size="30" name="setupDB_MySQLUser" placeholder="MySQL User"><br>
							<input type="password" size="30" name="setupDB_MySQLPassword" placeholder="MySQL Password">
						</div>
					</td>
				</tr>
				<tr>
					<td><input type="radio" class="radio_setupDB_Type" name="setupDB_Type" value="sqlite" checked> SQLite</td>
					<td></td>
				</tr>				
			</table>
			<hr><br>
			<b>Path to ICDBNavigator root folder (in the filesystem - I'll try to estimate it):</b><br>
			<input type="text" size="60" name="setupRootPath" value="<?php echo $_SERVER['DOCUMENT_ROOT'];?>"><br>
			<br><hr><br>
			<b>Other Options:</b>
			<br><br>
			<input type="checkbox" name="setupDownloadPdf" value="true"> Download PDF file of Datasheets and Application Notes<br>
			<br><hr><br>
			<input type="hidden" name="gosetup" value="TRUE">
			<div class="center"><input type="submit" value="Install!"></div>
		</form>
	
<?php ;}
else{
	/*	SETUP:
	 *  - creates data directory
	 *  - creates config.php file  
	 *  - creates new database and setup it
	 *  - create data subdirectories...
	 *  - creates default packages
	 *  - all DONE! :) 
	 * 
	 */

	// creating data directory...
	echo 'creating data directories...';
	//if(is_dir('data/pindescs')) exit(' data directories already exists, aborting!');
	if(!is_writable('.')) exit('ICDBN root not writable, please make manually a writable \'data\' directory into it and re-run this script!');
	
	if(!is_dir('data')) mkdir('data');
	
	echo '[OK]<br><br>';
	
	// writing config.php file...
	echo 'writing config.php file...';
	$file_contents = '<?php'.PHP_EOL;
	$file_contents .= "// path to ICDBNavigator root folder (in the filesystem)".PHP_EOL;
	$file_contents .= '$_CONFIG_ICDB_ROOT = "'.$_POST['setupRootPath'].'";'.PHP_EOL;
	$file_contents .= '// DB connection'.PHP_EOL;
	if($_POST['setupDB_Type'] == 'sqlite') $file_contents .= '$db = new PDO("sqlite:$_CONFIG_ICDB_ROOT/data/icdb.sqlite");'.PHP_EOL.'$_CONFIG_DB_USE_SQLITE = TRUE;'.PHP_EOL;
	elseif($_POST['setupDB_Type'] == 'mysql'){
		$file_contents .= '$db = new PDO(
			\'mysql:host='.$_POST['setupDB_MySQLHost'].';dbname='.$_POST['setupDB_MySQLDBName'].'\',
			\''.$_POST['setupDB_MySQLUser'].'\',
			\''.$_POST['setupDB_MySQLPassword'].'\',
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		);'.PHP_EOL.'$_CONFIG_DB_USE_SQLITE = FALSE;'.PHP_EOL;
	}
	$file_contents .= '// config'.PHP_EOL.PHP_EOL;
	$file_contents .= '/* $_CONFIG_PDFDOWNLOAD: TRUE to download datasheets and appnotes and save their URLs in the DB, FALSE to only save URLs in the DB*/'.PHP_EOL;
	
	if(isset($_POST['setupDownloadPdf'])) $file_contents .= '$_CONFIG_PDFDOWNLOAD = TRUE;'.PHP_EOL;
	else $file_contents .= '$_CONFIG_PDFDOWNLOAD = FALSE;'.PHP_EOL;
	$file_contents .= '?>';
	
	file_put_contents('data/config.php', $file_contents);
	
	echo '[OK]<br><br>';
	
	// creating new database...
	echo 'creating new database...';
	include ('data/config.php');
	
	if(!$db) exit('<b>[FAILED!]</b>');
	
	// table 'appnotes'
	if($_CONFIG_DB_USE_SQLITE) $sql = 'CREATE TABLE "appnotes" ("ID" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "name" TEXT NOT NULL , "description" TEXT NOT NULL , "url" TEXT)';
	else $sql = 'CREATE TABLE appnotes (ID INTEGER PRIMARY KEY  AUTO_INCREMENT  NOT NULL  UNIQUE , name TEXT NOT NULL , description TEXT NOT NULL , url TEXT)';
	$st = $db -> prepare($sql);
	$st -> execute();
	
	// table 'categories'
	if($_CONFIG_DB_USE_SQLITE) $sql = 'CREATE TABLE "categories" ("category" TEXT NOT NULL  UNIQUE )';
	else $sql = 'CREATE TABLE categories (ID INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE, category TEXT NOT NULL)';
	$st = $db -> prepare($sql);
	$st -> execute();
	$sql = "INSERT INTO categories (category) VALUES ('Miscellaneous')";
	$st = $db -> prepare($sql);
	$st -> execute();
	
	// table 'manufacturers'
	if($_CONFIG_DB_USE_SQLITE) $sql = 'CREATE TABLE "manufacturers" ("name" TEXT NOT NULL  UNIQUE , "website" TEXT)';
	else $sql = 'CREATE TABLE manufacturers (ID INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE, name TEXT NOT NULL, website TEXT)';
	$st = $db -> prepare($sql);
	$st -> execute();
	$sql = "INSERT INTO manufacturers (name) VALUES ('Various')";
	$st = $db -> prepare($sql);
	$st -> execute();
	
	// table 'packages'
	if($_CONFIG_DB_USE_SQLITE) $sql = 'CREATE TABLE "packages" ("pkgname" TEXT NOT NULL ,"pinsnum" INTEGER NOT NULL )';
	else $sql = 'CREATE TABLE packages (ID INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE, pkgname TEXT NOT NULL, pinsnum INTEGER NOT NULL )';
	$st = $db -> prepare($sql);
	$st -> execute();
	
	// table 'parts'
	if($_CONFIG_DB_USE_SQLITE) $sql = 'CREATE TABLE "parts" ("ID" INTEGER PRIMARY KEY  NOT NULL ,"name" TEXT NOT NULL ,"description" TEXT NOT NULL ,"manufacturer" TEXT NOT NULL ,"category" TEXT NOT NULL ,"appnotes" TEXT,"datasheeturl" TEXT,"package" TEXT NOT NULL ,"quantity" INTEGER NOT NULL ,"summary" TEXT)';
	else $sql = 'CREATE TABLE parts (ID INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE, name TEXT NOT NULL ,description TEXT NOT NULL ,manufacturer TEXT NOT NULL ,category TEXT NOT NULL ,appnotes TEXT,datasheeturl TEXT,package TEXT NOT NULL ,quantity INTEGER NOT NULL ,summary TEXT)';
	$st = $db -> prepare($sql);
	$st -> execute();
	
	echo '[OK]<br><br>';
	
	// creating data subdirectories...
	echo 'creating data subdirectories...';
	mkdir('data/appnotes');
	mkdir('data/datasheets');
	mkdir('data/logos');
	mkdir('data/packages');
	mkdir('data/pindescs');
	
	echo '[OK]<br><br>';
	
	// creating default packages...
	echo 'creating default packages...';
	$packagefolder = "data/packages/";
	include 'pages/pkggen/dipgen.php';
	include 'pages/pkggen/soicgen.php';
	include 'pages/pkggen/ssopgen.php';
	include 'pages/pkggen/qfpgen.php';
	include 'pages/pkggen/to92gen.php';
	include 'pages/pkggen/to220gen.php';
	include 'pages/pkggen/sot23gen.php';
	include 'pages/pkggen/othergen.php';
	
	echo '[OK]<br><br>';

	echo '<div class="center"><b>ICDBNavigator Installed!</b><br>(Please remove this file...)</div>';
	echo '<br><div class="center"><a href="index.html">Go to ICDBN Homepage</a></div>';
	
}
?>
	</div>
</body>
</html>