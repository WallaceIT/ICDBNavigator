<?php
session_start();
if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 'OK')
    return header('HTTP/1.0 401 Unauthorized');

if (!isset($_POST['goremove'])){?>
	<div id="popup_header">
		<form id="removepartForm">
			Are you sure you want to remove <b><?php echo $_POST['partname']?></b> from the DB?
			<br><br>
			<input type="submit" value="Remove">
			<input type="button" class="OkButton" value="Cancel">
			<input type="hidden" name="partname" value="<?php echo $_POST['partname']?>">
			<input type="hidden" name="partID" value="<?php echo $_POST['partID']?>">
			<input type="hidden" name="goremove" value="1">
		</form>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$( document ).on('submit', '#removepartForm', function(event){
				event.preventDefault();
				$.ajax({
					type: "POST",
					url: "pages/removepart.php",
					data: $(this).serialize(),
					dataType: "text",
					success: function(response){
						$.colorbox({html:response});
					}
				});
			});
		});
	</script>
<?php ;} else {
	include_once('../data/config.php');
	$st = $db -> prepare("DELETE FROM parts WHERE ID = ?");
	$st->bindParam(1, $_POST['partID']);
	$st -> execute();
	$partfile = "../data/pindescs/".$_POST['partname'].".xml";
	if (file_exists($partfile)) unlink($partfile);
	$datasheetfile = "../data/datasheets/".$_POST['partname'].".pdf";
	if (file_exists($datasheetfile)) unlink($datasheetfile);
	echo '<div id="popup_header"><b>'.$_POST['partname'].'</b> removed from the DB!<br><br>
			<form action="index.php"><input type="submit" value="Ok"></form></div>';
}
?>	