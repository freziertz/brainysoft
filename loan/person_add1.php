<?php 
require_once '../user/class.user.php';
require ('../inc/fileUploader.php');
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}
if ($_SERVER ["REQUEST_METHOD"] == "POST") {
	echo "test";
	if (!isset ( $_POST ["btnSave"] )) {
		
		try {
			//start transaction
			$user_login->beginTransaction();
			
			// Update party table and get party id
			$insertParty = $user_login->runQuery ( "INSERT INTO party (Partytype) VALUES (?)" );
			$insertParty->execute ( array (
					"Person" 
			) );
			//getting this person partyid
			$newPartyId = $user_login->lastID();	
			
			// Add Identity Card
			//Format file name of the identity card
			$identitypeId = $_POST ['identitytypeid'];			
			$filenumber = "ID_".$newPartyId."_".$identitypeId;
			//Begin adding Identity card			
			$identityNumber = $_POST ['identitynumber'];
			$issuedBy = $_POST ['issuedby'];
			//Format issued date
			$issuedDate = date_format ( date_create ( $_POST ['issueddate'] ), "Y-m-d" );
			//Format expiration date
			$expirationDate = date_format ( date_create ( $_POST ['expirationdate'] ), "Y-m-d" );
			//upload identity card
			$flup = new fileUploader\fileUploader ();
			$perPhoto = $flup->upload ( "brainysoft/doc/identity/",$_FILES ['identitypath'], $filenumber );
			$identitypath = $GLOBALS ['nameOfFile'];
			//Insert identity card in a database
			$insertIdentity = $user_login->runQuery ( "INSERT INTO identity (partyid,identitytypeid,identitynumber,issueddate,expiredate,issuedby,identitypath) VALUES (?,?,?,?,?,?,?)" );
			$insertIdentity->execute ( array (
					$newPartyId,
					$identitypeId,
					$identityNumber,
					$issuedDate,
					$expirationDate,
					$issuedBy,
					$identitypath,					 
			) );
			//End of adding identity card in a database
			//End of adding identity card
			
					
			//format photo file name
			$filenumber = "ph_".$newPartyId;		
			// Create new person			
			$createdDate = date ( 'Y-m-d' );
			$dateOfBirth = $_POST ['dateofbirth'];
			$date = str_replace ( '/', '-', $dateOfBirth );
			$datetoinsert = date ( 'Y-m-d', strtotime ( $date ) );
			//Upload person photo
			$flup = new fileUploader\fileUploader ();
			$perPhoto = $flup->upload ( "brainysoft/doc/userpic/",$_FILES ['photopath'], $filenumber );
			$photopath = $GLOBALS ['nameOfFile'];
			//End of uploading person photo
			//Start inserting new person into database
			$insertPerson = $user_login->runQuery ( "INSERT INTO person (partyid,title,firstname,lastname,othername,dateofbirth,nationality,tribe,gender,maritalstatus,photopath) VALUES (?,?,?,?,?,?,?,?,?,?,?)" );
			$insertPerson->execute ( array (
					$newPartyId,
					$_POST ['title'],
					$_POST ['firstname'],
					$_POST ['lastname'],
					$_POST ['middlename'],
					$datetoinsert,
					$_POST ['nationality'],
					$_POST ['tribe'],
					$_POST ['gender'],
					$_POST ['maritalstatus'],
					$photopath,
					 
			) );
			//end of inserting new person into database
			//End of creating new person	
			
				
			// Create new Physical address
				
			$partyId = $newPartyId;
			$country = $_POST ['country'];
			$region = $_POST ['region'];
			$district = $_POST ['district'];
			$ward = $_POST ['ward'];
			$localGov = $_POST ['localgov'];
			$location = $_POST ['location'];
			$street = $_POST ['street'];
			$houseNumber = $_POST ['housenumber'];
			$description = $_POST ['remarks'];
			$streetRepresentative = $_POST ['streetrepresentative'];
			$pobox = $_POST ['pobox'];
			$addressTypeId = 1;			
			
				
			$insertAddress = $user_login->runQuery ( "INSERT INTO physicaladdress (partyid,country,region,district,ward,localgov,location,street,housenumber,description,streetrepresentative,pobox,physicaladdresstypeid)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)" );
			$insertAddress->execute ( array (
					$partyId,
					$country,
					$region,
					$district,
					$ward,
					$localGov,
					$location,
					$street,
					$houseNumber,
					$description,
					$streetRepresentative,
					$pobox,
					$addressTypeId
				
			) );
			
			
			
			
			if ($user_login->commit()) {
				
				echo '<script type="text/javascript"> alert("Person Created Successfully.");</script>';
				$user_login->redirect('../loan/applicant_list.php');
			}
		} catch ( PDOException $e ) {
			$user_login->rollBack ();
			print $e->getMessage ();
			
			echo '<script type="text/javascript"> alert("Error");</script>';
		}
	}
}

			
//Define page variable	
$location = "New Person";
$title = "New Person";
$breadcumb ="Create New Person";
$breadcumbDescription=" Create new person, add address, contact and identity,";
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
                                                            <span class="caption-subject font-red bold uppercase"> Person Details -
                                                                <span class="step-title"> Step 1 of 4 </span>
                                                            </span>
                                                        </div>                                                     
                                                    </div>
                                                    <div class="portlet-body form">
                                                        <form class="form-horizontal" action="../loan/form_wizard.php" id="submit_form" method="post" enctype="multipart/form-data">
                                                            <div class="form-wizard">
                                                                <div class="form-body">
                                                                    <ul class="nav nav-pills nav-justified steps">
                                                                        <li>
                                                                            <a href="#tab1" data-toggle="tab" class="step">
                                                                                <span class="number"> 1 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Person infomation </span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#tab2" data-toggle="tab" class="step">
                                                                                <span class="number"> 2 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Physical address </span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#tab3" data-toggle="tab" class="step active">
                                                                                <span class="number"> 3 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Identity </span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#tab4" data-toggle="tab" class="step">
                                                                                <span class="number"> 4 </span>
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
                                                                            <h3 class="block">Provide your personal details</h3>
                                                                           													 
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Tribe
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="tribe" placeholder="Provide your tribe" />
                                                                                </div>
                                                                            </div>
															<div class="form-group">
                                                                <label class="control-label col-md-3">Name</label>
																 <div class="col-md-4">
                                                                <input type="text" placeholder="John Smith" class="form-control" /> 
																</div>
																</div>
                                                            
