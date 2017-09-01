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

if(isset($_GET['id']) )
{
	$loanid = $_GET['id'] ;
}

$sqlSelectLoan = ("SELECT loanid,partyid,Applicant, DueDays,phonenumber,loanroleid, emailaddress,issueddate,enddate,loanamount,totalinterest,totalamount,totalfine,processingfee,paidamount,loanbalance,totaldue FROM applicantview where loanstatus=5 and loanid = ?");
$stmtLoan = $user_login->runQuery($sqlSelectLoan);
$stmtLoan->execute(array($loanid));
$loanRow = $stmtLoan->fetch();
$loanApplicant = $loanRow['Applicant'];
$partyId = $loanRow['partyid'];
$issuedDate = $loanRow['issueddate'];
$endDate = $loanRow['enddate'];
$loanAmount = $loanRow['loanamount'];
$totalInterest = $loanRow['totalinterest'];
$totalFine= $loanRow['totalfine'];
$loanProcessingFee= $loanRow['processingfee'];
$paidAmount= $loanRow['paidamount'];
$loanBalance= $loanRow['loanbalance'];
$totalDue= $loanRow['totaldue'];
$mobile= $loanRow['phonenumber'];
$email= $loanRow['emailaddress'];


$sqlSelectLoanGuarantor = ("SELECT loanrole.partyid, CONCAT(firstname,' ',lastname) as fullname FROM loanrole, person where loanid = ? and roleid=2 and loanrole.partyid = person.partyid");
$stmtLoanLoanGuarantor = $user_login->runQuery($sqlSelectLoanGuarantor);
$stmtLoanLoanGuarantor->execute(array($loanid));




/*
$sqlSelectLoanDirector = ("SELECT loanrole.partyid as dpartyid, CONCAT(firstname,' ',lastname) as fullname FROM loanrole, person where loanid = ? and roleid=4 and loanrole.partyid = person.partyid");
$stmtLoanLoanDirector = $user_login->runQuery($sqlSelectLoanDirector);
$stmtLoanLoanDirector->execute(array($loanid)); 
*/


$sqlSelectSecurity = ("SELECT securitypath,expireddate,covervalue,marketvalue,loansecurity.securitytypeid, securityname FROM loansecurity,securitytype where securitytype.securitytypeid=loansecurity.securitytypeid and loanid = ? ");
$stmtLoanSecurity = $user_login->runQuery($sqlSelectSecurity);
$stmtLoanSecurity->execute(array($loanid));

$rowSecurity = $stmtLoanSecurity->fetch();
if (isset($rowSecurity['securitypath'])){
$securityPath = "../doc/contract/".$rowSecurity['securitypath'];
}else{
	$securityPath ="#";
} 


$sqlSelectPhysicalAddressResidence = ("SELECT physicaladdressid,location,street,housenumber FROM physicaladdress,loan  where physicaladdresstypeid = 1 and loanid = ? and loan.partyid = physicaladdress.partyid ");
$stmtLoanPhysicalAddressResidence = $user_login->runQuery($sqlSelectPhysicalAddressResidence);
$stmtLoanPhysicalAddressResidence->execute(array($loanid));
$rowPhR = $stmtLoanPhysicalAddressResidence -> fetch();
$location = $rowPhR['location'];
$street = $rowPhR['street'];
$houseNumber = $rowPhR['housenumber'];

$sqlSelectPhysicalAddressResidenceb = ("SELECT physicaladdressid,location,street,housenumber,country,region,district,ward,localgov,pobox,streetrepresentative,description FROM physicaladdress  where physicaladdresstypeid = 2 and partyid = ? ");
$stmtLoanPhysicalAddressResidenceb = $user_login->runQuery($sqlSelectPhysicalAddressResidenceb);
$stmtLoanPhysicalAddressResidenceb->execute(array($partyId));
$rowPhb= $stmtLoanPhysicalAddressResidenceb -> fetch();
$locationb = $rowPhb['location'];
$streetb = $rowPhb['street'];
$houseNumberb = $rowPhb['housenumber'];
$countryb = $rowPhb['country'];
$regionb = $rowPhb['region'];
$districtb = $rowPhb['district'];
$wardb = $rowPhb['ward'];
$localgovb = $rowPhb['localgov'];
$poboxb = $rowPhb['pobox'];
$streetrepresentativeb = $rowPhb['streetrepresentative'];
$descriptionb = $rowPhb['description'];

