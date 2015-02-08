<?php
    require_once('../data/config.php');

    $user = hash('sha224', $_POST['username']);
    $password = hash('sha224', $_POST['password']);

    $sqlquery = "SELECT * FROM login WHERE username = '$user'";
	$results = $db -> query($sqlquery);
	$row_results = $results -> fetch(PDO::FETCH_ASSOC);

    if(isset($row_results['username']) && $row_results['password'] == $password){
        session_start();
        $_SESSION['logged'] = 'OK';
    }
    header('Location: ../index.php');
?>
