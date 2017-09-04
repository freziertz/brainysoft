<?php
require_once '../user/class.user.php';
$user_login = new USER();

$sqlSelectPerson = ("SELECT 	partyid,CONCAT(partyid,' ',firstname,' ',lastname)as fullname FROM person");
$smtpLoan = $user_login->runQuery ( $sqlSelectPerson );
$smtpLoan->execute ( array () );
foreach ( $smtpLoan->fetchAll () as $row ) {
	$values[] = array(
			'label' => $row ['fullname'],
			'value' => $row ['fullname'],
			'id' => $row ['partyid'],
	);
}
$data = json_encode($values);
echo $data.";";

?>