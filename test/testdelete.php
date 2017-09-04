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


$testid = 1;
$a= $event->deletetest($user_login,$testid);
	echo $a;
