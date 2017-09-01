<?php 
//chnge this code to clear event and to add payment status of each installment
//also consider event reschedule
//this suppose to look on payment schedule and check first instalment is due then change status to be done
require_once '../user/class.user.php';
require ('../inc/fileUploader.php');
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}

if ($_SERVER ["REQUEST_METHOD"] == "POST") {
	
	$loanid = $_POST['loanid'] ;

	if (!isset ( $_POST ["btnSave"] )) {
		
	
		
		
	}
}




			
//Define page variable	
$location = "Loan Payment";
$title = "Loan Payment";
$breadcumb ="Loan payment";
$breadcumbDescription=" All payment";
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
                                                            <span class="caption-subject font-red bold uppercase"> Payment Details -
                                                                <span class="step-title"> Step 1 of 2 </span>
                                                            </span>
                                                        </div>                                                     
                                                    </div>
                                                    
                                                    
                                                        <form class="form-horizontal" action="../loan/loan_payment.php" id="submit_form" method="post" enctype="multipart/form-data">
                                                            <div class="form-wizard">
                                                                <div class="form-body">
                                                                    <ul class="nav nav-pills nav-justified steps">
                                                                        <li>
                                                                            <a href="#tab1" data-toggle="tab" class="step">
                                                                                <span class="number"> 1 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Add Payment </span>
                                                                            </a>
                                                                        </li>                                                                        
                                                                        <li>
                                                                            <a href="#tab2" data-toggle="tab" class="step">
                                                                                <span class="number"> 2 </span>
                                                                                <span class="desc">
                                                                                    <i class="fa fa-check"></i> Confirm Payment</span>
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
                                                                            <h3 class="block">Add Payment</h3>                                                                          
																			 
																			
																			
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Fine
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="totalfine" value="<?php ""; ?>" readonly />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Total Due
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" align="right" class="form-control" name="totaldue" value="<?php echo number_format(1000000,2); ?>" readonly />
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            
                                                                            <!-- repeater start here -->
                                                                           <div class="form-group mt-repeater">
                                                                				<div data-repeater-list="group-c">
                                                                    			<div data-repeater-item class="mt-repeater-item">
                                                                        <div class="row mt-repeater-row">
                                                                        	<label class="control-label col-md-3">Product Variation
                                                                        	</label>
                                                                            <div class="col-md-2">                                                                                
                                                                                <input type="text" placeholder="Salted Tuna" class="form-control" name="totaldue" /> 
                                                                            </div>
                                                                            <div class="col-md-3">                                                                                
                                                                                <input type="text" placeholder="3" name="totaldue" class="form-control" /> 
                                                                            </div>
                                                                            <div class="col-md-1">
                                                                                <a href="javascript:;" data-repeater-delete class="mt-repeater-delete" class="form-control" >
                                                                                    <i class="fa fa-close"></i>
                                                                                </a>
                                                                            </div>
                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                                <a href="javascript:;" align="right" data-repeater-create class="mt-repeater-add">
                                                                    			<i class="fa fa-plus"></i> Add
                                                                    			</a>
                                                                            </div>
                                                            </div>
                                                                            
                                                                            <!-- repeater end here -->
                                                                            
                                                                            
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Amount
                                                                                    <span class="required"> * </span>
                                                                                </label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="loanpaymentamount" placeholder="Provide your last name" />
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
																				<label class="control-label col-md-3">Payment Date
																					<span class="required"> * </span>
																				</label>
																				<div class="col-md-3">
																					<input class="form-control form-control-inline input-medium date-picker"data-date-format="dd-mm-yyyy"   size="16" type="text" value="Select date" name="paymentdate" />
																				</div>
																			</div>																			
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Account
																				<span class="required"> * </span>
																				</label>
                                                                                <div class="col-md-4">
                                                                                    <select name="paymentaccountid" id="country_list" class="form-control">
                                                                                        <?php																																										
																						// Get loantypeid and loantypename to populaty loantype
																						$sqlSelectPaymentMethod = ("SELECT accountid,accountname FROM accounts");
																						$stmtLoanPaymentMethod =$user_login->runQuery ( $sqlSelectPaymentMethod );	
																						$stmtLoanPaymentMethod->execute ( array () );
																						foreach ( $stmtLoanPaymentMethod->fetchAll () as $row ) {
																							echo "<option value='" . $row ['accountid'] . "'>" . $row ['accountname'] . "</option>";
																						}																																										
																						?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>																			 
																			
																		</div>
                      
<!--END TAB 1 *********************************************************************************************************************************************************************-->
																		
                                                                        

<!--BEGIN TAB 2*******************************************************************************************************************************************************************-->																
                                                                        <div class="tab-pane" id="tab2">
                                                                            <h3 class="block">Confirm your details</h3>
                                                                            <h4 class="form-section">Payment Details</h4>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-md-3">Loan Id:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="loanid"> </p>
                                                                                </div>
                                                                            </div>
																			 <div class="form-group">
                                                                                <label class="control-label col-md-3">Full Name:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="name"> </p>
                                                                                </div>
                                                                            </div>
                                                                            
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Date:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="paymentdate"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Amount:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="dateofbirth"> </p>
                                                                                </div>
                                                                            </div>
																			<div class="form-group">
                                                                                <label class="control-label col-md-3">Payment Account:</label>
                                                                                <div class="col-md-4">
                                                                                    <p class="form-control-static" data-display="paymentaccountid"> </p>
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