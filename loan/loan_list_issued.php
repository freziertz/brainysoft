<?php 
require_once '../user/class.user.php';
session_start();
$user_login = new USER();


if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}else{
	$loginUser=$createbyid=$_SESSION['userSession'];
}
 
	 $loanStatus = 5;
	 


if(isset($_GET['duestatus']) and ($_GET['duestatus']=='yes')){
	$sqlSelectLoanApplications = ("SELECT loanid,Applicant, DueDays,phonenumber,loanroleid, emailaddress,issueddate,enddate,loanamount,totalinterest,totalamount,loanduration,totalfine,processingfee,paidamount,loanbalance,totaldue FROM applicantview 
	where loanstatus = ? and
	totaldue > 0 
	group by loanid");
	//this suppose to look on payment schedule and check first instalment is due then change status to be done
	$stmtLoanApplications = $user_login->runQuery($sqlSelectLoanApplications);
	$stmtLoanApplications->execute(array($loanStatus));	
}else{
	$sqlSelectLoanApplications = ("SELECT loanid,Applicant, DueDays,phonenumber,loanroleid, emailaddress,issueddate,enddate,loanamount,totalinterest,totalamount,loanduration,totalfine,processingfee,paidamount,loanbalance,totaldue FROM applicantview where loanstatus = ? group by loanid");
	$stmtLoanApplications = $user_login->runQuery($sqlSelectLoanApplications);
	$stmtLoanApplications->execute(array($loanStatus));	
}


//Loan application
	


//Define page variable	
$location = "Issued Loan";
$title = "Issued Loan";
$breadcumb ="Issued Loan";
$breadcumbDescription=" View loan details, loan status and add payment";
$currentSymbo = "TZS";

include('../inc/header.php');
?>
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                          <div class="row">
                                            <div class="col-md-12">
                                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption font-dark">
                                                            <i class="icon-settings font-dark"></i>
                                                            <span class="caption-subject bold uppercase"><?php echo $title?></span>
                                                        </div>
                                                        <div class="tools"> </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                                                            <thead>
                                                                <tr>
																	<th>Loan</th>
																	<th>Applicant</th>
																	<th>Date</th>
																	<th>Mobile</th>
																	<th>Amount</th>
																	<th>Duration</th>
																	<th>Sec. Value</th> 
																	<th>Payment</th> 
																	<th>Event</th>	 
																	<th>Details</th>                                                                
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>Loan</th>
																	<th>Applicant</th>
																	<th>Date</th>
																	<th>Mobile</th>
																	<th>Amount</th>
																	<th>Duration</th>
																	<th>Sec. Value</th> 
																	<th>Payment</th> 
																	<th>Event</th>	 
																	<th>Details</th> 
                                                                </tr>
                                                            </tfoot>
                                                            <tbody>
	<?php
	$count = 0;
	$linksatus = "btn btn-success btn-sm active disabled";
	foreach ( $stmtLoanApplications->fetchAll () as $row ) {
		
		$sqlSelectSecurityValue = ("SELECT covervalue FROM loansecurity where loanid = ? ");
		$stmtLoanSecurityValue = $user_login->runQuery($sqlSelectSecurityValue);
		$stmtLoanSecurityValue->execute(array($row['loanid']));
		$row3 = $stmtLoanSecurityValue->fetch();
		$SecurityValue = $row3['covervalue'];	
		
		
		echo '<tr>';
		echo '<td>'. $row['loanid'] . '</td>';
		echo '<td>'. $row['Applicant'] . '</td>';
		echo '<td>'. $row['issueddate'] . '</td>';
		echo '<td>'. $row['phonenumber'] . '</td>';
		echo '<td>'. $row['loanamount'] . '</td>';	
		echo '<td>'. $row['loanduration'] . '</td>';
		echo '<td>'. $SecurityValue . '</td>';	
		echo "<td><a title='Details Security' class='btn btn-success btn-sm active' href='loan_payment.php?id=" . $row['loanid']."&wtd='addkits'".">Payment</a></td>";	
		echo "<td><a title='Details Security' class='btn btn-success btn-sm active' href='../event/event_add.php?id=" . $row['loanid']."&wtd='addkits'".">Event</a></td>";
		echo "<td><a title='Details Security' class='btn btn-success btn-sm active' href='loan_details.php?id=" . $row['loanid']."&wtd='addkits'".">Details</a></td>";
	}	
	?>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- END EXAMPLE TABLE PORTLET-->                                          
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