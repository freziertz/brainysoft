<?php 
//chnge this code to clear event and to add payment status of each installment
//also consider event reschedule
//this suppose to look on payment schedule and check first instalment is due then change status to be done
require_once '../user/class.user.php';
require ('../inc/fileUploader.php');
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}else{
	$createbyid=$_SESSION['userSession'];
}

if(isset($_GET['id']))
{
	$loanid = $_GET['id'] ;
}

if ($_SERVER ["REQUEST_METHOD"] == "POST") {
	
	$loanid = $_POST['loanid'] ;

	if (!isset ( $_POST ["btnSave"] )) {
		
	
		
		try {
			//start transaction
			$user_login->beginTransaction();
			
			// Get loanid,loanbalance from loan table
			$stmt = $user_login->runQuery ( "SELECT loanid, partyid, loanbalance,totalfine FROM loan WHERE loanid =:loanid" );
			$stmt->execute ( array (
					":loanid" => $_POST ['loanid'] 
			) );
			$row = $stmt->fetch ( PDO::FETCH_ASSOC );
			$loanBalance = $row ['loanbalance'];
			$totalFine = $row ['totalfine'];
			$loanId = $row ['loanid'];
			$partyid = $row ['partyid'];
			
			
			$stmtAccount = $user_login->runQuery ( "SELECT accountid, accountbalance FROM accounts WHERE accountid =:accountid" );
			$stmtAccount->execute ( array (
					":accountid" => $_POST ['paymentaccountid']
			) );
			$row = $stmtAccount->fetch ( PDO::FETCH_ASSOC );
			$accountBalance = $row ['accountbalance'];
			$accountId = $row ['accountid'];
			
			
			
			//$paymentAmount =  $_POST ['loanidtxt']
			
			$paymentTypeId = $_POST ['paymentaccountid'];
		
			
			
			$newPayment = $_POST ['loanpaymentamount'];
			function updateBalance($loanBalance, $newPayment) {
				if (($loanBalance - $newPayment) >= 0) {
					return $loanBalance - $newPayment;
				} else
					return - 1;
			}
			function updateTotalBalance($loanBalance, $newPayment, $totalFine) {
				if (($loanBalance + $totalFine - $newPayment) >= 0) {
					return $loanBalance + $totalFine - $newPayment;
				} else
					return - 1;
			}
			
			if (updateBalance ( $loanBalance, $newPayment ) != - 1) {
				$loanBalance = updateBalance ( $loanBalance, $newPayment );
				$totalLoanFine = $totalFine;
			} else if (updateTotalBalance ( $loanBalance, $newPayment, $totalFine ) != - 1) {
				$totalLoanFine = updateTotalBalance ( $loanBalance, $newPayment, $totalFine );
				$loanBalance = 0;
			} else{
				$totalLoanFine = 0;
				$loanBalance = 0;
				echo '<script type="text/javascript"> alert("Payment should be equal or less than Balance and Fine.");</script>';
			}
				//Calculate new account balance
			function accountBalance($accountBalance,$newPayment){
				return $accountBalance + $newPayment;
			}
			
			$accountBalance = accountBalance($accountBalance,$newPayment);
				
			
			$paymentDate = date_format ( date_create ( $_POST ['paymentdate'] ), "Y-m-d" );
			
			$createdById = $createbyid;
			
			$stmt = $user_login->runQuery ( "UPDATE loan SET loanbalance = ?, totalfine= ? WHERE loanid = ? " );
			$stmt->execute ( array (
					$loanBalance,
					$totalLoanFine,
					$loanId 
			) );
			
			
			
			//Update account balance on account table
			
			$stmt = $user_login->runQuery ( "UPDATE accounts SET accountbalance = ? WHERE accountid = ?" );
			$stmt->execute ( array (
					$accountBalance,					
					$accountId
			) );
			
			//Insert loan payment history on loan payment table
			
			
			$insertPayment = $user_login->runQuery ( "INSERT INTO loanpayment (loanid,paymentdate,paymentamount,loanbalance,paymenttypeid,receivedbyid) VALUES (?,?,?,?,?,?)" );
			$insertPayment->execute ( array (
					$loanId,
					$paymentDate,
					$newPayment,
					$loanBalance,
					$paymentTypeId,
					$createbyid 
			)
			 );
			
			//If everything OK commit			
			if ($user_login->commit ()) {
				
				echo '<script type="text/javascript"> alert("Payment Updated Successfully.");</script>';
			}
			//Otherwise catch error and rollback everything
		} catch ( PDOException $e ) {
			$user_login->rollBack ();
			print $e->getMessage ();
		}
	}
}


