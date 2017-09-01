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
                                                <div class="portlet light portlet-fit  calendar">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class=" icon-layers font-green"></i>
                                                            <span class="caption-subject font-green sbold uppercase">Calendar</span>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="col-md-3 col-sm-12">
                                                                <!-- BEGIN DRAGGABLE EVENTS PORTLET-->
                                                                <h3 class="event-form-title margin-bottom-20">Draggable Events</h3>
                                                                <div id="external-events">
                                                                    <form class="inline-form">
                                                                        <input type="text" value="" class="form-control" placeholder="Event Title..." id="event_title" />
                                                                        <br/>
                                                                        <a href="javascript:;" id="event_add" class="btn green"> Add Event </a>
                                                                    </form>
                                                                    <hr/>
                                                                    <div id="event_box" class="margin-bottom-10"></div>
                                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline" for="drop-remove"> remove after drop
                                                                        <input type="checkbox" class="group-checkable" id="drop-remove" />
                                                                        <span></span>
                                                                    </label>
                                                                    <hr class="visible-xs" /> </div>
                                                                <!-- END DRAGGABLE EVENTS PORTLET-->
                                                            </div>
                                                            <div class="col-md-9 col-sm-12">
                                                                <div id="calendar" class="has-toolbar"> </div>
                                                            </div>
                                                        </div>
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