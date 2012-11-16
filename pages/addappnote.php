<?php
include_once('../data/config.php');
if (!isset($_POST['url']) && !isset($_POST['appnoteID'])){?>
<div id="popup_header">
	Add new Application Note from web URL...
	<form action="#" id="addappnoteForm">
		<input name="addappnoteName" type="text" size="10" placeholder="Name" required>
		<input name="addappnoteDesc" type="text" size="40" placeholder="Description..." required><br>
		<input name="addappnoteUrl" type="url" size="50" placeholder="URL..." required>
		<input type="submit" id="addappnoteButton" value="Add!">
	</form>
	<br>
	...or link an existing Application Note to this part:
	<br>
	<form action="#" id="linkappnoteForm">
		<select name="linkappnoteID">
			<option value=""></option>
		<?php 
		$sqlquery = "SELECT * FROM appnotes";
		$appnotes = $db -> query($sqlquery);
		$row_appnote = $appnotes -> fetch(PDO::FETCH_ASSOC);	
		if(isset($row_appnote['name'])){		
			do{
				echo '<option value="'.$row_appnote['ID'].'">'.$row_appnote['name'].' - '.$row_appnote['description'].'</option>';
			} while ($row_appnote = $appnotes -> fetch(PDO::FETCH_ASSOC))
		;}
		?>
		</select>
	<input type="submit" id="addappnoteButton" value="Link!">
	</form>
	<?php if (isset($_GET['needdatasheet'])){?>
	<br>
	<hr>
	<br>Add Datasheet to this part:
	<br>
	<form action="#" id="adddatasheetForm">
		<input name="adddatasheetUrl" type="url" size="50" placeholder="URL..." required>
		<input type="submit" id="adddatasheetButton" value="Add!">
	</form>
	<?php ;}
	elseif(!file_exists("../data/datasheets/$_GET[partname].pdf")){
	?>
	<br>
	<hr>
	Download Datasheet for this part: <input type="button" id="downloaddatasheetButton" value="Download">
	<br>
	<?php ;} ?>
	<br><input type="button" class="OkButton" value="Cancel">
</div>
<?php ;} else if (isset($_POST['url'])) {
	$st = $db -> prepare("SELECT ID FROM appnotes ORDER BY ID DESC LIMIT 0,1");
	$st -> execute();
	$ID = ($st -> fetchColumn())+1;

	if($_CONFIG_PDFDOWNLOAD){
		$url = $_POST['url'];
		$dirname = '../data/appnotes/';
		$filename = $ID.'_'.$_POST['name'].'.pdf';
		$file = fopen($url, "rb");
		if (!$file) {
			return false;
		}else {
			$fc = fopen($dirname."$filename", "wb");
			while (!feof ($file)) {
				$line = fread($file, 1028);
				fwrite($fc,$line);
			}
			fclose($fc);
		}
	}
	$st = $db -> prepare("INSERT INTO appnotes ('name','description', 'url') VALUES (?, ?, ?)");
	$st->bindParam(1, $_POST['name']);
	$st->bindParam(2, $_POST['desc']);
	$st->bindParam(3, $_POST['url']);
	$st -> execute();
	$st = $db -> prepare("UPDATE parts SET appnotes = appnotes || '@' || ? WHERE ID = ?");
	$st->bindParam(1, $ID);
	$st->bindParam(2, $_POST['partID']);
	$st -> execute();
		return true;
} else if ($_POST['appnoteID'] != '') {
	$st = $db -> prepare("UPDATE parts SET appnotes = appnotes || '@' || ? WHERE ID = ?");
	$st->bindParam(1, $_POST['appnoteID']);
	$st->bindParam(2, $_POST['partID']);
	$st -> execute();
	return true;}	
?>