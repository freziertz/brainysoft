<?php 
require_once '../user/class.user.php';
require ('../inc/fileUploader.php');
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}
else {
	$createbyid=$_SESSION['userSession'];
}
//Check if partyid is set- sent from other page
if (isset($_GET['id'])){
	$partyId = $_GET['id'];
//if partyid is not set- assign the one from this page	
}else{
	$partyId = $_POST ["partyid"];
}


//If form is submitted, process loan
if ($_SERVER ["REQUEST_METHOD"] == "POST") {	
	try {			
			//start transaction
			$user_login->beginTransaction();
			
			// Get loantypeid,loantyperate and other setting from loantype table
			$stmtLoanType = $user_login->runQuery ( "SELECT loantypeid,loantyperate,loantypename,loanfinerate,loanminimumduration,loanmaximumduration,loanminimumage
													,loanmaximumage,loanprocessingfee,securitycoverpercent,loanworkflowid FROM loantype WHERE loantypeid =:loantypeid" );
			$stmtLoanType->execute ( array (
					":loantypeid" => $_POST ['loantypeid'] 
			) );
			$rowLoanType = $stmtLoanType->fetch ( PDO::FETCH_ASSOC );
			$loantyperate = $rowLoanType ['loantyperate'];
			$loantypeid = $rowLoanType ['loantypeid'];
			$loanProcessingFee = $rowLoanType ['loanprocessingfee'];
			$securityCoverPercent = $rowLoanType ['securitycoverpercent'];
			$loanWorkflowId = $rowLoanType ['loanworkflowid'];
			
			//End get loantypeid,loantyperate and other setting from loantype table
			// Get loan workflow properties
			
			$sqlWorkflowProperties= ("SELECT workflownodeid,workflownodename, workflownodetypeid,workflownodecomments,workflownodeduration FROM workflownode WHERE workflowid = ? AND workflownodetypeid = ? ");
			$stmtWorkflowProperties = $user_login->runQuery($sqlWorkflowProperties);
			$stmtWorkflowProperties->execute(array($loanWorkflowId,1));	
			$rowWorkflowProperties= $stmtWorkflowProperties->fetch();
			//$workflowNodeId = $rowWorkflowProperties['workflownodeid'];
			$workflowNodeName = $rowWorkflowProperties['workflownodename'];
			$workflowNodeTypeId = $rowWorkflowProperties['workflownodetypeid'];
			$workflowNodeComments = $rowWorkflowProperties['workflownodecomments'];
			$workflowSNodeDuration = $rowWorkflowProperties['workflownodeduration'];
			
			$sqlNextNode= ("SELECT Max(workflownextnodeid) as workflownextnodeid,workflownodetypeid FROM workflowhistory WHERE workflowid = ? AND workflowoitemid = ? ");
			$stmtNextNode = $user_login->runQuery($sqlNextNode);
			$stmtNextNode->execute(array($loanWorkflowId,$newLoanId));	
			$rowNextNode= $stmtNextNode->fetch();
			
			if($rowNextNode['workflownextnodeid'] > 0) {
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
			}
			
			
			
		
			
			
			
			
			// End Get loan workflow properties
			
			
			
			//Start Create New Loan
			
			//Loan Interest per month
			function loanInterestPerMonth($loanAmount, $loanRate) {
				return $loanAmount * $loanRate;
			}
			
			// Total Interest for fixed
			function loanTotalInterest($loanAmount, $loanRate, $loanDuration) {
				return $loanAmount * $loanRate * $loanDuration;
			}
			
			// Total Interest for fixed
			function loanTotalInterestReducing($loanAmount, $loanRate, $loanDuration) {
				$totalInterest[$i]=0;
				for ($i=1;$i<=$loanDuration;$i+1){
				$totalInterest[$i]+=($loanAmount -($loanAmount/$loanDuration))*$loanRate;
				
				}
				
			}
			
			
			// Loan Total plus Total Interest
			function loanTotalPlusTotalInterest($loanAmount, $loanRate, $loanDuration) {
				return $loanAmount + ($loanAmount * $loanRate * $loanDuration);
			}
			
			// Interest per month
			function paymentInterestPerMonth($loanAmount, $loanRate) {
				return ($loanAmount * $loanRate);
			}
			
			// Principle per month
			function paymentPrinciplePerMonth($loanAmount, $loanDuration) {
				return $loanAmount / $loanDuration;
			}
			
			// Total per month
			function paymentTotalPerMonth($loanAmount, $loanRate, $loanDuration) {
				return ($loanAmount + ($loanAmount * $loanRate * $loanDuration)) / $loanDuration;
			}
			
			// Processing fee
			function processingFee($loanAmount,$loanProcessingFee) {
				return $loanAmount * $loanProcessingFee;
			}
			
			// Loan End Date
			function loanEndDate($issuedDate, $loanDuration) {
				$loanDuration = $loanDuration . " month";
				$issuedDate = date_create ( $issuedDate );
				$loanEndDate = date_add ( $issuedDate, date_interval_create_from_date_string ( $loanDuration ) );
				return date_format ( $loanEndDate, "Y-m-d" );
			}
			
			
			// Loan Payment Date
			function loanPaymentDate($issuedDate) {
				$loanDuration = 1 . " month";
				$issuedDate = date_create ( $issuedDate );
				$paymentDate = date_add ( $issuedDate, date_interval_create_from_date_string ( $loanDuration ) );
				return date_format ( $paymentDate, "Y-m-d" );
			}		
						
			//Create New Loan			
			$partyid = $partyId;
			$loanAmount = $_POST ['loanamount'];
			$loanDuration = $_POST ['loanduration'];
			$issuedDate = date_format ( date_create ( $_POST ['requesteddate'] ), "Y-m-d" );
			$loanRate = $loantyperate;
			$loanEndDate = loanEndDate ( $issuedDate, $loanDuration );
			$interestPerMonth = loanInterestPerMonth ( $loanAmount, $loanRate );
			$totalInterest = loanTotalInterest ( $loanAmount, $loanRate, $loanDuration );
			$totalAmount = loanTotalPlusTotalInterest ( $loanAmount, $loanRate, $loanDuration );
			$paymentPrinciple = paymentPrinciplePerMonth ( $loanAmount, $loanDuration );
			$paymentInterest = paymentInterestPerMonth ( $loanAmount, $loanRate);
			$totalPaymentPerMonth = paymentTotalPerMonth ( $loanAmount, $loanRate, $loanDuration );
			$createdById = $createbyid;			
			$loanBalance = $totalAmount;
			$totalFine = 0.00; //this is the new loan so fine set to 0
			$processingFee = processingFee ( $loanAmount,$loanProcessingFee);
			$loanStatus = 1; //Loan is not issued status is 1 waiting for approval			
			$paymentDate = loanPaymentDate($issuedDate);
			$returnInterest =$interestPerMonth;
			$returnPrinciple = $paymentPrinciple;
			$returnTotalAmount = $totalPaymentPerMonth;            		
			
			
			$insert = $user_login->runQuery ( "INSERT INTO loan (
					partyid,
					loanamount,
					loantypeid,
					loanduration,
					issueddate,
					enddate,
					interestpermonth,
					totalinterest,
					totalamount,
					paymentprinciple,
					paymentinterest,
					totalpaymentpermoth,
					loanstatus,
					createdbyid,
					loanbalance,
					totalfine,
					processingfee)
			VALUES (
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?)" );
			
			
			
			$insert->execute ( array (
					$partyid,
					$loanAmount,
					$loantypeid,
					$loanDuration,
					$issuedDate,
					$loanEndDate,
					$interestPerMonth,
					$totalInterest,
					$totalAmount,
					$paymentPrinciple,
					$paymentInterest,
					$totalPaymentPerMonth,
					$loanStatus,
					$createdById,
					$loanBalance,
					$totalFine,
					$processingFee 
			) );
			//end loan add
			
			//get loan id of just created loan
			$newLoanId = $loanid = $user_login->lastID();
			
			//add security for this loan
			$securitypeId = $_POST ['securitytypeid'];			
			$filenumber = "security_".$newLoanId."_".$securitypeId;	//format file name for security document			
			$marketValue = $_POST ['marketvalue'];
			$coverValue = $marketValue * $securityCoverPercent; //default value is 1/3			
			$securityNumber = $_POST ['securitynumber'];
			$issuedBy = $_POST ['issuedby'];
			$description = $_POST ['description'];				
			$issuedDate = $_POST ['issueddate'];
			$date = str_replace ( '/', '-', $issuedDate );
			$issuedDateInsert = date ( 'Y-m-d', strtotime ( $date ) );				
			$expirationDate = $_POST ['expirationdate'];
			$date1 = str_replace ( '/', '-', $expirationDate );
			$datetoinsert = date ( 'Y-m-d', strtotime ( $date1 ) );			
			$flup = new fileUploader\fileUploader ();
			$perPhoto = $flup->upload ( "brainysoft/doc/security/",$_FILES ['securitypath'], $filenumber );
			$Securitypath = $GLOBALS ['nameOfFile'];
			$insertd = $user_login->runQuery ( "INSERT INTO loansecurity (loanid,securitytypeid,expireddate,issueddate,	securitypath,marketvalue,covervalue,securitynumber,description) VALUES (?,?,?,?,?,?,?,?,?)" );
			$insertd->execute ( array (
					$newLoanId,
					$securitypeId,
					$datetoinsert,
					$issuedDateInsert,					
					$Securitypath,
					$marketValue,
					$coverValue,
					$securityNumber,
					$description
					 
			) );	
			//end security add

			//Guaranter add
			$guaranterPartyid =  3;//$_POST ["guaranterid"];
			$roleid = 2; //loan role for guaranter is 2
			$insertLoanRole = $user_login->runQuery("INSERT INTO loanrole(loanid,partyid,roleid) VALUES (?,?,?)");
			$inserted = $insertLoanRole->execute(array($newLoanId,$guaranterPartyid,$roleid));			
			$filenumberContract = "contract_".$newLoanId;		
			$startDate = $_POST ['startdate'];
			$date = str_replace ( '/', '-', $startDate );
			$startdatetoinsert = date ( 'Y-m-d', strtotime ( $date ) );
			$expirationDate = $_POST ['expirationdate'];
			$date1 = str_replace ( '/', '-', $expirationDate );
			$enddatetoinsert = date ( 'Y-m-d', strtotime ( $date1 ) );
			$flup = new fileUploader\fileUploader ();
			$perPhoto = $flup->upload ( "brainysoft/doc/contract/",$_FILES ['contractpath'], $filenumberContract );
			$contractpath = $GLOBALS ['nameOfFile'];
			$contractNumber =  $_POST ['contractnumber'].$loanid;
			$contractName =  $_POST ['contractname'];
			$insertd = $user_login->runQuery ( "INSERT INTO contracts (loanid,startdate,enddate,contractpath,contractnumber,contractname) VALUES (?,?,?,?,?,?)" );
			$insertd->execute ( array (
					$loanid,					
					$startdatetoinsert,
					$enddatetoinsert,
					$contractpath,
					$contractNumber,
					$contractName ) );	
			
			//End fuaranter add
			
			//Create New Event ana payment schedule for This Loan
			//Event variables
			$eventTitle = "Loan Application";
			$eventTypeId =  1; //1 is for reminder
			$eventStartDate = loanPaymentDate($issuedDate);
			$eventStartTime=0;
			$eventEndDate=  null;
			$eventEndTime=0;
			$eventDiscription="Reminder for loan processing";
			$eventOwner = $createdById;
			$eventLocation = "Kilele Hq";
			$appDate = date_create ( date("Y-m-d"));
			$applicationDate =date_format ( $appDate, "Y-m-d" );			
			
		
			
			//Insert event for reminder
			$insertEvent = $user_login->runQuery("INSERT INTO event (loanid, eventtitle,eventtypeid,eventstartdate,eventstarttime,eventenddate,eventendtime,eventdiscription,eventowner,eventlocation) VALUES (?,?,?,?,?,?,?,?,?,?)");
			$insertEvent->execute(array($newLoanId,$eventTitle,$eventTypeId,$applicationDate,$eventStartTime,$eventEndDate,$eventEndTime,$eventDiscription,$eventOwner,$eventLocation));
			
			//Sent this loan to a workflow			
			
			
			
			
			//$workflownodestarttime = Currenttimestamp
			$workflownodeendtime = null; //Workflow start time + workflow duration default is unlimited
			$workflowOwnerId = $createbyid; //
			
			
			
			
			$insertWorkflowHistory = $user_login->runQuery("INSERT INTO workflowhistory(workflowid,workflownodeid,workflownextnodeid,workflowoitemid,workflownodeendtime,workflowownerid,workflowstatus,workflownodetypeid,workflowinstruction) VALUES (?,?,?,?,?,?,?,?,?)");
			$insertWorkflowHistory->execute(array($loanWorkflowId,$workflowNodeId,$workflowNextNodeId,$newLoanId,$workflownodeendtime,$workflowOwnerId,$workflowStatus,$nextNodeType,$workflowNodeComments));
			
			
			
		
			
			//Insert party role for the loan
			$roleid = 1; //roleid 1 is the applicant role
			$insertLoanRole = $user_login->runQuery("INSERT INTO loanrole(loanid,partyid,roleid) VALUES (?,?,?)");
			$insertLoanRole->execute(array($loanid,$roleid,$partyid));
			
			//Commit is everything go well and end transaction
			if ($user_login->commit()) {				
				echo '<script type="text/javascript"> alert("Loan Created Successfully.");</script>';
				$user_login->redirect('../loan/applicant_list.php');
			}
		} catch ( PDOException $e ) {
			//If there anything wrong rollback and end transaction
			$user_login->rollBack ();
			print $e->getMessage ();			
			echo '<script type="text/javascript"> alert("Error");</script>';
		}
	
}
	
	
	//Select applicant for given party number
	$sqlSelectLoanApplications = ("SELECT party.partyid as applicantnumber, party.partytype as applicantname, concat( firstname,' ', lastname ) AS fullname, businessname
											FROM party	LEFT JOIN person ON party.partyid = person.partyid
											LEFT JOIN organization ON party.partyid = organization.partyid
											WHERE party.partyid = ?									
		");
	$stmtApplicant = $user_login->runQuery($sqlSelectLoanApplications);
	$stmtApplicant->execute(array($partyId));
	$row = $stmtApplicant->fetch();
	
	if ($row ['businessname'] == NULL){
		$applicantName = $row ['fullname'];
	}
	else{
		$applicantName =  $row ['businessname'];
	}
	

			
//Define page variable	
$location = "Request New Loan";
$title = "Request New Loan";
$breadcumb ="Request New Loan";
$breadcumbDescription=" Create new loan, add security, guaranter and contract";
$currentSymbo = "TZS";
include('../inc/header.php')



?>
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                        <div class="row">
                                            <div class="col-md-12">                                           
                                                <div class="portlet light " id="form_wizard_1">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class=" icon-layers font-blue"></i>
                                                            <span class="caption-subject font-red bold uppercase"> New Loan Details -
                                                                <span class="step-title"> Step 1 of 5</span>
                                                            </span>
                                                        </div>                                                     
                                                    </div>
                                                    <div class="portlet-body form">
                                                        <form class="form-horizontal" action="../loan/loan_application_add.php" id="submit_form" method="post" enctype="multipart/form-data">
                                                            <div class="form-wizard">
                                                                <div class="form-body">
                                                                    <ul class="nav nav-pills nav-justified steps">
                                                                        <li>
                                                                            <a href="#tab1" data-toggle="tab" class="step">
                                                                                <span class="number"> 1 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Loan infomation </span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#tab2" data-toggle="tab" class="step">
                                                                                <span class="number"> 2 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Loan Security </span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#tab3" data-toggle="tab" class="step active">
                                                                                <span class="number"> 3 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Loan Guaranter </span>
                                                                            </a>
                                                                        </li>
																		<li>
                                                                            <a href="#tab4" data-toggle="tab" class="step active">
                                                                                <span class="number"> 4 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Loan Contract </span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#tab5" data-toggle="tab" class="step">
                                                                                <span class="number"> 5 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Confirm </span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                    <div id="bar" class="progress progress-striped" role="progressbar">
                                                                        <div class="progress-bar progress-bar-success"> </div>
                                                                    </div>
                                                                    <div class="tab-content">
                                                                        <div class="alert alert-danger display-none">
                                                                            <button class="close" data-dismiss="alert"></button> You have some form errors. Please check below. </div>
                                                                        <div class="alert alert-success display-none">
                                                                            <button class="close" data-dismiss="alert"></button> Your form validation is successful! </div>
																		<!--BEGIN TAB 1 -->	
                                                                        <div class="tab-pane active" id="tab1">
                                                                            <h3 class="block">Provide Loan details</h3>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Applicant Name
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="applicantname" value="<?php echo $applicantName ?>" readonly />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                
                                                                                <div class="col-md-4">
                                                                                    <input type="hidden" class="form-control" name="partyid" value="<?php echo $partyId ?>" placeholder="Enter Loan Amount" />
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Amount
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="number" class="form-control" name="loanamount" placeholder="Enter Loan Amount" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Type
																				 <span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="loantypeid" id="country_list" class="form-control">
																						<option value=""></option>																					
																							<?php
																						  //Get loantypeid and loantypename to populaty loantype
																																	
																						   $sqlSelectLoanType = ("SELECT loantypeid,loantypename FROM loantype");
																						   $stmtAdressType = $user_login->runQuery ( $sqlSelectLoanType );
																						   $stmtAdressType->execute ( array () );
																						   foreach ( $stmtAdressType->fetchAll () as $row ) {
																							echo "<option  value='" . $row ['loantypeid'] . "'>" . $row ['loantypename'] . "</option>";
																								}
																							?>                                                                         
                                                                                    </select>
                                                                                </div>
                                                                            </div>
																			
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Duration
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="number" class="form-control" name="loanduration" placeholder="Enter Loan Duration" min="1" max="3"  />
                                                                                </div>
                                                                            </div>										
																			<div class="form-group">
																				<label class="control-label col-md-3">Requested Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="requesteddate" />
																				</div>
																			</div>																				
                                                                        </div>
<!--END TAB 1 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 2 *******************************************************************************************************************************************************************-->
                                                                        <div class="tab-pane" id="tab2">
                                                                            <h3 class="block">Provide security details</h3>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Security Type
																				 <span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="securitytypeid" id="country_list" class="form-control">
                                                                                        <option value=""></option>
																						<?php
																					  //Get securitytypeid and Securitytypename to populaty securitytype																																
																					   $sqlSelectSecurityType = ("SELECT securitytypeid,securityname FROM securitytype");
																					   $stmtSecurityType = $user_login->runQuery ( $sqlSelectSecurityType );
																					   $stmtSecurityType->execute ( array () );
																					   foreach ( $stmtSecurityType->fetchAll () as $row ) {
																						echo "<option  value='" . $row ['securitytypeid'] . "'>" . $row ['securityname'] . "</option>";
																							}
																						?>																																											
                                                                                    </select>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Issued By
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="issuedby"placeholder="Provide Issued Authority" />                                                                                    
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Security No
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="securitynumber" placeholder="Provide Security Number" />                                                                                    
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
																				<label class="control-label col-md-3">Issued Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="issueddate" placeholder="Select issued date" />
																				</div>
																			</div>	
																			<div class="form-group">
																				<label class="control-label col-md-3">Expiration Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="expirationdate" placeholder="Select expired date" />
																				</div>
																			</div>	
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Estimated Market Value
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="marketvalue" placeholder="Provide estimated market value" />                                                                                    
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">Security Copy
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="securitypath">
																					<p class="help-block"> Select copy of secuity. </p>
																				</div>
																			</div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Remarks</label>
                                                                                <div class="col-md-4">
                                                                                    <textarea class="form-control" rows="3" name="description" placeholder="Provide your remarks"></textarea>
                                                                                </div>
                                                                            </div>
                                                                                                           
                                                                          
                                                                        </div>
<!--END TAB 2 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 3 *******************************************************************************************************************************************************************-->																		
																		<div class="tab-pane" id="tab3">
                                                                            <h3 class="block">Choose or add guaranter</h3>																		
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Guaranter name
                                                                                    <span class="required"> * </span>
                                                                                </label>                                                                            
																				<div class="col-md-4">
																					<input id="tags" type="text" class="form-control" name="directorname" placeholder="Select Director" />   
																					<input type=button onClick='location.href="../loan/person_add.php"' class="btn btn-default" value="New Person" />  
																					<p class="help-block"> Select director or click new to add new guaranter. </p>																					
                                                                                </div>
                                                                            </div>
																			
																																					
                                                                        </div>
<!--END TAB 3 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 4 *******************************************************************************************************************************************************************-->	
			
																		<div class="tab-pane" id="tab4">
                                                                            <h3 class="block">Contract Details</h3>	
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Contract Name
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" value="<?php echo "Kilele Microfinance & ".$applicantName ;?>" name="contractname" readonly />                                                                                    
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Contract Number
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" value="<?php echo 'KML/LC/'.date('Y')."/";?>" name="contractnumber" readonly />                                                                                    
                                                                                </div>
                                                                            </div>																			
                                                                            <div class="form-group">
																				<label class="control-label col-md-3">Start Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="startdate" placeholder="Select start date" />
																				</div>
																			</div>	
																			<div class="form-group">
																				<label class="control-label col-md-3">Expiration Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="expirationdate" placeholder="Select expiration date" />
																				</div>
																			</div>	
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">Contract Copy
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="contractpath">
																					<p class="help-block"> Select copy of contract. </p>
																				</div>
																			</div>																																		
                                                                        </div>
<!--END TAB 4 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 5 *******************************************************************************************************************************************************************-->																											
                                                                        <div class="tab-pane" id="tab5">
                                                                            <h3 class="block">Confirm Loan Details</h3>
                                                                            <h4 class="form-section">Loan Details</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Applicant Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="applicantname"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Amount:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="loanamount"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Type:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="loantypeid"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Duration:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="loanduration"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Requested Date:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="requesteddate"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Security Type:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="securitytypeid"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Issued Authority:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="issuedby"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Security Number:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="securitynumber"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Issueddate:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="issueddate"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Estimated Market Value:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="marketvalue"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Security Copy:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="securitycopy"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Remarks:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="description"> </p>
                                                                                </div>
                                                                            </div>
																			
																			
                                                                            <h4 class="form-section">Guaranter</h4>
                                                                            
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Guaranter name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="guaranterid"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <h4 class="form-section">Contract</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Contract Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="partyid"> </p>
                                                                                </div>
                                                                            </div> 
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Contract Number:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="contractnumber"> </p>
                                                                                </div>
                                                                            </div> 
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Start Date:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="startdate"> </p>
                                                                                </div>
                                                                            </div> 
																				<div class="form-group">
                                                                                <label class="control-label col-md-3">Epiration Date:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="expirationdate"> </p>
                                                                                </div>
                                                                            </div> 
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Contract Copy:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="contractpath"> </p>
                                                                                </div>
                                                                            </div> 
																																						
                                                                          </div>
<!--END TAB 5*********************************************************************************************************************************************************************-->
								
                                                                    </div>
                                                                </div>
																
                                                                <div class="form-actions">
                                                                    <div class="row">
                                                                        <div class="col-md-offset-3 col-md-9">
                                                                            <a href="javascript:;" class="btn default button-previous">
                                                                                <i class="fa fa-angle-left"></i> Back </a>
                                                                            <a href="javascript:;" class="btn btn-outline green button-next"> Continue
                                                                                <i class="fa fa-angle-right"></i>
                                                                            </a>
                                                                            <a href="javascript:submitform();" class="btn green button-submit"  name="btnSave"> Submit
                                                                                <i class="fa fa-check"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END PAGE CONTENT INNER -->
                                </div>
                            </div>
                            <!-- END PAGE CONTENT BODY -->
                            <!-- END CONTENT BODY -->
                        </div>
						
                        <!-- END CONTENT -->
<?php include('../inc/footer.php'); ?>	 

<script type="text/javascript">
	function submitform()
	{
		document.getElementById("submit_form").submit();

	}
	</script>	

<script>
$( function(){
	  var availableTags = 
	<?php $sqlSelectPerson = ("SELECT 	partyid,CONCAT(partyid,' ',firstname,' ',lastname)as fullname FROM person");
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
	$('#tags').autocomplete({ 
	     source: availableTags,
	     change: function (event, ui) { 
	    	 document.getElementById('directorid').value = ui.item.id;	        
	         } });  
	  } );
 </script>
																