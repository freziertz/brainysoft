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

$sqlSelectApplicant = ("SELECT loanid,partyid,Applicant FROM applicantview where loanid = ?");
$stmtApplicant = $user_login->runQuery($sqlSelectApplicant);
$stmtApplicant->execute(array($loanid));
$rowApplicant = $stmtApplicant->fetch();
$loanApplicant = $rowApplicant['Applicant'];
$partyId = $rowApplicant['partyid'];


//$mobile= $loanRow['phonenumber'];
//$email= $loanRow['emailaddress'];

$sqlSelectPartyType = ("SELECT partytype FROM party where partyid = ?");
$stmtPartyType = $user_login->runQuery($sqlSelectPartyType);
$stmtPartyType->execute(array($partyId));
$rowPartyType = $stmtPartyType->fetch();
$PartyType = $rowPartyType['partytype'];

$sqlSelectPartyType = ("SELECT partyid,partytype FROM party where partyid = ?");
$stmtPartyType = $user_login->runQuery($sqlSelectPartyType);
$stmtPartyType->execute(array($partyId));
$rowPartyType = $stmtPartyType->fetch();
$PartyType = $rowPartyType['partytype'];






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



//Start Person details

if ($PartyType == "Person"){
	

$sqlSelectPerson = ("SELECT partyid, CONCAT(title,'. ',firstname,' ',othername,' ',lastname) as fullname,dateofbirth,nationality,religion,tribe,gender,maritalstatus,photopath,createddate FROM person WHERE partyid =?");
$stmtPerson = $user_login->runQuery($sqlSelectPerson);
$stmtPerson->execute(array($partyId));
$personRow = $stmtPerson->fetch();
$loanApplicant = $personRow['fullname'];
$dateofbirth = $personRow['dateofbirth'];
$nationality = $personRow['nationality'];
$religion = $personRow['religion'];
$tribe = $personRow['tribe'];
$gender = $personRow['gender'];
$maritalstatus = $personRow['maritalstatus'];
$photopath = $personRow['photopath'];
$createddate = $personRow['createddate'];




$sqlSelectPhysicalAddressResidence = ("SELECT physicaladdressid,location,street,housenumber FROM physicaladdress,loan  where physicaladdresstypeid = 1 and loanid = ? and loan.partyid = physicaladdress.partyid ");
$stmtLoanPhysicalAddressResidence = $user_login->runQuery($sqlSelectPhysicalAddressResidence);
$stmtLoanPhysicalAddressResidence->execute(array($partyId));
$rowPhR = $stmtLoanPhysicalAddressResidence -> fetch();
$locationResident = $rowPhR['location'];
$streetResident = $rowPhR['street'];
$houseNumberResident = $rowPhR['housenumber'];
$countryResident = $rowPhR['country'];
$regionResident = $rowPhR['region'];
$districtResident = $rowPhR['district'];
$wardResident= $rowPhR['ward'];
$localgovResident = $rowPhR['localgov'];
$poboxResident= $rowPhR['pobox'];
$streetrepresentativeResident = $rowPhR['streetrepresentative'];
$descriptionResident = $rowPhR['description'];



$sqlSelectPhysicalAddressBusiness = ("SELECT physicaladdressid,location,street,housenumber,country,region,district,ward,localgov,pobox,streetrepresentative,description FROM physicaladdress  where physicaladdresstypeid = 2 and partyid = ? ");
$stmtLoanPhysicalAddressBusiness = $user_login->runQuery($sqlSelectPhysicalAddressBusiness);
$stmtLoanPhysicalAddressBusiness->execute(array($partyId));
$rowPhB= $stmtLoanPhysicalAddressBusiness -> fetch();
$locationBusiness = $rowPhB['location'];
$streetBusiness = $rowPhB['street'];
$houseNumberBusiness = $rowPhB['housenumber'];
$countryBusiness = $rowPhB['country'];
$regionBusiness = $rowPhB['region'];
$districtBusiness = $rowPhB['district'];
$wardBusiness= $rowPhB['ward'];
$localgovBusiness = $rowPhB['localgov'];
$poboxBusiness= $rowPhB['pobox'];
$streetrepresentativeBusiness = $rowPhB['streetrepresentative'];
$descriptionBusiness = $rowPhB['description'];


$sqlSelectLoanHistory = ("SELECT  loanid,issueddate,(loanbalance + totalfine) as loanamount FROM  loan where loanstatus = 5 and partyid = ? ");
$stmtLoanHistory = $user_login->runQuery($sqlSelectLoanHistory);
$stmtLoanHistory->execute(array($partyId));


//End this person info
//Start business info
}else{
$sqlSelectOrganization = ("SELECT partyid,businessname, registrationnumber,tin,vrn, registrationdate,businessregistrationpath,tinpath,vrnpath,memorundumpath,licensepath,logopath FROM organization where partyid = ?");
$stmtOrganization = $user_login->runQuery($sqlSelectOrganization);
$stmtOrganization->execute(array($partyId));
$organizationRow = $stmtOrganization->fetch();
$organizationName = $organizationRow['businessname'];
$registrationNumber = $organizationRow['registrationnumber'];
$tin = $organizationRow['tin'];
$vrn = $organizationRow['vrn'];
$registrationDate = $organizationRow['registrationdate'];
$registrationPath = $organizationRow['businessregistrationpath'];
$tinPath = $organizationRow['tinpath'];
$vrnPath = $organizationRow['vrnpath'];
$memorundumPath = $organizationRow['memorundumpath'];
$licensePath = $organizationRow['licensepath'];
$logoPath = $organizationRow['logopath'];


$sqlSelectPhysicalAddressBusiness = ("SELECT physicaladdressid,location,street,housenumber,country,region,district,ward,localgov,pobox,streetrepresentative,description FROM physicaladdress  where physicaladdresstypeid = 2 and partyid = ? ");
$stmtLoanPhysicalAddressBusiness = $user_login->runQuery($sqlSelectPhysicalAddressBusiness);
$stmtLoanPhysicalAddressBusiness->execute(array($partyId));
$rowPhB= $stmtLoanPhysicalAddressBusiness -> fetch();
$locationBusiness = $rowPhB['location'];
$streetBusiness = $rowPhB['street'];
$houseNumberBusiness = $rowPhB['housenumber'];
$countryBusiness = $rowPhB['country'];
$regionBusiness = $rowPhB['region'];
$districtBusiness = $rowPhB['district'];
$wardBusiness= $rowPhB['ward'];
$localgovBusiness = $rowPhB['localgov'];
$poboxBusiness= $rowPhB['pobox'];
$streetrepresentativeBusiness = $rowPhB['streetrepresentative'];
$descriptionBusiness = $rowPhB['description'];

$sqlSelectPerson = ("SELECT  personpartyid, CONCAT(firstname,' ',lastname) as fullname FROM  partyrole, person where  partyrole.organizationpartyid = ? and roleid=4 and person.partyid = partyrole.personpartyid");
$stmtPerson = $user_login->runQuery($sqlSelectPerson);
$stmtPerson->execute(array($partyId));

$sqlSelectLoanHistory = ("SELECT  loanid,issueddate,(loanbalance + totalfine) as loanamount FROM  loan where  loanstatus=5 and partyid = ? ");
$stmtLoanHistory = $user_login->runQuery($sqlSelectLoanHistory);
$stmtLoanHistory->execute(array($partyId));


/* $sqlSelectLoanDirector = ("SELECT loanrole.partyid, CONCAT(firstname,' ',lastname) as fullname FROM loanrole, person where loanid = ? and roleid=4 and loanrole.partyid = person.partyid");
$stmtLoanLoanDirector = $user_login->runQuery($sqlSelectLoanDirector);
$stmtLoanLoanDirector->execute(array($partyId)); */


$sqlSelectSecurity = ("SELECT securitypath FROM loansecurity where loanid = ? ");
$stmtLoanSecurity = $user_login->runQuery($sqlSelectSecurity);
$stmtLoanSecurity->execute(array($partyId));

	
}




	

		

