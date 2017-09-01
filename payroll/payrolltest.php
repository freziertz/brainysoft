<?php
//use Brainysoft\Payroll;

require_once '../user/class.user.php';
//require_once '../payroll/Payroll.php';
session_start();
$user_login = new USER();
//$salarydsc = new Payroll();


$grossSalary = 1000000;

$d=$cutDate = date('Y-12-31');



echo $d;

$a = 0;




//$payee= $salarydsc->payee($grossSalary,$user_login);
//echo "Payee for ".$grossSalary." is ".$payee;
