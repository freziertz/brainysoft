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

if (isset($_GET['daterange'] )){
	$dateRange = $_GET['daterange'];
	
	if($dateRange == 'year'){
		$sqlSelectLoanList = ("SELECT distinct loanid,Applicant, DueDays,issueddate,enddate,loanamount, totalinterest,totalamount,loanduration, loanstatus,totalfine,processingfee,paidamount,loanbalance,totaldue FROM loanallview WHERE YEAR(issueddate) = YEAR(CURDATE())");
		$stmtLoanList = $user_login->runQuery($sqlSelectLoanList);
		$stmtLoanList->execute(array());
		
		}elseif($dateRange == 'month'){
			$sqlSelectLoanList = ("SELECT distinct loanid,Applicant, DueDays,issueddate,enddate,loanamount, totalinterest,totalamount,loanduration, loanstatus,totalfine,processingfee,paidamount,loanbalance,totaldue FROM loanallview WHERE MONTH(issueddate) = MONTH(CURDATE())");
			$stmtLoanList = $user_login->runQuery($sqlSelectLoanList);
			$stmtLoanList->execute(array());
		}elseif($dateRange == 'week'){
			$sqlSelectLoanList = ("SELECT distinct loanid,Applicant, DueDays,issueddate,enddate,loanamount, totalinterest,totalamount,loanduration, loanstatus,totalfine,processingfee,paidamount,loanbalance,totaldue FROM loanallview WHERE WEEK(issueddate) = WEEK(CURDATE())");
			$stmtLoanList = $user_login->runQuery($sqlSelectLoanList);
			$stmtLoanList->execute(array());
		}elseif($dateRange == 'today'){
			$sqlSelectLoanList = ("SELECT distinct loanid,Applicant, DueDays,issueddate,enddate,loanamount, totalinterest,totalamount,loanduration, loanstatus,totalfine,processingfee,paidamount,loanbalance,totaldue FROM loanallview WHERE DAY(issueddate) = DAY(CURDATE())");
			$stmtLoanList = $user_login->runQuery($sqlSelectLoanList);
			$stmtLoanList->execute(array());
		}elseif($dateRange == 'custom'){
			$sqlSelectLoanList = ("SELECT distinct loanid,Applicant, DueDays,issueddate,enddate,loanamount, totalinterest,totalamount,loanduration, loanstatus,totalfine,processingfee,paidamount,loanbalance,totaldue FROM loanallview WHERE YEAR(issueddate) = YEAR(CURDATE())");
			$stmtLoanList = $user_login->runQuery($sqlSelectLoanList);
			$stmtLoanList->execute(array());
		}else{
			$sqlSelectLoanList = ("SELECT distinct(loanid),Applicant, DueDays,issueddate,enddate,loanamount, totalinterest,totalamount,loanduration, loanstatus,totalfine,processingfee,paidamount,loanbalance,totaldue FROM loanallview");
			$stmtLoanList = $user_login->runQuery($sqlSelectLoanList);
			$stmtLoanList->execute(array());
	}
	
}else{
	
	$sqlSelectLoanList = ("SELECT distinct(loanid),Applicant, DueDays,issueddate,enddate,loanamount, totalinterest,totalamount,loanduration, loanstatus,totalfine,processingfee,paidamount,loanbalance,totaldue FROM loanallview");
			$stmtLoanList = $user_login->runQuery($sqlSelectLoanList);
			$stmtLoanList->execute(array());
}


 



	

		

