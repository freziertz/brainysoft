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
	
	if (isset ( $_POST ["btnSave"] )) {
		
		try {
			//start transaction
			$user_login->beginTransaction();
			
			// Update party table and get party id		
			$insertPartyForOrganization = $user_login->runQuery ( "INSERT INTO party (Partytype) VALUES (?)" );
			$insertPartyForOrganization->execute ( array (
					"Organization" 
			) );
			//getting this Organization partyid
			$newPartyId = $user_login->lastID();	
			
			// Add Identity Card
			
			//End of adding identity card in a database
			//End of adding identity card
			
					
			//format photo file name
			$filenumber = "ph_".$newPartyId;		
			// Create new Organization	
			//Format registration date
			$registrationdate = $_POST ['registrationdate'];
			$date = str_replace ( '/', '-', $registrationdate );
			$registrationdate1 = date ( 'Y-m-d', strtotime ( $date ) );
			//End format registration date	
			
			//Create Upload Object
			$flup = new fileUploader\fileUploader();
			//End Create Upload Object
			
			//Start Upload TIN Copy
			if (isset ( $_FILES ['tincopy'] )) {
			$filenumber = $newPartyId."_tin";
			$perPhoto = $flup->upload ( "brainysoft/doc/docpath/certificate/",$_FILES ['tincopy'], $filenumber );
			$tinpath = $GLOBALS ['nameOfFile'];
			}
			//End Upload TIN			
			//Start Upload Registration Copy
			if (isset ( $_FILES ['registrationcopy'] )) {
			$filenumber = $newPartyId."_incorporation";
			$perPhoto = $flup->upload ( "brainysoft/doc/docpath/certificate/",$_FILES ['registrationcopy'], $filenumber );
			$registrationpath = $GLOBALS ['nameOfFile'];
			}
			//End Upload Registration Copy
			//Start Upload VRN Copy
			if (isset ( $_FILES ['vrncopy'] )) {
			$filenumber = $newPartyId."_vrn";
			$perPhoto = $flup->upload ( "brainysoft/doc/docpath/certificate/",$_FILES ['vrncopy'], $filenumber );
			$vrnpathpath = $GLOBALS ['nameOfFile'];
			}
			//End Upload VRN Copy
			//Start Upload Licence Copy
			if (isset ( $_FILES ['licensecopy'] )) {
			$filenumber = $newPartyId."_license";
			$perPhoto = $flup->upload ( "brainysoft/doc/docpath/certificate/",$_FILES ['licensecopy'], $filenumber );
			$licensepath = $GLOBALS ['nameOfFile'];
			}
			//End Upload Licence Copy
			//Start Upload Memorundum Copy
			if (isset ( $_FILES ['memorundumcopy'] )) {
			$filenumber = $newPartyId."_memorundum";
			$perPhoto = $flup->upload ( "brainysoft/doc/docpath/certificate/",$_FILES ['memorundumcopy'], $filenumber );
			$memorundumpath = $GLOBALS ['nameOfFile'];
			}
			//End Upload Memorundum Copy
			//Start Upload Logo
			if (isset ( $_FILES ['logocopy'] )) {
			$filenumber = $newPartyId."_logo";
			$perPhoto = $flup->upload ( "brainysoft/doc/docpath/certificate/",$_FILES ['logocopy'], $filenumber );
			$logopath = $GLOBALS ['nameOfFile'];
			}
			//End Upload Logo
			//Start Inserting Organization into the Database
			$insertOrganization = $user_login->runQuery ( "INSERT INTO organization (partyid,businessname,registrationnumber,tin,vrn,registrationdate,businessregistrationpath,tinpath,vrnpath,memorundumpath,licensepath,organizationtype,logopath) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)" );
			$insertOrganization->execute ( array (
					$newPartyId,
					$_POST ['organizationname'],
					$_POST ['registrationnumber'],					
					$_POST ['tin'],
					$_POST ['vrn'],
					$registrationdate1,
					$registrationpath,
					$tinpath,
					$vrnpathpath,
					$memorundumpath,
					$licensepath,					
					$_POST ['organizationtypeid'],
					$logopath			
			) );			
			//Start Inserting Organization into the Database
			//End of creating new Organization
			
			//Create new Physical address				
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
			$addressTypeId = 2;			
			
			//Start Inserting Physical Address Organization into the Database	
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
			//End Inserting Physical Address Organization into the Database

			//Start Create director role for organization
			$directorPartyId =  $_POST ["directorid"];
			$roleid = 4;
			$insertLoanRole = $user_login->runQuery("INSERT INTO partyrole(organizationpartyid,personpartyid,roleid) VALUES (?,?,?)");
			$inserted = $insertLoanRole->execute(array($partyId,$directorPartyId,$roleid));
			//End Create director role for organization			
			
			
			
			//If Everything Go Well, Commit
			if ($user_login->commit()) {				
				echo '<script type="text/javascript"> alert("Organization Created Successfully.");</script>';
				$user_login->redirect('../loan/organization_list.php');
				}
		} catch ( PDOException $e ) {
			//If there anything wrong rollback
			$user_login->rollBack ();
			print $e->getMessage ();			
			echo '<script type="text/javascript"> alert("Error");</script>';
		}
	}
}

			
//Define page variable	
$location = "New Organization";
$title = "New Organization";
$breadcumb ="Create New Organization";
$breadcumbDescription=" Create new Organization, add address, contact and directors,";
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
                                                            <span class="caption-subject font-red bold uppercase"> Organization Details -
                                                                <span class="step-title"> Step 1 of 4 </span>
                                                            </span>
                                                        </div>                                                     
                                                    </div>
                                                    <div class="portlet-body form">
                                                        <form class="form-horizontal" action="../loan/organization_add.php" id="submit_form" method="post" enctype="multipart/form-data">
                                                            <div class="form-wizard">
                                                                <div class="form-body">
                                                                    <ul class="nav nav-pills nav-justified steps">
                                                                        <li>
                                                                            <a href="#tab1" data-toggle="tab" class="step">
                                                                                <span class="number"> 1 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Organization infomation </span>
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
                                                                                    <i class="fa fa-check"></i> Directors </span>
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
                                                                            <h3 class="block">Provide your Organizational details</h3>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Organization Name
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="organizationname" placeholder="Provide organization name" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Organization Type
																				 <span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="organizationtypeid" id="country_list" class="form-control">
																						<option value=""></option>																					
                                                                                        <option value="3">Sole propriate</option>
																						<option value="1">Incorparated</option>
																						<option value="2">Partner</option>                                                                             
                                                                                    </select>
                                                                                </div>
                                                                            </div>
																			
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Registration Number
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="registrationnumber" placeholder="Provide registration number" />
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">TIN Number
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="tin" placeholder="Provide organization TIN number" />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">VRN
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="vrn" placeholder="Provide VAT registration number" />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
																				<label class="control-label col-md-3">Registration Date
																					<span class=""> </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="registrationdate" />
																				</div>
																			</div>																			
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">Registration copy
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="registrationcopy">
																					<p class="help-block"> Select copy of registration. </p>
																				</div>
																			</div>
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">TIN copy
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="tincopy">
																					<p class="help-block"> Select copy of TIN. </p>
																				</div>
																			</div>																			
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">VRN copy
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="vrncopy">
																					<p class="help-block"> Select copy of VAT registration. </p>
																				</div>
																			</div>
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">License copy
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="licensecopy">
																					<p class="help-block"> Select copy of business licence. </p>
																				</div>
																			</div>
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">Memorundum copy
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="memorundumcopy">
																					<p class="help-block"> Select copy of Memorundum. </p>
																				</div>
																			</div>
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">Logo copy
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="logocopy">
																					<p class="help-block"> Select copy of organization logo. </p>
																				</div>
																			</div>
                                                                        </div>
