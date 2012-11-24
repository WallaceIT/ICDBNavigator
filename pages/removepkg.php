<?php if (!isset($_POST['goremovepkg'])){?>
	<div id="popup_header" class="cAlign">
		<form id="removepkgForm">
			Are you sure you want to remove <b><?php echo $_POST['toremovepkg']?></b> from <b><?php echo $_POST['partname']?></b>?
			<br><br>
			<input type="submit" value="Remove">
			<input type="button" class="OkButton" value="Cancel">
			<input type="hidden" name="toremovepkg" value="<?php echo $_POST['toremovepkg']?>">
			<input type="hidden" name="partname" value="<?php echo $_POST['partname']?>">
			<input type="hidden" name="partID" value="<?php echo $_POST['partID']?>">
			<input type="hidden" name="goremovepkg" value="1">
		</form>
	</div>
	<script type="text/javascript">
	$(document).ready(function() {
			$( document ).on('submit', '#removepkgForm', function(event){
				event.preventDefault();
				$.ajax({
					type: "POST",
					url: "pages/removepkg.php",
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
	$st = $db -> prepare("SELECT package FROM parts WHERE ID = ?");
	$st->bindParam(1, $_POST['partID']);
	$st -> execute();
	$packages = ($st -> fetchColumn());
	$packages = preg_replace('/'.$_POST['toremovepkg'].':[0-9]+;/', '', $packages);
	$st = $db -> prepare("UPDATE parts SET package = ? WHERE ID = ?");
	$st->bindParam(1, $packages);
	$st->bindParam(2, $_POST['partID']);
	$st -> execute();
	$sqlquery = "SELECT package FROM parts WHERE ID =".$_POST['partID'];
	$packages = $db -> query($sqlquery);
	$packages = $packages -> fetchColumn();
	if ($packages == null){
		$st = $db -> prepare("UPDATE parts SET quantity = 0 WHERE ID = ?");
		$st->bindParam(1, $_POST['partID']);
		$st -> execute();
	};
	?>
	<div id="popup_header">
		<b><?php echo $_POST['toremovepkg'];?></b> removed from <b><?php echo $_POST['partname'];?></b>!
		<br><br>
		<input type="button" class="RefreshButton" value="Ok">
	</div>
<?php ;}
?>	