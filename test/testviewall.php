<?php
require_once '../user/class.user.php';
require_once '../test/testclass.php';
session_start();
$user_login = new USER();
$event = new Test();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}else{
	$createbyid=$_SESSION['userSession'];
}


$a= $event->viewtest($user_login);
foreach ($a as $row) {
	if ($row['tastetablevalue'] != NULL){
		echo $row['tasttableid']." ".$row['tastetablevalue'];	
	echo "<br />";
	}
	
}

$testid = 5;
$row= $event->viewSpecific($user_login,$testid);
echo $row['tasttableid']." ".$row['tastetablevalue'];	
