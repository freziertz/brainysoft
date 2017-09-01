<?php
require_once '../user/class.user.php';
require ('../inc/fileUploader.php');
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}

if (isset($_GET['id'])&& $_GET['aid'] && $_GET['done']){
	$loanid = $_GET['id'];
	$approvedby = $_GET['aid'];
	$approvedtypeid = $_GET['apt'];
	$workflowUserComments = "User comments";
	
	
	// Get loantypeid,loantyperate and other setting from loantype table
			$stmtLoanType = $user_login->runQuery ( "SELECT loantypeid FROM loan WHERE loanid = ?" );
			$stmtLoanType->execute ( array ($loanid) );			
			$rowLoanType = $stmtLoanType->fetch();			
			$loantypeid = $rowLoanType ['loantypeid'];				
			//End get loantypeid,loantyperate and other setting from loantype table
			
			
			// Get loantypeid,loantyperate and other setting from loantype table
			$stmtLoanType = $user_login->runQuery ( "SELECT loanworkflowid FROM loantype WHERE loantypeid = ?" );
			$stmtLoanType->execute ( array ($loantypeid) );
			$rowLoanType = $stmtLoanType->fetch();			
			$loanWorkflowId = $rowLoanType ['loanworkflowid'];
			
			//End get loantypeid,loantyperate and other setting from loantype table
			
			//Select current node
			
			$sqlNextNode= ("SELECT Max(workflownextnodeid) as workflownextnodeid,workflownodetypeid,workflowownerid FROM workflowhistory WHERE workflowid = ? AND workflowoitemid = ? ");
			$stmtNextNode = $user_login->runQuery($sqlNextNode);
			$stmtNextNode->execute(array($loanWorkflowId,$loanid));	
			$rowNextNode= $stmtNextNode->fetch();
			$currentNodeId = $rowNextNode['workflownextnodeid'];
			$workflowOwnerId = $rowNextNode['workflowownerid'];
			
			//Select properties of current node from workflow definition
	
			$sqlCurrentNodeProperties= ("SELECT workflownodeid,workflownodename, workflownodetypeid,workflownodecomments,workflownodeduration FROM workflownode WHERE workflowid = ? AND workflownodeid = ? ");
			$stmtCurrentNodeProperties = $user_login->runQuery($sqlCurrentNodeProperties);
			$stmtCurrentNodeProperties->execute(array($loanWorkflowId,$currentNodeId));	
			$rowCurrentNodeProperties= $stmtCurrentNodeProperties->fetch();
			$currentNodeId = $rowCurrentNodeProperties['workflownodeid'];
			$currentNodeName = $rowCurrentNodeProperties['workflownodename'];
			$currentNodeTypeId = $rowCurrentNodeProperties['workflownodetypeid'];
			$currentNodeComments = $rowCurrentNodeProperties['workflownodecomments'];
			$currentNodeDuration = $rowCurrentNodeProperties['workflownodeduration'];
			
			
			
			
			if ($currentNodeTypeId == 6) {
				$loanStatus = 5 ;
				$workflowStatus = 1; //completed
				$nextNodeId = -1;
				$nextNodeName = -1;
				$nextNodeTypeId = -1;
				$nextNodeComments = -1;
				$nextNodeDuration = -1;
				$workflowUserComments = $workflowUserComments;
				
				
				
			}else {
				$loanStatus = 1 ;
				$workflowStatus = 0; //active while -1 terminated
				$workflowUserComments = $workflowUserComments;
				
				//Update workflowhistory 
				$nextNodeId = $currentNodeId + 1;
				$sqlNextNodeProperties= ("SELECT workflownodeid,workflownodename, workflownodetypeid,workflownodecomments,workflownodeduration FROM workflownode WHERE workflowid = ? AND workflownodeid = ? ");
				$stmtNextNodeProperties = $user_login->runQuery($sqlNextNodeProperties);
				$stmtNextNodeProperties->execute(array($loanWorkflowId,$nextNodeId));	
				$rowNextNodeProperties= $stmtNextNodeProperties->fetch();
				$nextNodeId = $rowNextNodeProperties['workflownodeid'];
				$nextNodeName = $rowNextNodeProperties['workflownodename'];
				$nextNodeTypeId = $rowNextNodeProperties['workflownodetypeid'];
				$nextNodeComments = $rowNextNodeProperties['workflownodecomments'];
				$nextNodeDuration = $rowNextNodeProperties['workflownodeduration'];
				
			}
			
			//Select properties of next node from workflow definition
	
			
			
						
			/* if($rowNextNode['workflownextnodeid'] > 0) {
			$workflowNodeId = $rowNextNode['workflownextnodeid'];
			$workflowNextNodeId = $rowNextNode['workflownextnodeid'] + 1;
			$sqlNextNodeType= ("SELECT workflownodetypeid FROM workflownode WHERE workflowid = ? AND workflownodeid = ? ");
			$stmtNextNodeType = $user_login->runQuery($sqlNextNodeType);
			$stmtNextNodeType->execute(array($loanWorkflowId,$workflowNextNodeId));	
			$rowNextNodeType= $stmtNextNodeType->fetch();
			$nextNodeType = $rowNextNodeType['workflownodetypeid'];
			}else{
				$workflowNodeId = $rowWorkflowProperties['workflownodeid'];;
				$workflowNextNodeId = $rowWorkflowProperties['workflownodeid'] + 1 ;
				$sqlNextNodeType= ("SELECT workflownodetypeid FROM workflownode WHERE workflowid = ? AND workflownodeid = ? ");
			$stmtNextNodeType = $user_login->runQuery($sqlNextNodeType);
			$stmtNextNodeType->execute(array($loanWorkflowId,$workflowNextNodeId));	
			$rowNextNodeType= $stmtNextNodeType->fetch();
			$nextNodeType = $rowNextNodeType['workflownodetypeid'];
			}
			
			
			
			if ($nextNodeType == 6) {
				$loanStatus = 5 ;
				$workflowStatus = 1; //completed
			}else {
				$loanStatus = 1 ;
				$workflowStatus = 0; //active while -1 terminated
			} */
			
}
	//$If workflow is active;
	if (($workflowStatus == 0) AND ($approvedtypeid == 0)){	
	
	$workflowUserComments = $workflowUserComments;
	
	try{
	
		$user_login->beginTransaction();
	
		$updateLoanStatus = $user_login->runQuery("UPDATE loan SET loanstatus = ? WHERE loanid = ?");
		$updateLoanStatus->execute(array($loanStatus,$loanid));
	
	
		$insertWorkflowHistory = $user_login->runQuery("INSERT INTO workflowhistory(workflowid,workflownodeid,workflownextnodeid,workflowoitemid,workflownodeendtime,workflowownerid,workflowstatus,workflownodetypeid,workflowinstruction,workflowusercomments) VALUES (?,?,?,?,?,?,?,?,?)");
		$insertWorkflowHistory->execute(array($loanWorkflowId,$currentNodeId,$nextNodeId,$loanid,$currentNodeDuration,$workflowOwnerId,$workflowStatus,$nextNodeTypeId,$currentNodeComments,$workflowUserComments));
	
		if ($user_login->commit()) {
	
			echo '<script type="text/javascript"> alert("Loan Created Successfully.");</script>';
			$user_login->redirect('../loan/loan_application_list.php');
		}
	} catch ( PDOException $e ) {
		$user_login->rollBack();
		print $e->getMessage ();
		$user_login->redirect('../loan/loan_application_list.php');
	}

}else if($approvedtypeid == -1){
	
	$workflowStatus = -1;
	$loanStatus = -1 ;
	$workflowUserComments = $workflowUserComments;
	
	try{
	
		$user_login->beginTransaction();
	
		$updateLoanStatus = $user_login->runQuery("UPDATE loan SET loanstatus = ? WHERE loanid = ?");
		$updateLoanStatus->execute(array($loanStatus,$loanid));		
			
	
		$insertWorkflowHistory = $user_login->runQuery("INSERT INTO workflowhistory(workflowid,workflownodeid,workflownextnodeid,workflowoitemid,workflownodeendtime,workflowownerid,workflowstatus,workflownodetypeid,workflowinstruction) VALUES (?,?,?,?,?,?,?,?,?)");
		$insertWorkflowHistory->execute(array($loanWorkflowId,$currentNodeId,$nextNodeId,$loanid,$currentNodeDuration,$workflowOwnerId,$workflowStatus,$nextNodeTypeId,$currentNodeComments));
		
		if ($user_login->commit()) {
	
			echo '<script type="text/javascript"> alert("Loan Created Successfully.");</script>';
			$user_login->redirect('../loan/loan_application_list.php');
		}
	} catch ( PDOException $e ) {
		$user_login->rollBack();
		print $e->getMessage ();
		$user_login->redirect('../loan/loan_application_list.php');
	}
	
}else if($workflowStatus == 1 and $approvedtypeid == 0){
	
	
	$workflowStatus = 1;
	$createdById= $approvedby;
	$comments = "Loan Disbursed";
	$date = date("Y-m-d");
	
	$issuedDate = date_format ( date_create ($date), "Y-m-d" );
	
	$sqlSelectLoan = ("SELECT loanduration,loanamount,paymentprinciple,paymentinterest,totalpaymentpermoth FROM loan WHERE loanid = ?");
	$stmtLoan = $user_login->runQuery($sqlSelectLoan);
	$stmtLoan->execute(array($loanid));
	$loanRow = $stmtLoan->fetch();
	$loanDuration = $loanRow['loanduration'];
	$loanAmount = $loanRow['loanamount'];
	$paymentPrinciple = $loanRow['paymentprinciple'];
	$paymentInterest = $loanRow['paymentinterest'];
	$totalPaymentPerMonth = $loanRow['totalpaymentpermoth'];
	
	
	
	// Loan End Date
	function loanEndDate($issuedDate, $loanDuration) {
		$loanDuration = $loanDuration . " month";
		$issuedDate = date_create ( $issuedDate );
		date_add ( $issuedDate, date_interval_create_from_date_string ( $loanDuration ) );
		return date_format ( $issuedDate, "Y-m-d" );
	}
	

	
	
	$endDate = loanEndDate($issuedDate, $loanDuration);
		
	
	
	
	
	try{
		
		function loanPaymentDate($issuedDate) {
			$loanDuration = 1 . " month";
			$issuedDate = date_create ( $issuedDate );
			date_add ( $issuedDate, date_interval_create_from_date_string ( $loanDuration ) );
			return date_format ( $issuedDate, "Y-m-d" );
		}
	
		$user_login->beginTransaction();
	
		$updateLoanStatus = $user_login->runQuery("UPDATE loan SET loanstatus = ?,	issueddate = ?, enddate = ? WHERE loanid = ?");
		$updateLoanStatus->execute(array($loanStatus,$issuedDate,$endDate,$loanid));		
			
	
		$insertWorkflowHistory = $user_login->runQuery("INSERT INTO workflowhistory(workflowid,workflownodeid,workflownextnodeid,workflowoitemid,workflownodeendtime,workflowownerid,workflowstatus,workflownodetypeid,workflowinstruction) VALUES (?,?,?,?,?,?,?,?,?)");
		$insertWorkflowHistory->execute(array($loanWorkflowId,$currentNodeId,$nextNodeId,$loanid,$currentNodeDuration,$workflowOwnerId,$workflowStatus,$nextNodeTypeId,$currentNodeComments));
		
		
		$eventTitle = "First Installment";
		$eventTypeId =  1;
		$eventStartDate = loanPaymentDate($issuedDate);
		$eventStartTime=0;
		$eventEndDate=  $endDate;
		$eventEndTime=0;
		$eventDiscription="Reminder for return";
		$eventOwner = $createdById;
		$eventLocation = "Kilele Hq";
		
		
		
		//Create Payment Schedule
		for($i=1; $i <=$loanDuration; $i++){
			$loanDurationin = $i . " month";
			$issuedDate = date_create ( date("Y-m-d"));
			date_add ( $issuedDate, date_interval_create_from_date_string ( $loanDurationin ) );
			$paymentDate =date_format ( $issuedDate, "Y-m-d" );
		
			$insertEvent = $user_login->runQuery("INSERT INTO event (loanid, eventtitle,eventtypeid,eventstartdate,eventstarttime,eventenddate,eventendtime,eventdiscription,eventowner,eventlocation) VALUES (?,?,?,?,?,?,?,?,?,?)");
			$insertEvent->execute(array($loanid,$eventTitle,1,$paymentDate,$eventStartTime,$paymentDate,$eventEndTime,$eventDiscription,$eventOwner,$eventLocation));
				
			$insertPaymentSchedule =$user_login->runQuery("INSERT INTO loanpaymentschedule (loanid,paymentdate,principle,interest,returntotalamount) VALUES (?,?,?,?,?)");
			$insertPaymentSchedule->execute(array($loanid,$paymentDate,$paymentPrinciple,$paymentInterest,$totalPaymentPerMonth));
		}
	
		if ($user_login->commit()) {
	
			echo '<script type="text/javascript"> alert("Loan Created Successfully.");</script>';
			$user_login->redirect('../loan/loan_application_list.php');
		}
	} catch ( PDOException $e ) {
		$user_login->rollBack();
		print $e->getMessage ();
	
		$user_login->redirect('../loan/loan_application_list.php');
	}
}
	else{

	$user_login->redirect('../loan/loan_application_list.php');
}








