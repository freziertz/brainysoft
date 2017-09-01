<?php 
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
$loanid =10 ; //$_GET['id'] ;
}else {
	$loanid = 10;
}


if ($_SERVER ["REQUEST_METHOD"] == "POST") {
	
	$loanid = $_POST['loanid'] ;
	if (isset ( $_POST ["btnSave"] )) {
		
		try {
			//start transaction
			$user_login->beginTransaction();
			
			$sqlSelectMobileNumber = ("SELECT distinct phonenumber,applicant,totaldue,paymentdate FROM datahouserpdb.contactview where phonetype ='mobile' and loanid = ?");
			$stmtMobileNumber= $user_login->runQuery($sqlSelectMobileNumber);
			$stmtMobileNumber->execute(array($loanid));
			
			
				
			// Create new Event	
			//Format registration date
			$eventstartdate = $_POST ['eventstartdate'];
			$date = str_replace ( '/', '-', $eventstartdate);
			$eventStartDate = date ( 'Y-m-d', strtotime ( $date ) );
			//End format registration date	
			
			//Format registration date
			$eventenddate = $_POST ['eventenddate'];
			$date = str_replace ( '/', '-', $eventenddate );
			$eventEndDate = date ( 'Y-m-d', strtotime ( $date ) );
			//End format registration date
			
			
			
			
			
			$eventTitle = $_POST ['eventtitle'];
			$eventTypeId = $_POST ['eventtypeid'];
			$eventStartTime = $_POST ['eventstarttime'];
			$eventEndTime = $_POST ['eventendtime'];
			$eventDescription = $_POST ['eventdescription'];		
			$eventLocation = $_POST ['eventlocation'];
			$eventNotifiedPersonId = $_POST ['notifiedpersonid'];
			$notificationTypeId= $_POST ['notificationtypeid'];
			$notificationUnit= $_POST ['notificationunit'];
			$reminderTime= $_POST ['remindertime'];
			$reminderDate= $_POST ['remindertime'];
			
			$sqlNotificationType = ("SELECT notificationtypeid, notificationtypename FROM eventnotificationtype WHERE notificationtypeid = ? ");
			$stmtNotificationType= $user_login->runQuery($sqlNotificationType);
			$stmtNotificationType->execute(array($notificationTypeId));
			$rowNotificationType= $stmtNotificationType->fetch();
			$notificationName= $rowNotificationType['notificationtypename'];
			
			
			
			
			function reminderDate($eventStartDate,$eventStartTime,$reminderTime,$notificationUnit) {
				$reminderDuration = $reminderTime. " ".$notificationUnit;
				$reminderDate= date_create ( $eventStartDate);
				$reminderDate = date_sub ( $reminderDate, date_interval_create_from_date_string ( $reminderDuration) );				
				return date_format ( $reminderDate, "Y-m-d" );
			}
			
		
			
			
			
			
		
			//Start Inserting Event into the Database
			$sqlEvent =( "INSERT INTO Event (loanid,eventtitle,eventtypeid,eventstartdate,eventstarttime,eventenddate,eventendtime,eventdescription,eventowner,eventlocation) VALUES (?,?,?,?,?,?,?,?,?,?)" );
			$insertEvent = $user_login->runQuery ($sqlEvent);
			$insertEvent->execute ( array (
					$loanid,
					$eventTitle,
					$eventTypeId,
					$eventStartDate,					
					$eventStartTime,
					$eventEndDate,
					$eventEndTime,
					$eventDescription,
					$createbyid,
					$eventLocation
							
			) );
			
			$newEventId = $user_login->lastID();
			
			//Create Upload Object
			/* $flup = new fileUploader\fileUploader();
			//End Create Upload Object
			//Start Upload event attachment
			for($i=1;$i<=1;$i++){
			if (isset ( $_FILES ['eventattachment'] )) {
				$eventAttachmentName= basename($_FILES["eventattachment"]["name"]);				
				$filenumber = 'event_'.$newEventId.'_'.$i;
				$perPhoto = $flup->upload ( "brainysoft/doc/docpath/eventattachment/",$_FILES ['eventattachment'], $filenumber );
				$eventAttachmentPath = $GLOBALS ['nameOfFile'];
			}
			
			$insertEventAttachment = $user_login->runQuery ( "INSERT INTO eventattachment (attachmentpath,attachmentname,eventid)
			VALUES (?,?,?)" );
			$insertEventAttachment->execute ( array (
					$eventAttachmentPath,
					$eventAttachmentName,
					$newEventId
					
			) );
			
			
			} */
			
			//Start Upload event attachment
			for($i=1;$i<=1;$i++){
				
				$insertEventNotifiedPerson = $user_login->runQuery ( "INSERT INTO eventnotifiedperson (eventid,personid)
			VALUES (?,?)" );
				$insertEventNotifiedPerson->execute ( array (
						$newEventId,
						$eventNotifiedPersonId
						
				) );
				
				
			}
			
			
			for($i=1;$i<=1;$i++){
				$reminderDate=reminderDate($eventStartDate,$eventStartTime,$reminderTime,$notificationUnit);
				$insertEventNotification = $user_login->runQuery ( "INSERT INTO eventnotification (eventid,notificationtime,notificationdate,notificationypeid)
			VALUES (?,?,?,?)" );
				$insertEventNotification->execute ( array (
						$newEventId,
						$reminderTime,
						$reminderDate,
						$notificationTypeId
						
				) );
				
				
			}
			
			foreach ( $stmtMobileNumber->fetchAll () as $rowMobileNumber){				
				if (strlen($rowMobileNumber['phonenumber']) == 9){
					$messageTo = "+255".$rowMobileNumber['phonenumber'];
				}elseif (strlen($rowMobileNumber['phonenumber']) == 12){
					$messageTo = "+".$rowMobileNumber['phonenumber'];
				}else{
					$messageTo = $rowMobileNumber['phonenumber'];
				}
				$messageText = "Ndugu ".$rowMobileNumber['applicant']." unaombwa kurudisha marejesho yako,unadaiwa kiasi cha Tsh. ".$rowMobileNumber['totaldue']."kabla ya tarehe".$rowMobileNumber['paymentdate'];
				$insertSmsNotification = $user_login->runQuery ( "INSERT INTO smsout (eventid,loanid,messageto, messagefrom, messagetext, scheduled, issent)
				VALUES (?,?,?,?,?,?,?)" );
				$insertSmsNotification->execute ( array (
						$newEventId,
						$loanid,
						$messageTo,
						'+255653404187',
						$messageText,
						$reminderDate,
						0
						
						
				) );
			}
			
		
			
			
			//If Everything Go Well, Commit
			if ($user_login->commit()) {				
				echo '<script type="text/javascript"> alert("Event Created Successfully.");</script>';
				//$user_login->redirect('../loan/event_list.php');
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
$location = "New Event";
$title = "New Event";
$breadcumb ="Create New Event";
$breadcumbDescription=" Create new event, add notified person, attachment and notification type,";
$currentSymbo = "TZS";
include('../inc/header.php')



?>
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                        <div class="row">
                                            <div class="col-md-12">                                           
                                                <div class="portlet light " id="">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class=" icon-layers font-blue"></i>
                                                            <span class="caption-subject font-red bold uppercase"> Event Details -
                                                              
                                                            </span>
                                                        </div>                                                     
                                                    </div>
                                                    <div class="portlet-body form">
                                                        <form class="form-horizontal" action="../event/event_add.php"  method="post" enctype="multipart/form-data">
                                                           
                                                                <div class="form-body">
                                                                   
                                                                    
                                                                    <div class="tab-content">
                                                                        <div class="alert alert-danger display-none">
                                                                            <button class="close" data-dismiss="alert"></button> You have some form errors. Please check below. </div>
                                                                        <div class="alert alert-success display-none">
                                                                            <button class="close" data-dismiss="alert"></button> Your form validation is successful! </div>
																		<!--BEGIN TAB 1 -->	
                                                                        <div class="tab-pane active" id="tab1">
                                                                            <h3 class="block">Provide event details for loan <?php echo $loanid;?>   </h3>
                                                                             <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Id
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="loanid" value="<?php echo $loanid;?>" readonly />
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Event Title
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="eventtitle" placeholder="Provide event title" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Event Type
																				 <span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="eventtypeid" id="country_list" class="form-control">
																						<option value=""></option>																					
                                                                                         <?php																																										
																						// Get eventtypeid and eventtypename to eventtype
																						$sqlSelectEventType = ("SELECT eventtypeid,eventtypename FROM eventtype");
																						$stmtEventType =$user_login->runQuery ( $sqlSelectEventType);	
																						$stmtEventType->execute ( array () );
																						foreach ( $stmtEventType->fetchAll () as $row ) {
																							echo "<option value='" . $row ['eventtypeid'] . "'>" . $row ['eventtypename'] . "</option>";
																						}																																										
																						?>                                                                          
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
																				<label class="control-label col-md-3">Event Start Date
																					<span class=""> </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="eventstartdate" />
																				</div>
																				<label class="control-label col-md-2">Start Time                                                                                    
                                                                                </label>
                                                                                <div class="col-md-2">
                                                                                    <input type="time" class="form-control" name="eventstarttime" placeholder="Start time" />
                                                                                </div>
																			</div>								
																				
																			<div class="form-group">
																				<label class="control-label col-md-3">Event End Date
																					<span class=""> </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="eventenddate" />
																				</div>
																				<label class="control-label col-md-2">End Time
                                                                                    
                                                                                </label>
                                                                                <div class="col-md-2">
                                                                                    <input type="time" class="form-control" name="eventendtime" placeholder="End time" />
                                                                                </div>
																			</div>														
																																					
																			
																		<div class="form-group">
                                                                                <label class="control-label col-md-3">Event Location                                                                                   
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" value="HQ" name="eventlocation" placeholder="Provide even location" />
                                                                                </div>
                                                                            </div>
                                                                             <div class="form-group">
                                                                                <label class="control-label col-md-3">Notified Person																				
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="notifiedpersonid" id="country_list" class="form-control">
																						<option value=""></option>																					
                                                                                         <?php																																										
																						// Get notifiedpersonid and notifiedpersonname to users
																						$sqlSelectNotifiedPerson = ("SELECT userid,concat(title,'. ',firstname,' ',lastname) as fullname FROM users");
																						$stmtNotifiedPerson=$user_login->runQuery ( $sqlSelectNotifiedPerson);	
																						$stmtNotifiedPerson->execute ( array () );
																						foreach ( $stmtNotifiedPerson->fetchAll () as $row ) {
																							echo "<option value='" . $row ['userid'] . "'>" . $row ['fullname'] . "</option>";
																						}																																										
																						?>                                                                          
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Notification Type																				
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="notificationtypeid" id="country_list" class="form-control">
																						<option value=""></option>																					
                                                                                         <?php																																										
																						// Get notifiedpersonid and notifiedpersonname to users
																						$sqlSelectNotifiedType = ("SELECT notificationtypeid,notificationtypename FROM eventnotificationtype");
																						$stmtNotifiedType = $user_login->runQuery ( $sqlSelectNotifiedType);	
																						$stmtNotifiedType->execute ( array () );
																						foreach ( $stmtNotifiedType->fetchAll () as $row ) {
																							echo "<option value='" . $row ['notificationtypeid'] . "'>" . $row ['notificationtypename'] . "</option>";
																						}																																										
																						?>                                                                          
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-1">
                                                                                    <input type="number" class="form-control" value="0" name="remindertime" placeholder="Provide even location" />
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <select name="notificationunit" id="country_list" class="form-control">
																						<option value="Minutes">Minutes</option>	
																						<option value="Hours">Hours</option>
																						<option value="Days">Days</option>
																						<option value="Weeks">Weeks</option>																				
                                                                                                                                                               
                                                                                    </select>
                                                                                </div>
                                                                                
                                                                            </div>
                                                                            <div class="form-group">
																			<label for="exampleInputFile" class="col-md-3 control-label">Event Attachment																				
																			</label>
																				<div class="col-md-4">
																					<input type="file" id="exampleInputFile" name="eventattachment">
																					<p class="help-block"> Select event attachment. </p>
																				</div>
																			</div>	
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Event Description</label>
                                                                                <div class="col-md-4">
                                                                                    <textarea class="form-control" rows="3" name="eventdescription" placeholder="Provide your event description"></textarea>
                                                                                </div>
                                                                            </div>																	
																			
																		
                                                                        </div>
                                                                        
                                                                        <div class="form-group"> 
																	    <div class="col-sm-offset-4 col-sm-10">
																	      <button type="submit" class="btn btn-default" name="btnSave">Submit</button>
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


																