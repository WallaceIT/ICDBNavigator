<?php
include_once('../data/config.php');
if(isset($_POST['pin1'])){
	$partfile = "../data/pindescs/".$_POST['partname'].".xml";	
	
	$xml = simplexml_load_file($partfile);
	$pkg = $xml -> xpath("/part/pkg[@type = '$_POST[toeditpkg]']");
	if($pkg){
		for($i=1; $i<=$_POST['pinsnum'];$i++){
			$currentpin = 'pin'.$i;
			$pinXML = $xml -> xpath("/part/pkg[@type = '$_POST[toeditpkg]']/$currentpin");
			if ($_POST['pin'.$i] != '') $pinXML[0]['functions'] = $_POST['pin'.$i];
			else $pinXML[0]['functions'] = 'NC';
		}
	}
	else {
	$newpkg = $xml -> addChild('pkg');
	$newpkg  -> addAttribute('type', $_POST['toeditpkg']);
	for($i=1; $i<=$_POST['pinsnum'];$i++){
			$currentpin = 'pin'.$i;
			$currentpin = $newpkg -> addChild($currentpin);
			if ($_POST['pin'.$i] != '') $currentpin -> addAttribute('functions', $_POST['pin'.$i]);
			else $currentpin -> addAttribute('functions', 'NC');
		}
	}
	
	file_put_contents($partfile, $xml -> asXML());
	?>
			Package <b><?php echo $_POST['toeditpkg'];?></b> edited!
			<br>
			<input type="button" class="RefreshButton" value="Ok">
	<?php ;}
elseif (isset($_POST['toeditpkg'])){
	$query = "SELECT pinsnum FROM packages WHERE pkgname = '".$_POST['toeditpkg']."'";
	$pinsnum = $db -> query($query);
	$pinsnum = $pinsnum -> fetchColumn();
	$oldvalues = FALSE;
	$partfile = "../data/pindescs/".$_POST['partname'].".xml";	
	$xml = simplexml_load_file($partfile);
	if(current($xml -> xpath("/part/pkg[@type = '$_POST[toeditpkg]']/pin1/@functions"))) $oldvalues = TRUE;
	?>
		<form id="pineditForm">
			<input type="hidden" name="partname" value="<?php echo $_POST['partname']; ?>">
			<input type="hidden" name="toeditpkg" value="<?php echo $_POST['toeditpkg']; ?>">
			<input type="hidden" name="pinsnum" value="<?php echo $pinsnum; ?>">
			<input type="hidden" name="insert" value="1">
			
		</form>
		<div> <!-- required for jQuery find() to work properly -->
		<div id="popup_middle_content">
			<form id="pineditForm">
				<div id="pinpopulate_list">
				<i>Leave blank if Not Connected (NC)</i><br>
				<?php
					if($oldvalues){
					for($i=1; $i<=$pinsnum;$i++){
							$currentpin = 'pin'.$i;
							$pinfunc = current($xml -> xpath("/part/pkg[@type = '$_POST[toeditpkg]']/$currentpin/@functions"));
							echo 'PIN '.$i.': <input name="'.$currentpin.'" class="pins" type="text" size="40" value="'.$pinfunc.'" ><br>';
						}
					}
					else{
						for($i=1; $i<=$pinsnum;$i++){
							echo 'PIN '.$i.': <input name="pin'.$i.'" class="pins" type="text" size="60"><br>';
						};
					}
				?>
				<input type="hidden" name="partID" value="<?php echo $_POST['partID']; ?>">
				<input type="hidden" name="partname" value="<?php echo $_POST['partname']; ?>">
				<input type="hidden" name="toeditpkg" value="<?php echo $_POST['toeditpkg']; ?>">
				<input type="hidden" name="pinsnum" value="<?php echo $pinsnum; ?>">
				<input type="hidden" name="insert" value="1">
				</div>
			</form>
		</div>						
		<div id="popup_footer_content">
			<input type="button" class="OkButton" value="Cancel">
			<input type="button" id="editpkgButton" value="Edit!">
			<br><br>
			<form id="removepkgFirstForm">
				<input type="hidden" name="partID" value="<?php echo $_POST['partID']; ?>">
				<input type="hidden" name="partname" value="<?php echo $_POST['partname']; ?>">
				<input type="hidden" name="toremovepkg" value="<?php echo $_POST['toeditpkg']; ?>">
				<input type="submit" id="removepkgButton" value="Remove Package">
			</form>
		</div>
		</div> <!-- required for jQuery find() to work properly -->
<?php ;}
else{	//edit package selection mask

	$sqlquery = "SELECT * FROM parts WHERE ID =".$_GET['partID'];
	$part = $db -> query($sqlquery);
	$row_part = $part -> fetch(PDO::FETCH_ASSOC);
	?>
		<div id="popup_header">
			<form action="#" id="editpkgForm">
				Select the Package to edit: 
				<?php if ($row_part['package'] != 'other@'){?>
				<select name="toeditpkg">
					<?php
					$pkgs = preg_split("/;/", $row_part['package'],-1,PREG_SPLIT_NO_EMPTY);
					for($i=0; $i<count($pkgs); $i++){
						$pkgs[$i] = preg_split("/:/", $pkgs[$i],-1,PREG_SPLIT_NO_EMPTY);
						if($pkgs[$i][0] != 'other'){echo '<option value="'.$pkgs[$i][0].'">'.$pkgs[$i][0].'</option>';}
					};
				?>
				</select>
			<?php ;}?>
			<input type="hidden" name="partID" value="<?php echo $_GET['partID']?>">
			<input type="hidden" name="partname" value="<?php echo $_GET['partname']?>">
			<input type="submit" value="Select!">
			</form>
		</div>
		<div id="popup_middle" class="hide">
		</div>
		<div id="popup_footer">
			<input type="button" class="OkButton" value="Cancel">
		</div>
<?php ;}
?>