//Define page variable	
$location = "All Loan";
$title = "All Loan";
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
																	<th>L#</th>
																	<th>ApplicantName</th>
																	<th>IssuedDate</th>
																	<th>EndDate</th>   
																	<th>Duration</th>
																	 <th>Amount</th>
																	  <th>Interest</th>
																	 <th>Total</th>
																	<th>Fee</th>
																	<th>PaidAmount</th>
																	<th>LoanBalance</th> 
																	<th>TotalFine</th>
																	<th>TotalDue</th> 
																	<th>SecurityValue</th> 
																	<th>Details</th>                                                               
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>L#</th>
																	<th>ApplicantName</th>
																	<th>IssuedDate</th>
																	<th>EndDate</th>   
																	<th>Duration</th>
																	 <th>Amount</th>
																	  <th>Interest</th>
																	 <th>Total</th>
																	<th>Fee</th>
																	<th>PaidAmount</th>
																	<th>LoanBalance</th> 
																	<th>TotalFine</th>
																	<th>TotalDue</th> 
																	<th>SecurityValue</th> 
																	<th>Details</th>
                                                                </tr>
                                                            </tfoot>
                                                            <tbody>
	<?php
	$count = 0;
	$linksatus = "btn btn-success btn-sm active disabled";
	foreach ( $stmtLoanList->fetchAll () as $row ) {
		
		$sqlSelectLoanGuarantorCount = ("SELECT count(roleid) as guarantercount FROM loanrole where loanid = ? and roleid=2");
		$stmtLoanLoanGuarantorCount = $user_login->runQuery($sqlSelectLoanGuarantorCount);
		$stmtLoanLoanGuarantorCount->execute(array($row['loanid']));
		$row1 = $stmtLoanLoanGuarantorCount->fetch();
		$guarantorCount = $row1['guarantercount'];
		
		$sqlSelectSecurityCount = ("SELECT count(securityid) as securitcount FROM loansecurity where loanid = ? ");
		$stmtLoanSecurityCount = $user_login->runQuery($sqlSelectSecurityCount);
		$stmtLoanSecurityCount->execute(array($row['loanid']));
		$row2 = $stmtLoanSecurityCount->fetch();
		$securitCount = $row2['securitcount'];
		
		$sqlSelectContractCount = ("SELECT count(contractid) as contractcount FROM contracts where loanid = ? ");
		$stmtLoanContratctCount = $user_login->runQuery($sqlSelectContractCount);
		$stmtLoanContratctCount->execute(array($row['loanid']));
		$row3 = $stmtLoanContratctCount->fetch();
		$contractCount = $row3['contractcount'];
		
		$sqlSelectSecurityValue = ("SELECT covervalue FROM loansecurity where loanid = ? ");
		$stmtLoanSecurityValue = $user_login->runQuery($sqlSelectSecurityValue);
		$stmtLoanSecurityValue->execute(array($row['loanid']));
		$row3 = $stmtLoanSecurityValue->fetch();
		$SecurityValue = $row3['covervalue'];
		

	
		echo '<tr>';
		echo '<td> <a href="loan_details.php?id='.$row['loanid'].'">'.$row['loanid'].'</td>';
		echo '<td>'. $row['Applicant'] . '</td>';
		echo '<td>'. $row['issueddate'] . '</td>';
		echo '<td>'. $row['enddate'] . '</td>';
		echo '<td>'. $row['loanduration'] . '</td>';
		echo '<td align = "right">'. number_format($row['loanamount'],2) . '</td>';	
		echo '<td align = "right">'. number_format($row['totalinterest'],2) . '</td>';
		echo '<td align = "right">'. number_format($row['totalamount'],2) . '</td>';		
		echo '<td align = "right">'. number_format($row['processingfee'],2) . '</td>';
		echo '<td align = "right">'. number_format($row['paidamount'],2) . '</td>';
		echo '<td align = "right">'. number_format($row['loanbalance'],2) . '</td>';
		echo '<td align = "right">'. number_format($row['totalfine'],2) . '</td>';
		echo '<td align = "right">'. number_format($row['totaldue'],2) . '</td>';
		echo '<td align = "right">'. number_format($SecurityValue,2) . '</td>';
		echo '<td> <a href="loan_details.php?id='.$row['loanid'].'">Details</td>';
		

		$row['loanrole'] = 0 ;
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