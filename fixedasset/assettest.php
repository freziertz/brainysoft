<?php
use Brainysoft\FixedAsset;

require_once '../user/class.user.php';
require_once '../fixedasset/FixedAsset.php';
session_start();
$user_login = new USER();
$accdepreciation = new FixedAsset();



$assetid = 1;

$date1 = '2017-6-30';

/* $newDate = $accdepreciation->oneMonthAdd($date1);
echo $newDate;

$in_dateHigh= '28-01-2014';

echo "<br />";

$dateHigh = strtotime('+1 month', $in_dateHigh);
echo $dateHigh; echo "<br />";
$date1= strtotime( $date1);
echo $date1;

//$dyl= date_format ( $dateLow,"Y" );
//$dml= date_format ( $dateLow,"m" );

$newDate = $accdepreciation->oneMonthAdd($date);
echo $newDate;


echo "<br />";
echo date('Y-m',$date1);
$dateLow = $dyl.$dml."01";
echo "<br />";
echo date('Y-m-d',$dateHigh); */
// $dy= date_format ( $date1,"Y" );
// $dm= date_format ( $date1,"m" );
// $dd= date_format ( $date1,"d" );

// $t=time($date1);
// echo($t . "<br>");
// echo (date("Y-m-d",$t));

// echo( "  <br>");

// echo strftime('%m/%Y', $date2);


/* echo( "  <br>");
 * 
 

echo $dy." <br />";
echo strtotime($date2), "\n";
echo $dm." <br />";
echo $dd." <br />";
echo $dy.$dm;
echo strftime('%Y/%m', $date2);

 */
 $accumulatedDepreciation = $accdepreciation->fixedLineDepreciation($assetid,$user_login,$date1);
foreach ($accumulatedDepreciation as $v){
	echo "Depreciation for ".$assetid." is ".$v;
	echo "<br />";
} 

 