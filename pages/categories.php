<?php
include_once('../data/config.php');
$noresults = 1;
if($_POST['category'] == 'all'){
	$thead = '
	<div id="resultstitle">Browse Categories</div>
	<div id="results_inner">
		<table id="categoriestable">
			<colgroup>
				<col width="25%"/>
			</colgroup>
	';
	$sqlquery = "SELECT * FROM categories ORDER BY category ASC";
	$results = $db -> query($sqlquery);
	$row_results = $results -> fetch(PDO::FETCH_ASSOC);
	if(isset($row_results['category'])) {
		$noresults = 0;
		echo $thead;
		do {?>
			<tr class="clickme" cat="<?php echo $row_results['category']?>">
				<td><img src="images/category.png" border="0"/> - <?php echo $row_results['category']?> -</td>
			</tr>
	<?php ;} while ($row_results = $results -> fetch(PDO::FETCH_ASSOC));}
	if ($noresults) echo '<div id="noresults">No Results!</div>';
	echo '</table></div>';
}
else{
$thead = '
<div id="resultstitle">Category: <b>'.$_POST['category'].'</b></div>
<div id="results_inner">
	<table id="resultstable">
		<colgroup>
			<col width="25%"/>
			<col/>
		</colgroup>
';
$sql = "SELECT * FROM parts WHERE category = '".$_POST['category']."'";
$results = $db -> query($sql);
$row_results = $results -> fetch(PDO::FETCH_ASSOC);
if(isset($row_results['name'])) {
	$noresults = 0;
	echo $thead;
	do {?>
		<tr class="clickme" url="viewpart.php?ID=<?php echo $row_results['ID'] ?>">
			<td><img src="images/ic_22.png" border="0"/> <?php echo $row_results['name']?> [ <?php echo $row_results['quantity']?> ]</td>
			<td><?php echo $row_results['description']?></td>
		</tr>
<?php ;} while ($row_results = $results -> fetch(PDO::FETCH_ASSOC));}
if ($noresults) echo '<div id="noresults">No Results!</div>';
echo '</table></div>';}
?>