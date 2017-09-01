<?php 
require_once '../user/class.user.php';
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}

//Define page variable	
$location = "Loan Application";
$title = "Loan Application";
$breadcumb ="Loan Application";
$breadcumbDescription=" View loan application details, add identity, physical address, director and reject or approve loan";
$currentSymbo = "TZS";

include('../inc/header.php')?>
 <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                        <div class="row">										
                                            <div class="col-md-12 ">
                                                <!-- BEGIN SAMPLE FORM PORTLET-->
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-settings font-dark"></i>
                                                            <span class="caption-subject font-dark sbold uppercase">Horizontal Form</span>
                                                        </div>
                                                        <div class="actions">
                                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                                <label class="btn btn-transparent dark btn-outline btn-circle btn-sm active">
                                                                    <input type="radio" name="options" class="toggle" id="option1">Actions</label>
                                                                <label class="btn btn-transparent dark btn-outline btn-circle btn-sm">
                                                                    <input type="radio" name="options" class="toggle" id="option2">Settings</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body form">
                                                        <form class="form-horizontal" role="form">
                                                            <div class="form-body">
															 <div class="form-group">
                                                                    <label class="col-md-3 control-label">Title</label>
                                                                    <div class="col-md-3">
                                                                        <select class="form-control">
                                                                            <option value="" disabled selected>Title</option>
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
                                                                    <label class="col-md-3 control-label">First Name</label>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control" placeholder="Enter text">                                                                       
                                                                    </div>
                                                                </div>
																<div class="form-group">
                                                                    <label class="col-md-3 control-label">Midle Names</label>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control" placeholder="Enter text">                                                                       
                                                                    </div>
                                                                </div>
																<div class="form-group">
                                                                    <label class="col-md-3 control-label">Last Name</label>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control" placeholder="Enter text">                                                                       
                                                                    </div>
                                                                </div>
																
																    <div class="form-group">
                                                                    <label class="col-md-3 control-label">Gender</label>
                                                                    <div class="col-md-3">
                                                                        <div class="mt-radio-list">
                                                                            <label class="mt-radio mt-radio-line">
                                                                                <input type="radio" name="optionsRadios" id="optionsRadios22" value="option1" checked> Male
                                                                                <span></span>
                                                                            </label>
                                                                            <label class="mt-radio mt-radio-line">
                                                                                <input type="radio" name="optionsRadios" id="optionsRadios23" value="option2" checked> Female
                                                                                <span></span>
                                                                            </label>
                                                                            </div>
                                                                    </div>
                                                                </div>								
																
																<div class="form-group">
                                                                    <label class="col-md-3 control-label">Nationality</label>
                                                                    <div class="col-md-3">
                                                                        <select class="form-control">
                                                                            <option value="" disabled selected>Nationality</option>
																			<option value="Mr">Tanzania</option>
																			<option value="Mr">Kenya</option>
																			<option value="Mr">Uganda</option>
																			<option value="Mrs">Rwanda</option>
																			<option value="Dr">Burundi</option>
																			
                                                                        </select>
                                                                    </div>
                                                                </div>
																
																  <div class="form-group">
                                                                    <label class="control-label col-md-3">Date of Birth</label>
                                                                    <div class="col-md-3">
                                                                        <input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" />
                                                                     </div>
                                                                </div>
																
																 <div class="form-group">
                                                                    <label for="exampleInputFile" class="col-md-3 control-label">File input</label>
                                                                    <div class="col-md-9">
                                                                        <input type="file" id="exampleInputFile">
                                                                        <p class="help-block"> some help text here. </p>
                                                                    </div>
                                                                </div>


                        <div class="form-actions">
                                                                <div class="row">
                                                                    <div class="col-md-offset-3 col-md-9">
                                                                        <button type="submit" class="btn green">Submit</button>
                                                                        <button type="button" class="btn default">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- END SAMPLE FORM PORTLET-->
											
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