<!--END TAB 1 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 2 *******************************************************************************************************************************************************************-->
                                                                        <div class="tab-pane" id="tab2">
                                                                            <h3 class="block">Provide your physical address details</h3>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Country
																				 <span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="country" id="country_list" class="form-control">
                                                                                        <option value=""></option>
                                                                                        <option value="AF">Afghanistan</option>
                                                                                        <option value="AL">Albania</option>
                                                                                        <option value="DZ">Algeria</option>
                                                                                        <option value="AS">American Samoa</option>
                                                                                        <option value="AD">Andorra</option>
                                                                                        <option value="AO">Angola</option>
                                                                                        <option value="AI">Anguilla</option>
                                                                                        <option value="AR">Argentina</option>
                                                                                        <option value="AM">Armenia</option>
                                                                                        <option value="AW">Aruba</option>
                                                                                        <option value="AU">Australia</option>
                                                                                        <option value="AT">Austria</option>
                                                                                        <option value="AZ">Azerbaijan</option>
                                                                                        <option value="BS">Bahamas</option>
                                                                                        <option value="BH">Bahrain</option>
                                                                                        <option value="BD">Bangladesh</option>
                                                                                        <option value="BB">Barbados</option>
                                                                                        <option value="BY">Belarus</option>
                                                                                        <option value="BE">Belgium</option>
                                                                                        <option value="BZ">Belize</option>
                                                                                        <option value="BJ">Benin</option>
                                                                                        <option value="BM">Bermuda</option>
                                                                                        <option value="BT">Bhutan</option>
                                                                                        <option value="BO">Bolivia</option>
                                                                                        <option value="BA">Bosnia and Herzegowina</option>
                                                                                        <option value="BW">Botswana</option>
                                                                                        <option value="BV">Bouvet Island</option>
                                                                                        <option value="BR">Brazil</option>
                                                                                        <option value="IO">British Indian Ocean Territory</option>
                                                                                        <option value="BN">Brunei Darussalam</option>
                                                                                        <option value="BG">Bulgaria</option>
                                                                                        <option value="BF">Burkina Faso</option>
                                                                                        <option value="BI">Burundi</option>
                                                                                        <option value="KH">Cambodia</option>
                                                                                        <option value="CM">Cameroon</option>
                                                                                        <option value="CA">Canada</option>
                                                                                        <option value="CV">Cape Verde</option>
                                                                                        <option value="KY">Cayman Islands</option>
                                                                                        <option value="CF">Central African Republic</option>
                                                                                        <option value="TD">Chad</option>
                                                                                        <option value="CL">Chile</option>
                                                                                        <option value="CN">China</option>
                                                                                        <option value="CX">Christmas Island</option>
                                                                                        <option value="CC">Cocos (Keeling) Islands</option>
                                                                                        <option value="CO">Colombia</option>
                                                                                        <option value="KM">Comoros</option>
                                                                                        <option value="CG">Congo</option>
                                                                                        <option value="CD">Congo, the Democratic Republic of the</option>
                                                                                        <option value="CK">Cook Islands</option>
                                                                                        <option value="CR">Costa Rica</option>
                                                                                        <option value="CI">Cote d'Ivoire</option>
                                                                                        <option value="HR">Croatia (Hrvatska)</option>
                                                                                        <option value="CU">Cuba</option>
                                                                                        <option value="CY">Cyprus</option>
                                                                                        <option value="CZ">Czech Republic</option>
                                                                                        <option value="DK">Denmark</option>
                                                                                        <option value="DJ">Djibouti</option>
                                                                                        <option value="DM">Dominica</option>
                                                                                        <option value="DO">Dominican Republic</option>
                                                                                        <option value="EC">Ecuador</option>
                                                                                        <option value="EG">Egypt</option>
                                                                                        <option value="SV">El Salvador</option>
                                                                                        <option value="GQ">Equatorial Guinea</option>
                                                                                        <option value="ER">Eritrea</option>
                                                                                        <option value="EE">Estonia</option>
                                                                                        <option value="ET">Ethiopia</option>
                                                                                        <option value="FK">Falkland Islands (Malvinas)</option>
                                                                                        <option value="FO">Faroe Islands</option>
                                                                                        <option value="FJ">Fiji</option>
                                                                                        <option value="FI">Finland</option>
                                                                                        <option value="FR">France</option>
                                                                                        <option value="GF">French Guiana</option>
                                                                                        <option value="PF">French Polynesia</option>
                                                                                        <option value="TF">French Southern Territories</option>
                                                                                        <option value="GA">Gabon</option>
                                                                                        <option value="GM">Gambia</option>
                                                                                        <option value="GE">Georgia</option>
                                                                                        <option value="DE">Germany</option>
                                                                                        <option value="GH">Ghana</option>
                                                                                        <option value="GI">Gibraltar</option>
                                                                                        <option value="GR">Greece</option>
                                                                                        <option value="GL">Greenland</option>
                                                                                        <option value="GD">Grenada</option>
                                                                                        <option value="GP">Guadeloupe</option>
                                                                                        <option value="GU">Guam</option>
                                                                                        <option value="GT">Guatemala</option>
                                                                                        <option value="GN">Guinea</option>
                                                                                        <option value="GW">Guinea-Bissau</option>
                                                                                        <option value="GY">Guyana</option>
                                                                                        <option value="HT">Haiti</option>
                                                                                        <option value="HM">Heard and Mc Donald Islands</option>
                                                                                        <option value="VA">Holy See (Vatican City State)</option>
                                                                                        <option value="HN">Honduras</option>
                                                                                        <option value="HK">Hong Kong</option>
                                                                                        <option value="HU">Hungary</option>
                                                                                        <option value="IS">Iceland</option>
                                                                                        <option value="IN">India</option>
                                                                                        <option value="ID">Indonesia</option>
                                                                                        <option value="IR">Iran (Islamic Republic of)</option>
                                                                                        <option value="IQ">Iraq</option>
                                                                                        <option value="IE">Ireland</option>
                                                                                        <option value="IL">Israel</option>
                                                                                        <option value="IT">Italy</option>
                                                                                        <option value="JM">Jamaica</option>
                                                                                        <option value="JP">Japan</option>
                                                                                        <option value="JO">Jordan</option>
                                                                                        <option value="KZ">Kazakhstan</option>
                                                                                        <option value="KE">Kenya</option>
                                                                                        <option value="KI">Kiribati</option>
                                                                                        <option value="KP">Korea, Democratic People's Republic of</option>
                                                                                        <option value="KR">Korea, Republic of</option>
                                                                                        <option value="KW">Kuwait</option>
                                                                                        <option value="KG">Kyrgyzstan</option>
                                                                                        <option value="LA">Lao People's Democratic Republic</option>
                                                                                        <option value="LV">Latvia</option>
                                                                                        <option value="LB">Lebanon</option>
                                                                                        <option value="LS">Lesotho</option>
                                                                                        <option value="LR">Liberia</option>
                                                                                        <option value="LY">Libyan Arab Jamahiriya</option>
                                                                                        <option value="LI">Liechtenstein</option>
                                                                                        <option value="LT">Lithuania</option>
                                                                                        <option value="LU">Luxembourg</option>
                                                                                        <option value="MO">Macau</option>
                                                                                        <option value="MK">Macedonia, The Former Yugoslav Republic of</option>
                                                                                        <option value="MG">Madagascar</option>
                                                                                        <option value="MW">Malawi</option>
                                                                                        <option value="MY">Malaysia</option>
                                                                                        <option value="MV">Maldives</option>
                                                                                        <option value="ML">Mali</option>
                                                                                        <option value="MT">Malta</option>
                                                                                        <option value="MH">Marshall Islands</option>
                                                                                        <option value="MQ">Martinique</option>
                                                                                        <option value="MR">Mauritania</option>
                                                                                        <option value="MU">Mauritius</option>
                                                                                        <option value="YT">Mayotte</option>
                                                                                        <option value="MX">Mexico</option>
                                                                                        <option value="FM">Micronesia, Federated States of</option>
                                                                                        <option value="MD">Moldova, Republic of</option>
                                                                                        <option value="MC">Monaco</option>
                                                                                        <option value="MN">Mongolia</option>
                                                                                        <option value="MS">Montserrat</option>
                                                                                        <option value="MA">Morocco</option>
                                                                                        <option value="MZ">Mozambique</option>
                                                                                        <option value="MM">Myanmar</option>
                                                                                        <option value="NA">Namibia</option>
                                                                                        <option value="NR">Nauru</option>
                                                                                        <option value="NP">Nepal</option>
                                                                                        <option value="NL">Netherlands</option>
                                                                                        <option value="AN">Netherlands Antilles</option>
                                                                                        <option value="NC">New Caledonia</option>
                                                                                        <option value="NZ">New Zealand</option>
                                                                                        <option value="NI">Nicaragua</option>
                                                                                        <option value="NE">Niger</option>
                                                                                        <option value="NG">Nigeria</option>
                                                                                        <option value="NU">Niue</option>
                                                                                        <option value="NF">Norfolk Island</option>
                                                                                        <option value="MP">Northern Mariana Islands</option>
                                                                                        <option value="NO">Norway</option>
                                                                                        <option value="OM">Oman</option>
                                                                                        <option value="PK">Pakistan</option>
                                                                                        <option value="PW">Palau</option>
                                                                                        <option value="PA">Panama</option>
                                                                                        <option value="PG">Papua New Guinea</option>
                                                                                        <option value="PY">Paraguay</option>
                                                                                        <option value="PE">Peru</option>
                                                                                        <option value="PH">Philippines</option>
                                                                                        <option value="PN">Pitcairn</option>
                                                                                        <option value="PL">Poland</option>
                                                                                        <option value="PT">Portugal</option>
                                                                                        <option value="PR">Puerto Rico</option>
                                                                                        <option value="QA">Qatar</option>
                                                                                        <option value="RE">Reunion</option>
                                                                                        <option value="RO">Romania</option>
                                                                                        <option value="RU">Russian Federation</option>
                                                                                        <option value="RW">Rwanda</option>
                                                                                        <option value="KN">Saint Kitts and Nevis</option>
                                                                                        <option value="LC">Saint LUCIA</option>
                                                                                        <option value="VC">Saint Vincent and the Grenadines</option>
                                                                                        <option value="WS">Samoa</option>
                                                                                        <option value="SM">San Marino</option>
                                                                                        <option value="ST">Sao Tome and Principe</option>
                                                                                        <option value="SA">Saudi Arabia</option>
                                                                                        <option value="SN">Senegal</option>
                                                                                        <option value="SC">Seychelles</option>
                                                                                        <option value="SL">Sierra Leone</option>
                                                                                        <option value="SG">Singapore</option>
                                                                                        <option value="SK">Slovakia (Slovak Republic)</option>
                                                                                        <option value="SI">Slovenia</option>
                                                                                        <option value="SB">Solomon Islands</option>
                                                                                        <option value="SO">Somalia</option>
                                                                                        <option value="ZA">South Africa</option>
                                                                                        <option value="GS">South Georgia and the South Sandwich Islands</option>
                                                                                        <option value="ES">Spain</option>
                                                                                        <option value="LK">Sri Lanka</option>
                                                                                        <option value="SH">St. Helena</option>
                                                                                        <option value="PM">St. Pierre and Miquelon</option>
                                                                                        <option value="SD">Sudan</option>
                                                                                        <option value="SR">Suriname</option>
                                                                                        <option value="SJ">Svalbard and Jan Mayen Islands</option>
                                                                                        <option value="SZ">Swaziland</option>
                                                                                        <option value="SE">Sweden</option>
                                                                                        <option value="CH">Switzerland</option>
                                                                                        <option value="SY">Syrian Arab Republic</option>
                                                                                        <option value="TW">Taiwan, Province of China</option>
                                                                                        <option value="TJ">Tajikistan</option>
                                                                                        <option value="TZ">Tanzania, United Republic of</option>
                                                                                        <option value="TH">Thailand</option>
                                                                                        <option value="TG">Togo</option>
                                                                                        <option value="TK">Tokelau</option>
                                                                                        <option value="TO">Tonga</option>
                                                                                        <option value="TT">Trinidad and Tobago</option>
                                                                                        <option value="TN">Tunisia</option>
                                                                                        <option value="TR">Turkey</option>
                                                                                        <option value="TM">Turkmenistan</option>
                                                                                        <option value="TC">Turks and Caicos Islands</option>
                                                                                        <option value="TV">Tuvalu</option>
                                                                                        <option value="UG">Uganda</option>
                                                                                        <option value="UA">Ukraine</option>
                                                                                        <option value="AE">United Arab Emirates</option>
                                                                                        <option value="GB">United Kingdom</option>
                                                                                        <option value="US">United States</option>
                                                                                        <option value="UM">United States Minor Outlying Islands</option>
                                                                                        <option value="UY">Uruguay</option>
                                                                                        <option value="UZ">Uzbekistan</option>
                                                                                        <option value="VU">Vanuatu</option>
                                                                                        <option value="VE">Venezuela</option>
                                                                                        <option value="VN">Viet Nam</option>
                                                                                        <option value="VG">Virgin Islands (British)</option>
                                                                                        <option value="VI">Virgin Islands (U.S.)</option>
                                                                                        <option value="WF">Wallis and Futuna Islands</option>
                                                                                        <option value="EH">Western Sahara</option>
                                                                                        <option value="YE">Yemen</option>
                                                                                        <option value="ZM">Zambia</option>
                                                                                        <option value="ZW">Zimbabwe</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Region
																				 <span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="region" id="country_list" class="form-control">
                                                                                        <option value=""></option>
                                                                                        <option value="Dar es salaam">Dar es salaam</option>
                                                                                        <option value="Dodoma">Dodoma</option>
                                                                                        <option value="Arusha">Arusha</option>
                                                                                        <option value="Mwanza">Mwanza</option>
                                                                                        <option value="Kigoma">Kigoma</option>
                                                                                        <option value="Kilimanjaro">Kilimanjaro</option>                                                                                    
                                                                                    </select>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">District
																				 <span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="district" id="country_list" class="form-control">
                                                                                        <option value=""></option>
                                                                                        <option value="Kinondoni">Kinondoni</option>
                                                                                        <option value="Ilala">Ilala</option>
                                                                                        <option value="Temeke">Temeke</option>
                                                                                        <option value="Ubungo">Ubungo</option>
                                                                                        <option value="Kigamboni">Kigamboni</option>                                                                                                                                                                         
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Ward
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="ward"placeholder="Provide your ward" />                                                                                    
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Local Govn Name
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="localgov" placeholder="Provide your local govn Name"/>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Location
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="location" placeholder="Provide your location(eg Sinza Mori)" />
                                                                               </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Street
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="street" placeholder="Provide your street" />                                                                                    
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">House Number
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="housenumber" placeholder="Provide your house number" /> 
																					
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">P.O.Box                                                                                    
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="pobox" placeholder="Provide your P.O.Box" />                                                                                    
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Street representative
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="streetrepresentative" placeholder="Provide your street representative"/>                                                                                  
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Email
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="email" placeholder="Provide your email" />                                                                                   
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Telephone Nummber
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
                                                                            <h3 class="block">Choose or add director</h3>																		
                                                                            <div class="form-group">
                                                                                <label for="tags" class="control-label col-md-3">Director name
                                                                                    <span class="required"> * </span>
                                                                                </label>                                                                            
																				<div class="col-md-4">
																					<input id="typeahead_example_2" type="text" class="form-control" name="directorname" placeholder="Select Director" />   
																					<input type=button onClick='location.href="../loan/person_add.php"' class="btn btn-default" value="New Person" />  
																					<p class="help-block"> Select director or click new to add new director. </p>																					
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Telephone Nummber
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input hidden="true" id="directorid" name="directorid" value="">                                                                                  
                                                                                </div>
                                                                            </div>
																			
																																					
                                                                        </div>
<!--END TAB 3 *********************************************************************************************************************************************************************-->
<!--BEGIN TAB 4 *******************************************************************************************************************************************************************-->																
                                                                        <div class="tab-pane" id="tab4">
                                                                            <h3 class="block">Confirm your details</h3>
                                                                            <h4 class="form-section">Organizational Details</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Organization Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="organizationname"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Organizationtype:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="organizationtypeid"> </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Registration Number:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="registrationnumber"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">TIN Number:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="tin"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">VAT registration number:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="vrn"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Registration date:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="registrationdate"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Registration Copy:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="registrationcopy"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">TIN Copy:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="tincopy"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">VRN Copy:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="vrncopy"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Licence Copy:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="licensecopy"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Memorundum Copy:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="memorundumcopy"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Logo Copy:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="logocopy"> </p>
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
                                                                            <h4 class="form-section">Directors</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Director Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="partyid"> </p>
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
																