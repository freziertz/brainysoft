<?php
require_once '../user/class.user.php';
include '../sms/sendsms.php';
session_start();
$user_login = new USER();


if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}else{
	$loginUser=$createbyid=$_SESSION['userSession'];
}

if (SendMessage('+255754307151', 'My text message now')){
	echo "message sent";
}else{
	echo "message not sent"; 
}