$sqlSelectPhysicalAddressResidenceb = ("SELECT physicaladdressid,location,street,housenumber,country,region,district,ward,localgov,pobox,streetrepresentative,description FROM physicaladdress  where physicaladdresstypeid = 1 and partyid = ? ");
$stmtLoanPhysicalAddressResidenceb = $user_login->runQuery($sqlSelectPhysicalAddressResidenceb);
$stmtLoanPhysicalAddressResidenceb->execute(array($partyId));
$rowPhr= $stmtLoanPhysicalAddressResidenceb -> fetch();
$locationr = $rowPhr['location'];
$streetr = $rowPhr['street'];
$houseNumberr = $rowPhr['housenumber'];
$countryr = $rowPhr['country'];
$regionr = $rowPhr['region'];
$districtr = $rowPhr['district'];
$wardr = $rowPhr['ward'];
$localgovr = $rowPhr['localgov'];
$poboxr = $rowPhr['pobox'];
$streetrepresentativer = $rowPhr['streetrepresentative'];
$descriptionr = $rowPhr['description'];

$sqlSelectContract = ("SELECT contractpath FROM contracts where loanid = ? ");
$stmtLoanContratct= $user_login->runQuery($sqlSelectContract);
$stmtLoanContratct->execute(array($loanid));
$row3 = $stmtLoanContratct->fetch();
if (isset($row3['contractpath'])){
$contractPath = "../doc/contract/".$row3['contractpath'];
}else{
	//$contractPath = '<script type="text/javascript"> alert("Person Created Successfully.");</script>';
	$contractPath = '#';
}








	

		

//Define page variable	
$location = "Loan details";
$title = "Details for ".$loanApplicant;
$breadcumb ="Loan details for ".$loanApplicant;
$breadcumbDescription=" View loan details, loan status and add payment";
$currentSymbo = "TZS";

include('../inc/header.php');
?>
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                          <div class="row">
                                            <div class="col-md-12">
											<div class="panel panel-success">
											<div class="panel-heading"><?php echo "Details for ".$loanApplicant?></div>
											<div class="panel-body"><div>
                                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                                <div class="portlet light ">
                                               
                                                    <div class="portlet-body">
                                                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                                                            <tbody>
														  <tr><td>Applicant</td><td><a href='../loan/applicant_details.php?id=<?php echo $loanid; ?>'><?php echo $loanApplicant;?></a></td>
														  <td>Loan Amount</td><td align="right"><?php echo number_format($loanAmount,2)?></td> </tr>
														   <tr> <td>Issued Date</td><td><?php echo $issuedDate?></td> 
														   <td>Processing Fee</td><td align="right"><?php echo number_format($loanProcessingFee,2);?></td>
														   </tr>
														   <tr> <td>End Date</td><td><?php echo $endDate?></td>
														   <td>Interest</td><td align="right"><?php echo number_format($totalInterest,2);?></td> </tr>
															<tr>  <td>Phone</td><td><?php echo $mobile;?></td> 
															<td>Amount Paid</td><td align="right"><?php echo number_format($paidAmount,2);?></td>
															</tr>
															<tr> <td>Email</td><td><?php echo $email;?></td>
															<td>Loan Balance</td><td align="right"><?php echo number_format($loanBalance,2);?></td> 
														  </tr>
														  <tr> <td>Contract</td><td><a href='<?php echo $contractPath; ?>'>Click here</a></td> 
														  <td>Fine</td><td align="right"><?php echo number_format($totalFine,2);?></td> 
														  
														  </tr>
														  <tr> <td>Security</td><td><a href='<?php echo $securityPath; ?>'>Click here</a></td> 
														  <td>Fine</td><td align="right"><?php echo number_format($totalFine,2);?></td> 
														  
														  </tr>
														   <tr><td>Add Payment</td><td><a href='../loan/loan_payment.php?id=<?php echo $loanid ?>'>Click here</a></td>
														   <td>Total Due</td><td align="right"><?php echo number_format($totalDue,2);?></td>
														   
														  </tr>
     
      
													</tbody>
                                                              
	
                                                                
                                                       
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- END EXAMPLE TABLE PORTLET-->                                          
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