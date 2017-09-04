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

$testname = 'test';
$a= $event->insertTest($user_login,$testname);
	echo $a;
