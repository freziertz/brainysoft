<?php

function fineComputation(){
	
	require_once '../user/class.user.php';
$user_login = new USER();
/* if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
} */

//calculate fine for each loan
	// Find next run date
function nextRunDate($toDay,$i){
	$duration = $i . " days";
	$toDay = date_create ( $toDay );
	$nextDate= date_add ( $toDay, date_interval_create_from_date_string ( $duration ) );
	return date_format ( $nextDate, "Y-m-d" );
}
try {	
	//Begin transaction
	$user_login->beginTransaction();
			// Get loanid,loanbalance from loan table
			$stmtLoanBalance = $user_login->runQuery( "SELECT loanid, enddate,loantypeid, loanbalance,totalfine FROM loan WHERE loanstatus = 5" );
			$stmtLoanBalance->execute ();
			$result = $stmtLoanBalance->fetchAll();
			
			foreach ($result as $row ){
				$loanBalance = $row ['loanbalance'];
				$totalFine = $row ['totalfine'];
				$loanId = $row ['loanid'];
				$loanEndDate = $row ['enddate'];
				$loanTypeId = $row ['loantypeid'];					
				
				// Get last fine run date
				$stmtMaxRunDate = $user_login->runQuery ( "SELECT loanid,nextrundate, Max(lastrundate) as lastrundate	FROM loanfine WHERE loanid =:loanid" );
				$stmtMaxRunDate->execute ( array (":loanid" => $loanId));
				$rowMaxRunDate = $stmtMaxRunDate->fetch ( PDO::FETCH_ASSOC );
				$rowMaxRunDate ['lastrundate'];
				$lastRunDate =  date_format(date_create($rowMaxRunDate ['lastrundate']),'Y-m-d');
				//$nexRunDate =  date_format(date_create($row ['nextrundate']),'Y-m-d');
			
				//Get loan fine rate			
				$stmtFineRate = $user_login->runQuery ( "SELECT loantypeid, loanfinerate FROM loantype WHERE loantypeid =:loantypeid" );
				$stmtFineRate->execute ( array (":loantypeid" => $loanTypeId));
				$rowFineRate = $stmtFineRate->fetch ( PDO::FETCH_ASSOC );
				$loanFineRate = $rowFineRate ['loanfinerate'];
				$loanTypeId = $rowFineRate ['loantypeid'];
				
				//Get today date				
				$toDay =date_format(date_create(),'Y-m-d');
				
				//If there is loan fine of fine already run on previous days
				//Calculate fine for each day from last run
				if ($totalFine > 0){ //if the fine already run at least then total fine should be greater than zero
					if (($loanBalance > 0)&& ($loanEndDate < $toDay)){					
						if ($lastRunDate < $toDay){	
							$stmtDateDiff = $user_login->runQuery ( "SELECT DATEDIFF( ?, ? ) AS DiffDate" );
							$stmtDateDiff->execute ( array ($toDay,$lastRunDate ) );
							$rowDateDiff = $stmtDateDiff->fetch ( PDO::FETCH_ASSOC );
							$numberOfDay =  $rowDateDiff ['DiffDate'];
							
							for ( $i = 1; $i <= $numberOfDay; $i++ ){
								$fineAmount = ($loanBalance * $loanFineRate);					
								$totalFine = $totalFine + $fineAmount;				
								$nextRundate = nextRunDate($lastRunDate,$i+1);
								$lastRundate = nextRunDate($lastRunDate,$i);				
								$nextRunTime= 0;
								
								
								
								//insert fine history
								$insertFineHistory= $user_login->runQuery("INSERT INTO loanfine (loanid,fineamount,lastrundate,nextrundate,nextruntime) VALUES (?,?,?,?,?)");
								$insertFineHistory->execute(array($loanId,$fineAmount,$lastRundate,$nextRundate,$nextRunTime));
								
								// Update total fine for each run
								$updateTotalFine = $user_login->runQuery ( "UPDATE loan  SET totalfine= ? WHERE loanid = ?" );
								$updateTotalFine->execute ( array ($totalFine,$loanId) );
							}						
						}
					}			
				}else{ // If loan does not have fine or fine run for the first time
					if (($loanBalance > 0)&& ($loanEndDate < $toDay)){				
					$stmtDateDiffe =$user_login->runQuery ( "SELECT DATEDIFF( ?, ? ) AS DiffDate" );
					$stmtDateDiffe->execute ( array ($toDay,$loanEndDate ) );
					$rowDateDiffe = $stmtDateDiffe->fetch ( PDO::FETCH_ASSOC );
					$numberOfDay =  $rowDateDiffe ['DiffDate'];	
						
						//Calculate fine for each day
						for ( $i = 1; $i <= $numberOfDay; $i++ ){
							$fineAmount = ($loanBalance * $loanFineRate);
							$totalFine = $totalFine + $fineAmount;					
							//Find last and next run date
							$nextRundate = nextRunDate($loanEndDate,$i+1);
							$lastRundate = nextRunDate($loanEndDate,$i);				
							$nextRunTime= 0;							
							//insert fine history
							$insertFineHistory= $user_login->runQuery("INSERT INTO loanfine (loanid,fineamount,lastrundate,nextrundate,nextruntime) VALUES (?,?,?,?,?)");
							$insertFineHistory->execute(array($loanId,$fineAmount,$lastRundate,$nextRundate,$nextRunTime));
							}}				
							// Update total fine on loan table
							$updateTotalFine = $user_login->runQuery ( "UPDATE loan  SET totalfine= ? WHERE loanid = ?" );
							$updateTotalFine->execute ( array ($totalFine,$loanId) );			
				}				
			//End of for each loan
			}	
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
}	


?>