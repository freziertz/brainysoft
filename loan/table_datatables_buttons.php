<?php 
require_once '../user/class.user.php';
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}
$sqlSelectSetting = ("SELECT directornumber,addressnumber,identitynumber,securitynumber,contractnumber,guarantornumber,identitydirectornumber from setting");
$stmtSetting = $user_login->runQuery($sqlSelectSetting);
$stmtSetting->execute(array());
$rowSetting = $stmtSetting->fetch();

$directorNumber = 	$rowSetting['directornumber'];
$addressNumber =  	$rowSetting['addressnumber'];
$identityNumber = 	$rowSetting['identitynumber'];
$identityDirectorNumber = 	$rowSetting['identitydirectornumber'];
$securityNumber = 	$rowSetting['securitynumber'];
$contractNumber = 	$rowSetting['contractnumber'];
$guarantorNumber = 	$rowSetting['guarantornumber'];

	$sqlSelectLoanApplications = ("SELECT party.partyid as applicantnumber, party.partytype as applicantname, concat( firstname,' ', lastname ) AS fullname, businessname
							FROM party	LEFT JOIN person ON party.partyid = person.partyid LEFT JOIN organization ON party.partyid = organization.partyid");
	$stmtApplicant = $user_login->runQuery($sqlSelectLoanApplications);
	$stmtApplicant->execute(array());

//Define page variable	
$location = "Applicant List";
$title = "Applicant List";
$breadcumb ="Applicant List";
$breadcumbDescription=" View applicant details, add identity, physical address, director and request loan";
$currentSymbo = "TZS";

include('../inc/header.php');?>
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                          <div class="row">
                                            <div class="col-md-12">
                                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption font-dark">
                                                            <i class="icon-settings font-dark"></i>
                                                            <span class="caption-subject bold uppercase">Buttons</span>
                                                        </div>
                                                        <div class="tools"> </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                                                            <thead>
                                                                <tr>
																	<th>No</th>
																	<th>Name</th>
																	<th>Address</th>
																	<th>Director</th>
																	<th>Identity</th>
																	<th>Details</th>
																	<th>Request Loan</th>                                                                  
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>No</th>
																	<th>Name</th>
																	<th>Address</th>
																	<th>Director</th>
																	<th>Identity</th>
																	<th>Details</th>
																	<th>Request Loan</th>
                                                                </tr>
                                                            </tfoot>
                                                            <tbody>
															<?php
	
	
	foreach ( $stmtApplicant->fetchAll () as $row ) {
		
		$sqlSelectIdentityCount = ("SELECT count(identityid) as identitycount FROM identity where partyid = ? ");
		$stmtLoanIdentityCount = $user_login->runQuery($sqlSelectIdentityCount);
		$stmtLoanIdentityCount->execute(array($row['applicantnumber']));
		$row2 = $stmtLoanIdentityCount->fetch();
		$identytyCount = $row2['identitycount'];
		
		$sqlSelectIdentityCountDirector = ("SELECT count(identityid) as identitycountd FROM identity,partyrole,organization where roleid = 4 and organization.partyid = ? and identity.partyid = partyrole.personpartyid");
		$stmtLoanIdentityCountDirector = $user_login->runQuery($sqlSelectIdentityCountDirector);
		$stmtLoanIdentityCountDirector->execute(array($row['applicantnumber']));
		$row3 = $stmtLoanIdentityCountDirector->fetch();
		$identityDirectorCount = $row3['identitycountd'];
		
		$sqlSelectDirectorCount = ("SELECT count(personpartyid) as directorcount FROM partyrole where roleid = 4 and partyrole.organizationpartyid = ?");
		$stmtLoanDirectorCount = $user_login->runQuery($sqlSelectDirectorCount);
		$stmtLoanDirectorCount->execute(array($row['applicantnumber']));
		$row4 = $stmtLoanDirectorCount->fetch();
		$DirectorCount = $row4['directorcount'];
		
		$sqlSelectAddressCount = ("SELECT count(physicaladdressid) as addresscount FROM physicaladdress where partyid = ? ");
		$stmtLoanAddressCount = $user_login->runQuery($sqlSelectAddressCount);
		$stmtLoanAddressCount->execute(array($row['applicantnumber']));
		$row5 = $stmtLoanAddressCount->fetch();
		$addressCount = $row5['addresscount'];
		
		echo '<tr>';
		echo '<td>'. $row['applicantnumber'] . '</td>';
		if ($row ['businessname'] == NULL){
			echo '<td>'. $row['fullname'] . '</td>';			
		}
		else{		
			echo '<td>'. $row['businessname'] . '</td>';
		}
		echo "<td><a title='Add at least two Address residence and business' class='btn btn-success btn-circle' href='physical_address_add.php?id=" . $row['applicantnumber']."&wtd='addkits'"."> address ".$addressCount."</a>";
		if ($row ['businessname'] == NULL){
			echo "<td><a title='Add at least one Director' class='btn btn-danger btn-circle disabled' href='organization_director_role_add.php?id=" . $row['applicantnumber']."&wtd='addkits'".">N/A</a>";
		}
		else{
			echo "<td><a title='Add at least one Director' class='btn btn-success btn-circle' href='organization_director_role_add.php?id=" . $row['applicantnumber']."&wtd='addkits'".">Director ".$DirectorCount."</a>";
		}
		if ($row ['businessname'] == NULL){
			
			echo "<td><a title='Add at least one Identity' class='btn btn-success btn-circle' href='identity_add.php?id=" . $row['applicantnumber']."&wtd='addkits'".">Identity ".$identytyCount."</a>";
			
		}
		else{
			echo "<td><a title='Add at least one Identity' class='btn btn-danger btn-circle disabled' href='identity_add.php?id=" . $row['applicantnumber']."&wtd='addkits'".">Identity ".$identityDirectorCount."</a>";
			
		}
		
		if ($row ['businessname'] == NULL){
			echo "<td><a title='Add at least one Director' class='btn btn-success btn-circle ' href='person_details.php?id=" . $row['applicantnumber']."&wtd='addkits'".">Details</a>";
		}
		else{
			echo "<td><a title='Add at least one Director' class='btn btn-success btn-circle' href='organization_details.php?id=" . $row['applicantnumber']."&wtd='addkits'".">Details</a>";
		}
		
		if ($row ['businessname'] == NULL){
		if (($addressCount >= $addressNumber)&& ($identytyCount >= $identityNumber) ){
			echo "<td><a title='Request Loan' class='btn btn-success btn-sm active' href='loan_application_add.php?id=" . $row['applicantnumber'] ."&='addkits'".">Request Loan</a></td>";
		}else {
			echo "<td><a title='Request Loan' class='btn btn-danger btn-sm disabled' href='loan_application_add.php?id=" . $row['applicantnumber']."&wtd='addkits'"."> Add Requirement</a></td>";
		}
		}else{
			if (($addressCount >= $addressNumber)&& ($identityDirectorCount >= $identityDirectorNumber)&& ($DirectorCount >= $directorNumber) ){
				echo "<td><a title='Request Loan' class='btn btn-success btn-sm active' href='loan_application_add.php?id=" . $row['applicantnumber'] ."&='addkits'".">Request Loan</a></td>";
			}else {
				echo "<td><a title='Request Loan' class='btn btn-danger btn-sm disabled' href='loan_application_add.php?id=" . $row['applicantnumber']."&wtd='addkits'".">Add Requirement </a></td>";
			}
			
		}
		
		echo '</tr>';
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