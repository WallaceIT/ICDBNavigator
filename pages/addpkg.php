<?php
include_once('../data/config.php');
if(isset($_POST['insert'])){
	//remove "other" from packages
	$sqlquery = "SELECT package FROM parts WHERE ID =".$_POST['partID'];
	$curpackages = $db -> query($sqlquery);
	$curpackages = $curpackages -> fetchColumn();
	if (preg_match("/other:[0-9]+;/", $curpackages)){
		if($_CONFIG_DB_USE_SQLITE) $sql = "UPDATE parts SET package = ? || ':0;' WHERE ID = ?";
		else $sql = "UPDATE parts SET package = CONCAT(?, ':0;') WHERE ID = ?";
	}
	else {
		if($_CONFIG_DB_USE_SQLITE) $sql = "UPDATE parts SET package = package || ? || ':0;' WHERE ID = ?";
		else $sql = "UPDATE parts SET package = CONCAT(package, ?, ':0;') WHERE ID = ?";		
	}
	$st = $db -> prepare($sql);
	$st->bindParam(1, $_POST['newpkg']);
	$st->bindParam(2, $_POST['partID']);
	$st -> execute();
	if(isset($_POST['pin1'])){
		
		$partfile = "../data/pindescs/".$_POST['partname'].".xml";
		$xml = simplexml_load_file($partfile);
		$newpkgXML = $xml -> addChild('pkg');
		$newpkgXML -> addAttribute('type', $_POST['newpkg']);
		for($i=1; $i<=$_POST['pinsnum'];$i++){
			$newpinXML = $newpkgXML -> addChild('pin'.$i);
			if ($_POST['pin'.$i] != '')	$newpinXML -> addAttribute('functions', $_POST['pin'.$i]); 
			else $newpinXML -> addAttribute('functions', 'NC');
		}		
		file_put_contents($partfile, $xml -> asXML());

	}
	?>
		Package <b><?php echo $_POST['newpkg'];?></b> added!
		<br>
		<input type="button" class="RefreshButton" value="Ok">
<?php ;}
elseif (isset($_POST['newpkg'])){
	$query = "SELECT pinsnum FROM packages WHERE pkgname = '".$_POST['newpkg']."'";
	$pinsnum = $db -> query($query);
	$pinsnum = $pinsnum -> fetchColumn();
	
	?>
		<div> <!-- required for jQuery find() to work properly -->
		<div id="popup_middle_content">
			<form id="pinpopulateForm">
				<div id="pinpopulate_list">
					Leave blank if Not Connected (NC)<br>
					<?php for($i=1; $i<=$pinsnum;$i++) echo '<input name="pin'.$i.'" class="pins" type="text" size="60" placeholder="pin '.$i.' functions..." > ';?>
					<input type="hidden" name="partID" value="<?php echo $_POST['partID']; ?>">
					<input type="hidden" name="partname" value="<?php echo $_POST['partname']; ?>">
					<input type="hidden" name="newpkg" value="<?php echo $_POST['newpkg']; ?>">
					<input type="hidden" name="pinsnum" value="<?php echo $pinsnum; ?>">
					<input type="hidden" name="insert" value="1">
				</div>
			</form>
		</div>						
		<div id="popup_footer_content">
				<input type="button" id="addpkgButton" value="Add!">
				<input type="button" class="OkButton" value="Cancel">
		</div>
		</div> <!-- required for jQuery find() to work properly -->
<?php ;}
else{	//new package selection mask
	$pkgfound = FALSE;
	$sqlquery = "SELECT * FROM packages ORDER BY pinsnum ASC";
	$packages = $db -> query($sqlquery);
	$sqlquery = "SELECT package FROM parts WHERE ID = ".$_GET['partID'];
	$curpackages = $db -> query($sqlquery);
	$curpackages = $curpackages -> fetchColumn();
	?>
	<div id="popup_header">
		<form action="#" id="addpkgForm">
			Select the Package to add: 
			<select name="newpkg">
					<?php while ($row_packages = $packages -> fetch(PDO::FETCH_ASSOC)){
						if(!preg_match("/".$row_packages['pkgname']."/", $curpackages)){  
							$pkgfound = TRUE;?>
					<option value="<?php echo $row_packages['pkgname'];?>"><?php echo $row_packages['pinsnum'].'pin - '.$row_packages['pkgname'];?></option>
				<?php ;};}?>
			</select>
			<?php if ($pkgfound) {?>
			<input type="submit" id="addpkgSubmit" value="Select!">
			<br>
			<input type="checkbox" name="skipbox" id="skipbox" value="1"> Skip pins description fill in
			<input type="hidden" name="partID" value="<?php echo $_GET['partID']?>">
			<input type="hidden" name="partname" value="<?php echo $_GET['partname']?>">
			<?php ;} else echo '<br><b>No other packages to add!</b>';?>
		</form>
	</div>
	<div id="popup_middle" class="hide">
	</div>
	<div id="popup_footer">
		<input type="button" class="OkButton" value="Cancel">
	</div>
<?php ;}
?>