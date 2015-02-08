<?php
include_once('../data/config.php');
$noresults = 1;
if($_POST['category'] == 'all'){
	$thead = '
	<div id="resultstitle">Browse Categories</div>
	<div id="results_inner">
		<table id="resultstable">
	';
	$sqlquery = "SELECT * FROM categories WHERE category NOT LIKE '%@%' ORDER BY category ASC";
	$results = $db -> query($sqlquery);
	$row_results = $results -> fetch(PDO::FETCH_ASSOC);
	if(isset($row_results['category'])) {
		$noresults = 0;
		echo $thead;
		do {?>
			<tr class="clickme result_category" cat="<?php echo $row_results['category']?>">
				<td><img src="images/category.png" border="0"/> <b><?php echo $row_results['category']?></b></td>
			</tr>
	<?php ;} while ($row_results = $results -> fetch(PDO::FETCH_ASSOC));}
	if ($noresults) echo '<div id="noresults">No Results!</div>';
	echo '</table></div>';
}
else{
	$prev = explode('@', $_POST['category'], -1);
	if(count($prev) == 0) $prev = 'all';
	else $prev = implode('', $prev);
	echo '
	<div id="resultstitle">Category: <b>'.str_replace('@', ' > ', $_POST['category']).'</b></div>
	<div id="results_inner">
		<table id="resultstable">
			<colgroup>
				<col width="30%"/>
				<col/>
			</colgroup>
		<tr class="clickme result_category" cat="'.$prev.'">
					<td><img src="images/category.png" border="0"/> <b>..</b></td>
					<td></td>
		</tr>
	';
	
	$sqlquery = "SELECT * FROM categories WHERE category LIKE '$_POST[category]@%' ORDER BY category ASC";
	$results = $db -> query($sqlquery);
	$row_results = $results -> fetch(PDO::FETCH_ASSOC);
	if(isset($row_results['category'])) {
		do {
			$subcategory = explode('@', $row_results['category']);
			$subcategory = end($subcategory); 
			?>
			<tr class="clickme result_category" cat="<?php echo $row_results['category']?>">
				<td><img src="images/category.png" border="0"/> <b><?php echo $subcategory?></b></td>
				<td></td>
			</tr>
	<?php ;} while ($row_results = $results -> fetch(PDO::FETCH_ASSOC));}
	
	$sql = "SELECT * FROM parts WHERE category = '$_POST[category]'";
	$results = $db -> query($sql);
	$row_results = $results -> fetch(PDO::FETCH_ASSOC);
	if(isset($row_results['name'])) {
		do {?>
			<tr class="clickme result_part" url="viewpart.php?ID=<?php echo $row_results['ID'] ?>">
				<td><img src="images/ic_22.png" border="0"/> <?php echo $row_results['name']?> [ <?php echo $row_results['quantity']?> ]</td>
				<td><?php echo $row_results['description']?></td>
			</tr>
	<?php ;} while ($row_results = $results -> fetch(PDO::FETCH_ASSOC));}
	echo '</table></div>';
}
?>