<!--**************************************************************************************************************************************************************************-->																
                                                            <div class="form-group mt-repeater">
                                                                <div data-repeater-list="group-b">
																<div class="row">
                                                                    <div class="mt-repeater-item">
                                                                        <label class="control-label col-md-3">Mobile Number
																		</label>
																		<div class="col-md-4">
																			<input type="text" placeholder="+1 646 580 DEMO (6284)" class="form-control" /> 
																		</div>
																	</div>
																	</div>
																	</br>
																	<div class="row">
                                                                    <div data-repeater-item class="mt-repeater-item mt-overflow">
																	<div class="col-md-12">
                                                                        <label class="control-label col-md-3">Additional Contact Number
																		</label>
                                                                        
																		<div class="col-md-4">
                                                                            <input type="text" placeholder="+1 646 580 DEMO (62)" class="form-control mt-repeater-input-inline" />
																			</div>
																 <div class="col-md-1">																			
                                                                            <a href="javascript:;" data-repeater-delete class="btn btn-danger mt-repeater-delete mt-repeater-del-right">
                                                                                <i class="fa fa-close"></i>
                                                                            </a>
																		</div>
                                                                        
                                                                    </div>
																	</div>
																	</div>
                                                                </div>
																 <div class="col-md-offset-3 col-md-9">
                                                                <a href="javascript:;" data-repeater-create class="btn btn-success mt-repeater-add">
                                                                    <i class="fa fa-plus"></i> Add new contact number</a>
																	</div>
                                                            </div>