$sqlSelectApplicant = ("SELECT Applicant FROM loanallview where loanid = ?");
$stmtApplicant = $user_login->runQuery ( $sqlSelectApplicant );
$stmtApplicant->execute ( array ($loanid) );
$applicantRow = $stmtApplicant->fetch();
$applicantName = $applicantRow['Applicant'];

	$sqlSelectLoanList = ("SELECT loanid,loanbalance, totalfine,(loanbalance+totalfine) as totaldue FROM loan WHERE loanid=?");
	$stmtLoanList = $user_login->runQuery($sqlSelectLoanList);
	$stmtLoanList->execute(array($loanid));
	$rowLoanList = $stmtLoanList->fetch();
	$loanBalance = $rowLoanList['loanbalance'];
	$totalFine = $rowLoanList['totalfine'];
	$totalDue = $rowLoanList['totaldue'];


			
//Define page variable	
$location = "Loan Payment";
$title = "Loan Payment";
$breadcumb ="Loan payment";
$breadcumbDescription=" All payment";
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
                                                            <span class="caption-subject font-red bold uppercase"> Payment Details -
                                                                <span class="step-title"> Step 1 of 2 </span>
                                                            </span>
                                                        </div>                                                     
                                                    </div>
                                                    <div class="portlet-body form">
                                                        <form class="form-horizontal" action="../loan/loan_payment.php" id="submit_form" method="post" enctype="multipart/form-data">
                                                            <div class="form-wizard">
                                                                <div class="form-body">
                                                                    <ul class="nav nav-pills nav-justified steps">
                                                                        <li>
                                                                            <a href="#tab1" data-toggle="tab" class="step">
                                                                                <span class="number"> 1 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Add Payment </span>
                                                                            </a>
                                                                        </li>                                                                        
                                                                        <li>
                                                                            <a href="#tab2" data-toggle="tab" class="step">
                                                                                <span class="number"> 2 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Confirm Payment</span>
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
                                                                            <h3 class="block">Add Payment</h3>                                                                          
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Id
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="loanid" value="<?php echo $loanid ?>" readonly />
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Name
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="name" value="<?php echo $applicantName; ?>" readonly />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Balance
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="loanbalance" value="<?php echo number_format($loanBalance,2); ?>" readonly />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Fine
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="totalfine" value="<?php echo number_format($totalFine,2) ; ?>" readonly />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Total Due
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" align="right" class="form-control" name="totaldue" value="<?php echo number_format($totalDue,2); ?>" readonly />
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Amount
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="loanpaymentamount" placeholder="Provide your last name" />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
																				<label class="control-label col-md-3">Payment Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="paymentdate" />
																				</div>
																			</div>																			
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Account
																				<span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="paymentaccountid" id="country_list" class="form-control">
                                                                                        <?php																																										
																						// Get loantypeid and loantypename to populaty loantype
																						$sqlSelectPaymentMethod = ("SELECT accountid,accountname FROM accounts");
																						$stmtLoanPaymentMethod =$user_login->runQuery ( $sqlSelectPaymentMethod );	
																						$stmtLoanPaymentMethod->execute ( array () );
																						foreach ( $stmtLoanPaymentMethod->fetchAll () as $row ) {
																							echo "<option value='" . $row ['accountid'] . "'>" . $row ['accountname'] . "</option>";
																						}																																										
																						?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>																			 
																			
																		</div>
                      
<!--END TAB 1 *********************************************************************************************************************************************************************-->
																		
                                                                        

<!--BEGIN TAB 2*******************************************************************************************************************************************************************-->																
                                                                        <div class="tab-pane" id="tab2">
                                                                            <h3 class="block">Confirm your details</h3>
                                                                            <h4 class="form-section">Payment Details</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Id:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="loanid"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Full Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="name"> </p>
                                                                                </div>
                                                                            </div>
                                                                            
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Date:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="paymentdate"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Amount:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="dateofbirth"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Account:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="paymentaccountid"> </p>
                                                                                </div>
                                                                            </div>
																			
                                                                            
                                                                        </div>
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