//Define page variable	
$location = "Client details";
$title = "Details for ".$loanApplicant;
$breadcumb ="Details for ".$loanApplicant;
$breadcumbDescription=" View client details, addresses";
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
															<?php if($PartyType == "Person"){ ?>
														  
															 <tr><td>Date of birth</td><td><?php echo $dateofbirth;?></td>
															  <td>Nationality</td><td align="right"><?php echo $nationality; ?></td> </tr>
															   <tr> <td>Gender</td><td><?php echo $gender; ?></td> 
															   <td>Tribe</td><td align="right"><?php echo $tribe;?></td>
															   </tr>
															   <tr> <td>Marital Status</td><td><?php echo $maritalstatus?></td>
															   <td>Religion</td><td align="right"><?php echo $religion;?></td> </tr>
																<tr>  <td>Created Date</td><td><?php echo $createddate;?></td> 
																<td>Loan Details</td><td align="right"><a href='../loan/loan_details.php?id=<?php echo $loanid ?>'>Click here</a></td>
																</tr>
																</tbody>
  </table>  
																
<div class="col-sm-4">															                  
 <div class="panel panel-info">
    <div class="panel-heading">Business Address</div>
    <div class="panel-body"><div>   
<table class="table table-striped table-condensed">   
    <tbody>
     
       
       <?php 
	   
	   echo '<tr><td>Location</td><td>'.$locationBusiness.'</td> </tr>';
	   echo '<tr><td>Street</td><td>'.$streetBusiness.'</td> </tr>';
	   echo '<tr><td>House No/Building </td><td>'.$houseNumberBusiness.'</td> </tr>';
	   echo '<tr><td>P.O.Box</td><td>'.$poboxBusiness.'</td> </tr>';
	   echo '<tr><td>Street Rep.</td><td>'.$streetrepresentativeBusiness.'</td> </tr>';	 
	   echo '<tr><td>Ward</td><td>'.$wardBusiness.'</td> </tr>';
	   echo '<tr><td>District</td><td>'.$districtBusiness.'</td> </tr>';
	   echo '<tr><td>Region</td><td>'.$regionBusiness.'</td> </tr>';
	   echo '<tr><td>Country</td><td>'.$countryBusiness.'</td> </tr>';
	   echo '<tr><td>Description</td><td>'.$descriptionBusiness.'</td> </tr>';
       
       
       ?>
      
          
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
  
  
     
              


 <div class="col-sm-4">
 <div class="panel panel-info">
    <div class="panel-heading">Resident Address</div>
    <div class="panel-body"><div>   
<table class="table table-striped table-condensed">   
    <tbody>
     
       
       <?php 
	   
	   echo '<tr><td>Location</td><td>'.$locationResident.'</td> </tr>';
	   echo '<tr><td>Street</td><td>'.$streetResident.'</td> </tr>';
	   echo '<tr><td>House No/Building </td><td>'.$houseNumberResident.'</td> </tr>';
	   echo '<tr><td>P.O.Box</td><td>'.$poboxResident.'</td> </tr>';
	   echo '<tr><td>Street Rep.</td><td>'.$streetrepresentativeResident.'</td> </tr>';	 
	   echo '<tr><td>Ward</td><td>'.$wardResident.'</td> </tr>';
	   echo '<tr><td>District</td><td>'.$districtResident.'</td> </tr>';
	   echo '<tr><td>Region</td><td>'.$regionResident.'</td> </tr>';
	   echo '<tr><td>Country</td><td>'.$countryResident.'</td> </tr>';
	   echo '<tr><td>Description</td><td>'.$descriptionResident.'</td> </tr>';
       
       
       ?>
      
          
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
				 
				 
				 
				  <div class="col-sm-4">
 <div class="panel panel-warning">
    <div class="panel-heading">Loan History</div>
    <div class="panel-body"><div>   
<table class="table table-striped table-condensed">
   
   
    <tbody>
	<tr><td>Loan#<td/><td>Issued Date<td/><td align="right">Amount Due</a></td> </tr>
	<?php 
	   foreach ( $stmtLoanHistory->fetchAll () as $row ) {
		if (isset($row['loanid'])){
       	$loanid = $row['loanid'];
		
		echo '<tr><td> <a href="loan_details.php?id='.$row['loanid'].'">'.$loanid.'<td/><td> '.$row['issueddate'].'<td/><td align="right"> '.number_format($row['loanamount'],2).'</a></td> </tr>';
		
		
		}       
       
       }
       ?>
     
        
       
       
         
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
  
                
 
                 </div>
 



                 
                 
                 
                 
                 
                   </div>
																
																
																
															<?php }else{ ?>
															<tbody>
															 <tr><td>Organization Name</td><td><?php echo $organizationName;?></td>
															  <td>Registration Number</td><td align="right"><?php echo $registrationNumber;?></td> </tr>
															   <tr> <td>Tin</td><td><?php echo $tin?></td> 
															   <td>Vrn</td><td align="right"><?php echo $vrn;?></td>
															   </tr>
															   <tr> <td>Registration Date</td><td><?php echo $registrationDate?></td>
															   <td>Registration Copy</td><td align="right">
															   <?php 
															   if (isset($registrationPath)){
																$path = "../doc/docpath/certificate/".$registrationPath;
															   echo '<a href='.$path.'>Certificate of Incoparation</a>';
															   }else {
															   echo "No Registration Copy";
															   }
															   ?></td> </tr>
																<tr>  <td>Tin Copy</td><td>
																<?php 
																if (isset($tinPath)){
																$path = "../doc/docpath/certificate/".$tinPath;
															   echo '<a href='.$path.'>Tin</a>';
															   }else {
															   echo "No Tin Copy";
															   }?></td> 
																<td>Vrn Copy</td><td align="right"><?php
																if (isset($vrnPath)){
																	$path = "../doc/docpath/certificate/".$vrnPath;
																	echo '<a href='.$path.'>Vrn</a>';
																}else {
																	echo "No Vrn";
																}
																?></td>
																</tr>
																<tr> <td>Memorundum</td><td>
																<?php
																if (isset($memorundumPath)){
																	$path = "../doc/docpath/certificate/".$memorundumPath;
																	echo '<a href='.$path.'>Memorundum</a>';
																}else {
																	echo "No Memorundum";
																}
																?></td>
																<td>License</td><td align="right">
																<?php 
																if (isset($licensePath)){
																	$path = "../doc/docpath/certificate/".$licensePath;
																	echo '<a href='.$path.'>Business License</a>';
																}else {
																	echo "No Licence";
																}
																?>
																</td> 
															  </tr>
															  
															  </tbody>
                                                              
	
                                                                
                                                       
                                                        </table>
														
														<div class="col-sm-4">
 <div class="panel panel-info">
    <div class="panel-heading">Business Address</div>
    <div class="panel-body"><div>   
<table class="table table-striped table-condensed">   
    <tbody>
     
       
       <?php 
	   
	   echo '<tr><td>Location</td><td>'.$locationBusiness.'</td> </tr>';
	   echo '<tr><td>Street</td><td>'.$streetBusiness.'</td> </tr>';
	   echo '<tr><td>House No/Building </td><td>'.$houseNumberBusiness.'</td> </tr>';
	   echo '<tr><td>P.O.Box</td><td>'.$poboxBusiness.'</td> </tr>';
	   echo '<tr><td>Street Rep.</td><td>'.$streetrepresentativeBusiness.'</td> </tr>';	 
	   echo '<tr><td>Ward</td><td>'.$wardBusiness.'</td> </tr>';
	   echo '<tr><td>District</td><td>'.$districtBusiness.'</td> </tr>';
	   echo '<tr><td>Region</td><td>'.$regionBusiness.'</td> </tr>';
	   echo '<tr><td>Country</td><td>'.$countryBusiness.'</td> </tr>';
	   echo '<tr><td>Description</td><td>'.$descriptionBusiness.'</td> </tr>';
       
       
       ?>
      
          
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
  


 <div class="col-sm-3">
 <div class="panel panel-success">
    <div class="panel-heading">Directors</div>
    <div class="panel-body"><div>   
<table class="table table-striped table-condensed">   
    <tbody>
     
       <?php 
       foreach ( $stmtPerson->fetchAll () as $row ) {
		if (isset($row['fullname'])){
       	$name = $row['fullname'];
		echo '<tr><td> <a href="../loan/applicant_details.php?id='.$row['personpartyid'].'">'.$name.'</a></td> </tr>';
		
	
		
		}       
       
       }
       ?>
         
    </tbody>
  </table>              
      </div>
  </div>
  </div>
                   
                </div>
                
 <div class="col-sm-5">
 <div class="panel panel-warning">
    <div class="panel-heading">Loan History</div>
    <div class="panel-body"><div>   
<table class="table table-striped table-condensed">
   
   
    <tbody>
	<tr><td>Loan#</td><td>Issued Date</td><td align="right">Amount Due</td> </tr>
	<?php 
	   foreach ( $stmtLoanHistory->fetchAll () as $row ) {
		if (isset($row['loanid'])){
       	$loanid = $row['loanid'];
		
		echo '<tr><td> <a href="loan_details.php?id='.$row['loanid'].'">'.$loanid.'</a></td><td> '.$row['issueddate'].'</td><td align="right"> '.number_format($row['loanamount'],2).'</td> </tr>';
		
		
		
		}       
       
       }
       ?>
     
        
       
       
         
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
 


                 
                 
                 
                 
                  </div>
                   </div>
				   </div>
 
														  
															<?php } ?>
															
															
																
																													
     
      
													
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