<!--**************************************************************************************************************************************************************************-->	
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Occupation
																</label>
																<div class="col-md-4">
                                                                <input type="text" placeholder="Web Developer" class="form-control" /> 
																</div>
																</div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">About
																</label>
																<div class="col-md-4">
                                                                <textarea class="form-control" rows="3" placeholder="We are KeenThemes!!!"></textarea>
                                                            </div>
															</div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Website Url
																</label>
																<div class="col-md-4">
                                                                <input type="text" placeholder="http://www.mywebsite.com" class="form-control" />
																</div>																
																</div>
                                                            <div class="margin-top-10">
                                                                <a href="javascript:;" class="btn green">Save Changes </a>
                                                                <a href="javascript:;" class="btn default">Cancel </a>
                                                            </div>																			
																			
																			
                                                                        </div>
<!--END TAB 1 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 2 *******************************************************************************************************************************************************************-->
                                                                        <div class="tab-pane" id="tab2">
                                                                            <h3 class="block">Provide your physical address details</h3>
																			
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Mobile
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="mobile" placeholder="Provide your mobile number"/>                                                                                   
                                                                                </div>
                                                                            </div>                                  
                                                                          <div class="form-group">
                                                                                <label class="control-label col-md-3">Remarks</label>
                                                                                <div class="col-md-4">
                                                                                    <textarea class="form-control" rows="3" name="remarks" placeholder="Provide your remarks"></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
<!--END TAB 2 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 3 *******************************************************************************************************************************************************************-->																		
																		<div class="tab-pane" id="tab3">
                                                                            <h3 class="block">Provide your identity card details</h3>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Identity type</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="identitytypeid" id="country_list" class="form-control">
                                                                                        <option value=""></option>
                                                                                        <?php
																						  //Get identitytypeid and identitytypename to populaty identitytype																																	
																						   $sqlSelectIdentityType = ("SELECT identitytypeid,identitytypename FROM identitytype");
																						   $stmtIdentityType = $user_login->runQuery ( $sqlSelectIdentityType );
																						   $stmtIdentityType->execute ( array () );
																						   foreach ( $stmtIdentityType->fetchAll () as $row ) {
																							echo "<option  value='" . $row ['identitytypeid'] . "'>" . $row ['identitytypename'] . "</option>";
																								}
																							?>                                                                                  
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Identity Number
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="identitynumber" placeholder="Provide your identity number" />                                                                                    
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
																				<label class="control-label col-md-3">Issued Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select issued date" name="issueddate" />
																				</div>
																			</div>
																			<div class="form-group">
																				<label class="control-label col-md-3">Expired Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select expired date" name="expirationdate" />
																				</div>
																			</div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Issued By
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" placeholder="" class="form-control" name="issuedby" placeholder="Provide identity issued authority"/>
                                                                                    <span class="help-block"> </span>
                                                                                </div>
                                                                            </div> 
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">Upload Identity
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="identitypath">
																					<p class="help-block"> Select identity. </p>
																				</div>
																			</div>																			
                                                                        </div>
<!--END TAB 3 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 4 *******************************************************************************************************************************************************************-->																
                                                                        <div class="tab-pane" id="tab4">
                                                                            <h3 class="block">Confirm your details</h3>
                                                                            <h4 class="form-section">Personal Details</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Title:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="title"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">First Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="firstname"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Middle Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="middlename"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Last Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="lastname"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Date of Birth:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="dateofbirth"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Gender:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="gender"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Nationality:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="nationality"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Tribe:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="tribe"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Marital Status:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="maritalstatus"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">photopath:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="photopath"> </p>
                                                                                </div>
                                                                            </div>
																			
																			
                                                                            <h4 class="form-section">Physical Address</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Country:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="country"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Region:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="region"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">District:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="district"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Ward:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="ward"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Local Government:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="localgov"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Location:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="location"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Street:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="street"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">House Number:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="housenumber"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">P.O.Box:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="pobox"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Street Representative:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="streetrepresentative"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">email:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="email"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">mobile:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="mobile"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Remarks:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="remarks"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <h4 class="form-section">Identity Card</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Identity type:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="identitytypeid"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Identity Number:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="identitynumber"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Issued Date:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="issueddate"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Expiration Date:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="expirationdate"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Identity Path:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="identitypath"> </p>
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