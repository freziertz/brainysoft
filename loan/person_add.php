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
                                                        <form class="form-horizontal" action="../loan/person_add.php" id="submit_form" method="post" enctype="multipart/form-data">
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
                                                                                <label class="control-label col-md-3">Title
																				 <span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="title" id="country_list" class="form-control">																					
                                                                                        <option value=""></option>
																						<option value="Mr">Mr</option>
																						<option value="Mr">Miss</option>
																						<option value="Mr">Madame</option>
																						<option value="Mrs">Mrs</option>
																						<option value="Dr">Dr</option>
																						<option value="Prof">Prof</option>                                                                               
                                                                                    </select>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">First Name
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="firstname" placeholder="Provide your first name" />
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Middle Name
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="middlename" placeholder="Provide your middle name" />
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Last Name
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="lastname" placeholder="Provide your last name" />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
																				<label class="control-label col-md-3">Date of Birth
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="dateofbirth" />
																				</div>
																			</div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Gender
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <div class="radio-list">
                                                                                        <label>
                                                                                            <input type="radio" name="gender" value="Male" data-title="Male" /> Male </label>
                                                                                        <label>
                                                                                            <input type="radio" name="gender" value="Female" data-title="Female" /> Female </label>
                                                                                    </div>
                                                                                    <div id="form_gender_error"> </div>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Nationality
																				<span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="nationality" id="country_list" class="form-control">
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
                                                                                <label class="control-label col-md-3">Tribe
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="tribe" placeholder="Provide your tribe" />
                                                                                </div>
                                                                            </div> 
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Marital Status
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <div class="radio-list">
                                                                                        <label>
                                                                                            <input type="radio" name="maritalstatus" value="Sngle" data-title="single" /> Single </label>
                                                                                        <label>
                                                                                            <input type="radio" name="maritalstatus" value="Married" data-title="married" /> Merried </label>
                                                                                    </div>
                                                                                    <div id="form_gender_error"> </div>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">Photo
																				<span class="required"> * </span>
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="photopath">
																					<p class="help-block"> Select photo. </p>
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
                                                                                    <input type="text" class="form-control" name="housenumber" placeholder="Provide your house number" />                                                                                    <span class="help-block"> Provide your house number </span>
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