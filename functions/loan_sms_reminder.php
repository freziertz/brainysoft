<?php

function sendSmsReminder(){
	include '../sms/sendsms.php';
	
require_once '../user/class.user.php';
$user_login = new USER();
/* if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
} */

$sqlLoanSmsReminder = ("SELECT distinct loanid,phonenumber,partyid,phonetype,emailaddress,emailtype,paymentdate,loanbalance,totalfine,totaldue
FROM contactview WHERE
DAY(paymentdate)=DAY(CURDATE()) AND 
phonetype = 'mobile' "); 
$stmtLoanSmsReminder = $user_login->runQuery($sqlLoanSmsReminder);
$stmtLoanSmsReminder->execute ();
$resulSmsReminder = $stmtLoanSmsReminder->fetchAll();


foreach ($resulSmsReminder as $row ){
	$loanId = $row ['loanid'];
	$paymentDate = $row ['paymentdate'];
	$phoneNumber = $row ['phonenumber'];
	$emailAddress = $row ['emailaddress'];
	$loanBalance = $row ['loanbalance'];
	$partyId = $row ['partyid'];
	$loanBalance = $row ['loanbalance'];
	$totalFine = $row ['totalfine'];
	$totalDue = $row ['totaldue'];


	
	

//calculate fine for each loan
	// Find next run date

try {	
	//Begin transaction
	$user_login->beginTransaction();
		
								
								
								//insert fine history
								$insertFineHistory= $user_login->runQuery("INSERT INTO loanfine (loanid,fineamount,lastrundate,nextrundate,nextruntime) VALUES (?,?,?,?,?)");
								$insertFineHistory->execute(array($loanId,$fineAmount,$lastRundate,$nextRundate,$nextRunTime));
								
								// Update total fine for each run
								$updateTotalFine = $user_login->runQuery ( "UPDATE loan  SET totalfine= ? WHERE loanid = ?" );
								$updateTotalFine->execute ( array ($totalFine,$loanId) );
												
						
								
				
			//End of for each loan
				
	if ($user_login->commit()) {
		// log information to the fine run log
		echo '<script type="text/javascript"> alert("Fine Created Successfully.");</script>';
	}
	//end commit if
	} catch ( PDOException $e ) {
			$user_login->rollBack();
			//Log error msg
			print $e->getMessage ();
		}	
	
}}

?>