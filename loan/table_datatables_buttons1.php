<?php 
require_once '../user/class.user.php';
session_start();
$user_login = new USER();


if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}else{
	$createbyid=$_SESSION['userSession'];
}
//Loan application
$sqlSelectLoanApplications = ("SELECT loanid,Applicant,count(loanid)as guarantors, DueDays,phonenumber,loanroleid, emailaddress,issueddate,enddate,loanamount,totalinterest,totalamount,totalfine,processingfee,paidamount,loanbalance,totaldue FROM applicantview where loanstatus=1 group by loanid");
$stmtLoanApplications = $user_login->runQuery($sqlSelectLoanApplications);
$stmtLoanApplications->execute(array());
	
//Define page variable	
$location = "Loan Application";
$title = "Loan Application";
$breadcumb ="Loan Application";
$breadcumbDescription=" View loan application details, add identity, physical address, director and reject or approve loan";
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
																	<th>Number</th>
																	<th>Applicant</th>
																	<th>Date</th>
																	<th>Mobile</th>
																	<th>Amount</th>
																	<th>Security</th>
																	<th>Contract</th>
																	<th>Guarantor</th>
																	<th>Checked</th>
																	<th>Reject</th>                                                                
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>Number</th>
																	<th>Applicant</th>
																	<th>Date</th>
																	<th>Mobile</th>
																	<th>Amount</th>
																	<th>Security</th>
																	<th>Contract</th>
																	<th>Guarantor</th>
																	<th>Checked</th>
																	<th>Reject</th>
                                                                </tr>
                                                            </tfoot>
                                                            <tbody>
	<?php
	$count = 0;
	$linksatus = "btn btn-success btn-sm active disabled";
	foreach ( $stmtLoanApplications->fetchAll () as $row ) {
		
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
		
		echo '<tr>';
		echo '<td>'. $row['loanid'] . '</td>';
		echo '<td>'. $row['Applicant'] . '</td>';
		echo '<td>'. $row['issueddate'] . '</td>';
		echo '<td>'. $row['phonenumber'] . '</td>';
		echo '<td>'. $row['loanamount'] . '</td>';		
		echo "<td><a title='Security' class='btn btn-success btn-sm active' href='security_add.php?id=" . $row['loanid']."&wtd='addkits'".">".$securitCount."</a></td>";
		echo "<td><a title='Contract' class='btn btn-success btn-sm active' href='contract_add.php?id=" . $row['loanid'] ."&wtd='addkits'".">".$contractCount."</a></td>";
		echo "<td><a title='Guarantor' class='btn btn-success btn-sm active' href='person_loan_role_add.php?id=" . $row['loanid'] ."&wtd='addkits'".">".$guarantorCount." </a></td>";
		
		if (($guarantorCount >= 2)&&($contractCount >= 1)&&($securitCount >= 1)){
		echo "<td><a title='Complete' class='btn btn-success btn-sm active' href='loan_process.php?id=" . $row['loanid'] ."&aid=" . $createbyid . "&done=" . 1 ."&apt=" . 2 . "'>Done</a></td>";
		}else {
			echo "<td><a title='Not Done' class='btn btn-danger btn-sm disabled' href='loan_process.php?id=" . $row['loanid'] ."&aid=".$createbyid."'> No </a></td>";
		}
			
		echo "<td><a id='demo' title='Rejected' class='btn btn-success btn-sm active' href='loan_process.php?id=" . $row['loanid'] ."&aid=" . $createbyid . "&done=" . 1 ."&apt=" . 21 . "'>Reject</a></td>";
				echo '</tr>';
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