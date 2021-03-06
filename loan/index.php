<?php 
require_once '../user/class.user.php';
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}else{
	$currentUser = $createbyid=$_SESSION['userSession'];
}
	
	$sqlIssuedThisYear = ("SELECT SUM(loanamount) as loanamount FROM datahouserpdb.loan WHERE loanstatus = 5 and YEAR(issueddate) = YEAR(CURDATE()); ");
	$stmtLoanThisYear = $user_login->runQuery($sqlIssuedThisYear);
	$stmtLoanThisYear->execute(array());
	$rowIssuedLoan=$stmtLoanThisYear->fetch();
	$totalLoanThisYear = $rowIssuedLoan['loanamount'];
	
	
	$sqlPaymentThisYear = ("SELECT SUM(paymentamount) as paymentamount FROM datahouserpdb.loanpayment WHERE YEAR(paymentdate) = YEAR(CURDATE()); ");
	$stmtPaymentThisYear = $user_login->runQuery($sqlPaymentThisYear);
	$stmtPaymentThisYear->execute(array());
	$rowLoanPayment=$stmtPaymentThisYear->fetch();
	$totalCollectionThisYear = $rowLoanPayment['paymentamount'];
	
	$sqlIssuedThisMonth = ("SELECT SUM(loanamount) as loanamount,SUM(totalamount-loanbalance)as collection FROM datahouserpdb.loan WHERE loanstatus = 5 and MONTH(issueddate) = MONTH(CURDATE()); ");
	$stmtLoanThisMonth = $user_login->runQuery($sqlIssuedThisMonth);
	$stmtLoanThisMonth->execute(array());
	$rowIssuedThisMonth=$stmtLoanThisMonth->fetch();
	$totalLoanThisMonth = $rowIssuedThisMonth['loanamount'];
	
	
	$sqlPaymentThisMonth = ("SELECT SUM(paymentamount) as paymentamount FROM datahouserpdb.loanpayment WHERE MONTH(paymentdate) = MONTH(CURDATE()); ");
	$stmtPaymentThisMonth = $user_login->runQuery($sqlPaymentThisMonth);
	$stmtPaymentThisMonth->execute(array());
	$rowThisMonthPayment=$stmtPaymentThisMonth->fetch();
	$totalCollectionThisMonth = $rowThisMonthPayment['paymentamount'];
	
	$sqlIssuedThisWeek = ("SELECT SUM(loanamount) as loanamount,SUM(totalamount-loanbalance)as collection FROM datahouserpdb.loan WHERE loanstatus = 5 and WEEK(issueddate) = WEEK(CURDATE()); ");
	$stmtLoanThisWeek = $user_login->runQuery($sqlIssuedThisWeek);
	$stmtLoanThisWeek->execute(array());
	$rowIssuedThisWeek=$stmtLoanThisWeek->fetch();
	$totalLoanThisWeek = $rowIssuedThisWeek['loanamount'];
	
	
	$sqlPaymentThisWeek = ("SELECT SUM(paymentamount) as paymentamount FROM datahouserpdb.loanpayment WHERE WEEK(paymentdate) = WEEK(CURDATE()); ");
	$stmtPaymentThisWeek = $user_login->runQuery($sqlPaymentThisWeek);
	$stmtPaymentThisWeek->execute(array());
	$rowThisWeekPayment=$stmtPaymentThisWeek->fetch();
	$totalCollectionThisWeek = $rowThisWeekPayment['paymentamount'];
	
	//Define page variable
	$location = "Dashboard";
	$title = "Loan Dashboard";
	$breadcumb ="Loan Dashboard";
	$breadcumbDescription="statistics, charts, recent loan, recent events and reports";
	$currentSymbo = "TZS";
	
include('../inc/header.php');?>
<!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="dashboard-stat2 ">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h4 class="font-green-sharp">
																<span data-counter="counterup" data-value="<?php echo number_format($totalLoanThisYear,2);?>">0</span>
																<small class="font-green-sharp"><?php echo $currentSymbo; ?></small>                                                                
                                                            </h4>
                                                            <small><?php echo date("Y");?> LOAN</small>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="icon-pie-chart"></i>
                                                        </div>
                                                    </div>
                                                    <div class="progress-info">
                                                        <div class="progress">
                                                            <span style="width: 76%;" class="progress-bar progress-bar-success green-sharp">
                                                                <span class="sr-only">76% progress</span>
                                                            </span>
                                                        </div>
                                                        <div class="status">
                                                            <div class="status-title"> progress </div>
                                                            <div class="status-number"> 76% </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="dashboard-stat2 ">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h4 class="font-red-haze">
                                                                <span data-counter="counterup" data-value="<?php echo number_format($totalLoanThisMonth,2);?>">0</span>
																<small class="font-green-sharp"><?php echo $currentSymbo; ?></small>  
                                                            </h4>
                                                            <small><?php echo date("M");?> LOAN</small>
                                                        </div>
                                                        <div class="icon">
                                                             <i class="icon-basket"></i>
                                                        </div>
                                                    </div>
                                                    <div class="progress-info">
                                                        <div class="progress">
                                                            <span style="width: 85%;" class="progress-bar progress-bar-success red-haze">
                                                                <span class="sr-only">85% progress</span>
                                                            </span>
                                                        </div>
                                                        <div class="status">
                                                            <div class="status-title"> progress </div>
                                                            <div class="status-number"> 85% </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="dashboard-stat2 ">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h4 class="font-blue-sharp">
                                                                <span data-counter="counterup" data-value="<?php echo number_format($totalCollectionThisYear,2);?>"></span>
																<small class="font-green-sharp"><?php echo $currentSymbo; ?></small>  
                                                            </h4>
                                                            <small><?php echo date("Y");?> COLLECTION</small>
                                                        </div>
                                                        <div class="icon">
															 <i class="icon-like"></i>                                                           
                                                        </div>
                                                    </div>
                                                    <div class="progress-info">
                                                        <div class="progress">
                                                            <span style="width: 45%;" class="progress-bar progress-bar-success blue-sharp">
                                                                <span class="sr-only">45% grow</span>
                                                            </span>
                                                        </div>
                                                        <div class="status">
                                                            <div class="status-title"> grow </div>
                                                            <div class="status-number"> 45% </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="dashboard-stat2 ">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h4 class="font-purple-soft">
                                                                <span data-counter="counterup" data-value="<?php echo number_format($totalCollectionThisMonth,2);?>"></span>
																<small class="font-green-sharp"><?php echo $currentSymbo; ?></small>  
                                                            </h4>
                                                            <small><?php echo date("M");?> COLLECTION</small>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="icon-user"></i>
                                                        </div>
                                                    </div>
                                                    <div class="progress-info">
                                                        <div class="progress">
                                                            <span style="width: 57%;" class="progress-bar progress-bar-success purple-soft">
                                                                <span class="sr-only">56% change</span>
                                                            </span>
                                                        </div>
                                                        <div class="status">
                                                            <div class="status-title"> change </div>
                                                            <div class="status-number"> 57% </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <span class="caption-subject bold uppercase font-dark">Revenue</span>
                                                            <span class="caption-helper">distance stats...</span>
                                                        </div>
                                                        <div class="actions">
                                                            <a class="btn btn-circle btn-icon-only btn-default" href="#">
                                                                <i class="icon-cloud-upload"></i>
                                                            </a>
                                                            <a class="btn btn-circle btn-icon-only btn-default" href="#">
                                                                <i class="icon-wrench"></i>
                                                            </a>
                                                            <a class="btn btn-circle btn-icon-only btn-default" href="#">
                                                                <i class="icon-trash"></i>
                                                            </a>
                                                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div id="dashboard_amchart_1" class="CSSAnimationChart"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption ">
                                                            <span class="caption-subject font-dark bold uppercase">Finance</span>
                                                            <span class="caption-helper">distance stats...</span>
                                                        </div>
                                                        <div class="actions">
                                                            <a href="#" class="btn btn-circle green btn-outline btn-sm">
                                                                <i class="fa fa-pencil"></i> Export </a>
                                                            <a href="#" class="btn btn-circle green btn-outline btn-sm">
                                                                <i class="fa fa-print"></i> Print </a>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div id="dashboard_amchart_3" class="CSSAnimationChart"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light portlet-fit ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-directions font-green hide"></i>
                                                            <span class="caption-subject bold font-dark uppercase "> Activities</span>
                                                            <span class="caption-helper">Horizontal Timeline</span>
                                                        </div>
                                                        <div class="actions">
                                                            <div class="btn-group">
                                                                <a class="btn blue btn-outline btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Actions
                                                                    <i class="fa fa-angle-down"></i>
                                                                </a>
                                                                <ul class="dropdown-menu pull-right">
                                                                    <li>
                                                                        <a href="javascript:;"> Action 1</a>
                                                                    </li>
                                                                    <li class="divider"> </li>
                                                                    <li>
                                                                        <a href="javascript:;">Action 2</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:;">Action 3</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:;">Action 4</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="cd-horizontal-timeline mt-timeline-horizontal" data-spacing="60">
                                                            <div class="timeline">
                                                                <div class="events-wrapper">
                                                                    <div class="events">
                                                                        <ol>
                                                                            <li>
                                                                                <a href="#0" data-date="16/01/2014" class="border-after-red bg-after-red selected">16 Jan</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="28/02/2014" class="border-after-red bg-after-red">28 Feb</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="20/04/2014" class="border-after-red bg-after-red">20 Mar</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="20/05/2014" class="border-after-red bg-after-red">20 May</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="09/07/2014" class="border-after-red bg-after-red">09 Jul</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="30/08/2014" class="border-after-red bg-after-red">30 Aug</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="15/09/2014" class="border-after-red bg-after-red">15 Sep</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="01/11/2014" class="border-after-red bg-after-red">01 Nov</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="10/12/2014" class="border-after-red bg-after-red">10 Dec</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="19/01/2015" class="border-after-red bg-after-red">29 Jan</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="03/03/2015" class="border-after-red bg-after-red">3 Mar</a>
                                                                            </li>
                                                                        </ol>
                                                                        <span class="filling-line bg-red" aria-hidden="true"></span>
                                                                    </div>
                                                                    <!-- .events -->
                                                                </div>
                                                                <!-- .events-wrapper -->
                                                                <ul class="cd-timeline-navigation mt-ht-nav-icon">
                                                                    <li>
                                                                        <a href="#0" class="prev inactive btn btn-outline red md-skip">
                                                                            <i class="fa fa-chevron-left"></i>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#0" class="next btn btn-outline red md-skip">
                                                                            <i class="fa fa-chevron-right"></i>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                                <!-- .cd-timeline-navigation -->
                                                            </div>
                                                            <!-- .timeline -->
                                                            <div class="events-content">
                                                                <ol>
                                                                    <li class="selected" data-date="16/01/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">New User</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_3.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">16 January 2014 : 7:45 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, mi felis, aliquam at iaculis
                                                                                mi felis, aliquam at iaculis finibus eu ex. Integer efficitur tincidunt malesuada. Sed sit amet molestie elit, vel placerat ipsum. Ut consectetur odio non est rhoncus volutpat.</p>
                                                                            <a href="javascript:;" class="btn btn-circle red btn-outline">Read More</a>
                                                                            <a href="javascript:;" class="btn btn-circle btn-icon-only blue">
                                                                                <i class="fa fa-plus"></i>
                                                                            </a>
                                                                            <a href="javascript:;" class="btn btn-circle btn-icon-only green pull-right">
                                                                                <i class="fa fa-twitter"></i>
                                                                            </a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="28/02/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Sending Shipment</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_3.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Hugh Grant</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">28 February 2014 : 10:15 AM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle btn-outline green-jungle">Download Shipment List</a>
                                                                            <div class="btn-group dropup pull-right">
                                                                                <button class="btn btn-circle blue-steel dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> Actions
                                                                                    <i class="fa fa-angle-down"></i>
                                                                                </button>
                                                                                <ul class="dropdown-menu pull-right" role="menu">
                                                                                    <li>
                                                                                        <a href="javascript:;">Action </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Another action </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Something else here </a>
                                                                                    </li>
                                                                                    <li class="divider"> </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Separated link </a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="20/04/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Blue Chambray</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_1.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue">Rory Matthew</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">20 April 2014 : 10:45 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut. laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut. </p>
                                                                            <a href="javascript:;" class="btn btn-circle red">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="20/05/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">20 May 2014 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="09/07/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Event Success</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_1.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Matt Goldman</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">9 July 2014 : 8:15 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde.</p>
                                                                            <a href="javascript:;"
                                                                                class="btn btn-circle btn-outline purple-medium">View Summary</a>
                                                                            <div class="btn-group dropup pull-right">
                                                                                <button class="btn btn-circle green dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> Actions
                                                                                    <i class="fa fa-angle-down"></i>
                                                                                </button>
                                                                                <ul class="dropdown-menu pull-right" role="menu">
                                                                                    <li>
                                                                                        <a href="javascript:;">Action </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Another action </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Something else here </a>
                                                                                    </li>
                                                                                    <li class="divider"> </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Separated link </a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="30/08/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Conference Call</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_1.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Rory Matthew</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">30 August 2014 : 5:45 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <img class="timeline-body-img pull-left" src="../assets/pages/media/blog/5.jpg" alt="">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut. laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut. </p>
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut. laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut. </p>
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut. laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut. </p>
                                                                            <a href="javascript:;" class="btn btn-circle red">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="15/09/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Conference Decision</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_5.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Jessica Wolf</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">15 September 2014 : 8:30 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <img class="timeline-body-img pull-right" src="../assets/pages/media/blog/6.jpg" alt="">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-sharp">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="01/11/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">1 November 2014 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="10/12/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">10 December 2015 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="19/01/2015">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">19 January 2015 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="03/03/2015">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">3 March 2015 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                </ol>
                                                            </div>
                                                            <!-- .events-content -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light portlet-fit ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-directions font-green hide"></i>
                                                            <span class="caption-subject bold font-dark uppercase"> Events</span>
                                                            <span class="caption-helper">Horizontal Timeline</span>
                                                        </div>
                                                        <div class="actions">
                                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                                <label class="btn green btn-outline btn-circle btn-sm active">
                                                                    <input type="radio" name="options" class="toggle" id="option1">Actions</label>
                                                                <label class="btn  green btn-outline btn-circle btn-sm">
                                                                    <input type="radio" name="options" class="toggle" id="option2">Tools</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="cd-horizontal-timeline mt-timeline-horizontal" data-spacing="60">
                                                            <div class="timeline mt-timeline-square">
                                                                <div class="events-wrapper">
                                                                    <div class="events">
                                                                        <ol>
                                                                            <li>
                                                                                <a href="#0" data-date="16/01/2014" class="border-after-blue bg-after-blue selected">Expo 2016</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="28/02/2014" class="border-after-blue bg-after-blue">New Promo</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="20/04/2014" class="border-after-blue bg-after-blue">Meeting</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="20/05/2014" class="border-after-blue bg-after-blue">Launch</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="09/07/2014" class="border-after-blue bg-after-blue">Party</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="30/08/2014" class="border-after-blue bg-after-blue">Reports</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="15/09/2014" class="border-after-blue bg-after-blue">HR</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="01/11/2014" class="border-after-blue bg-after-blue">IPO</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="10/12/2014" class="border-after-blue bg-after-blue">Board</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="19/01/2015" class="border-after-blue bg-after-blue">Revenue</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#0" data-date="03/03/2015" class="border-after-blue bg-after-blue">Dinner</a>
                                                                            </li>
                                                                        </ol>
                                                                        <span class="filling-line bg-blue" aria-hidden="true"></span>
                                                                    </div>
                                                                    <!-- .events -->
                                                                </div>
                                                                <!-- .events-wrapper -->
                                                                <ul class="cd-timeline-navigation mt-ht-nav-icon">
                                                                    <li>
                                                                        <a href="#0" class="prev inactive btn blue md-skip">
                                                                            <i class="fa fa-chevron-left"></i>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#0" class="next btn blue md-skip">
                                                                            <i class="fa fa-chevron-right"></i>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                                <!-- .cd-timeline-navigation -->
                                                            </div>
                                                            <!-- .timeline -->
                                                            <div class="events-content">
                                                                <ol>
                                                                    <li class="selected" data-date="16/01/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Expo 2016 Launch</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Lisa Bold</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">23 February 2014</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod mi felis, aliquam at iaculis eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis
                                                                                mi felis, aliquam at iaculis eu, onsectetur adipiscing elit finibus eu ex. Integer efficitur leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas onsectetur
                                                                                adipiscing elit nunc. Suspendisse potenti</p>
                                                                            <a href="javascript:;" class="btn btn-circle dark btn-outline">Read More</a>
                                                                            <a href="javascript:;" class="btn btn-circle btn-icon-only green pull-right">
                                                                                <i class="fa fa-twitter"></i>
                                                                            </a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="28/02/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Sending Shipment</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_3.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Hugh Grant</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">28 February 2014 : 10:15 AM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle btn-outline green-jungle">Download Shipment List</a>
                                                                            <div class="btn-group dropup pull-right">
                                                                                <button class="btn btn-circle blue-steel dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> Actions
                                                                                    <i class="fa fa-angle-down"></i>
                                                                                </button>
                                                                                <ul class="dropdown-menu pull-right" role="menu">
                                                                                    <li>
                                                                                        <a href="javascript:;">Action </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Another action </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Something else here </a>
                                                                                    </li>
                                                                                    <li class="divider"> </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Separated link </a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="20/04/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Blue Chambray</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_1.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue">Rory Matthew</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">20 April 2014 : 10:45 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut. laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut. </p>
                                                                            <a href="javascript:;" class="btn btn-circle red">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="20/05/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">20 May 2014 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="09/07/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Event Success</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_1.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Matt Goldman</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">9 July 2014 : 8:15 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde.</p>
                                                                            <a href="javascript:;"
                                                                                class="btn btn-circle btn-outline purple-medium">View Summary</a>
                                                                            <div class="btn-group dropup pull-right">
                                                                                <button class="btn btn-circle green dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> Actions
                                                                                    <i class="fa fa-angle-down"></i>
                                                                                </button>
                                                                                <ul class="dropdown-menu pull-right" role="menu">
                                                                                    <li>
                                                                                        <a href="javascript:;">Action </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Another action </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Something else here </a>
                                                                                    </li>
                                                                                    <li class="divider"> </li>
                                                                                    <li>
                                                                                        <a href="javascript:;">Separated link </a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="30/08/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Conference Call</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_1.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Rory Matthew</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">30 August 2014 : 5:45 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <img class="timeline-body-img pull-left" src="../assets/pages/media/blog/5.jpg" alt="">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut. laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut. </p>
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut. laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut. </p>
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut. laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut. </p>
                                                                            <a href="javascript:;" class="btn btn-circle red">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="15/09/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Conference Decision</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_5.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Jessica Wolf</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">15 September 2014 : 8:30 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <img class="timeline-body-img pull-right" src="../assets/pages/media/blog/6.jpg" alt="">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus
                                                                                minus veritatis qui ut.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-sharp">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="01/11/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">1 November 2014 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="10/12/2014">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">10 December 2014 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="19/01/2015">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">19 January 2015 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                    <li data-date="03/03/2015">
                                                                        <div class="mt-title">
                                                                            <h2 class="mt-content-title">Timeline Received</h2>
                                                                        </div>
                                                                        <div class="mt-author">
                                                                            <div class="mt-avatar">
                                                                                <img src="../assets/pages/media/users/avatar80_2.jpg" />
                                                                            </div>
                                                                            <div class="mt-author-name">
                                                                                <a href="javascript:;" class="font-blue-madison">Andres Iniesta</a>
                                                                            </div>
                                                                            <div class="mt-author-datetime font-grey-mint">3 March 2015 : 12:20 PM</div>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="mt-content border-grey-steel">
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod eleifend ipsum, at posuere augue. Pellentesque mi felis, aliquam at iaculis eu, finibus eu ex. Integer efficitur
                                                                                leo eget dolor tincidunt, et dignissim risus lacinia. Nam in egestas nunc. Suspendisse potenti. Cras ullamcorper tincidunt malesuada. Sed sit amet molestie elit, vel placerat
                                                                                ipsum. Ut consectetur odio non est rhoncus volutpat. Nullam interdum, neque quis vehicula ornare, lacus elit dignissim purus, quis ultrices erat tortor eget felis. Cras commodo
                                                                                id massa at condimentum. Praesent dignissim luctus risus sed sodales.</p>
                                                                            <a href="javascript:;" class="btn btn-circle green-turquoise">Read More</a>
                                                                        </div>
                                                                    </li>
                                                                </ol>
                                                            </div>
                                                            <!-- .events-content -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption caption-md">
                                                            <i class="icon-bar-chart font-dark hide"></i>
                                                            <span class="caption-subject font-green-steel bold uppercase">Member Activity</span>
                                                            <span class="caption-helper">weekly stats...</span>
                                                        </div>
                                                        <div class="actions">
                                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                                                    <input type="radio" name="options" class="toggle" id="option1">Today</label>
                                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                                                                    <input type="radio" name="options" class="toggle" id="option2">Week</label>
                                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                                                                    <input type="radio" name="options" class="toggle" id="option2">Month</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row number-stats margin-bottom-30">
                                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                                <div class="stat-left">
                                                                    <div class="stat-chart">
                                                                        <!-- do not line break "sparkline_bar" div. sparkline chart has an issue when the container div has line break -->
                                                                        <div id="sparkline_bar"></div>
                                                                    </div>
                                                                    <div class="stat-number">
                                                                        <div class="title"> Total </div>
                                                                        <div class="number"> 2460 </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                                <div class="stat-right">
                                                                    <div class="stat-chart">
                                                                        <!-- do not line break "sparkline_bar" div. sparkline chart has an issue when the container div has line break -->
                                                                        <div id="sparkline_bar2"></div>
                                                                    </div>
                                                                    <div class="stat-number">
                                                                        <div class="title"> New </div>
                                                                        <div class="number"> 719 </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="table-scrollable table-scrollable-borderless">
                                                            <table class="table table-hover table-light">
                                                                <thead>
                                                                    <tr class="uppercase">
                                                                        <th colspan="2"> MEMBER </th>
                                                                        <th> Earnings </th>
                                                                        <th> CASES </th>
                                                                        <th> CLOSED </th>
                                                                        <th> RATE </th>
                                                                    </tr>
                                                                </thead>
                                                                <tr>
                                                                    <td class="fit">
                                                                        <img class="user-pic rounded" src="../assets/pages/media/users/avatar4.jpg"> </td>
                                                                    <td>
                                                                        <a href="javascript:;" class="primary-link">Brain</a>
                                                                    </td>
                                                                    <td> $345 </td>
                                                                    <td> 45 </td>
                                                                    <td> 124 </td>
                                                                    <td>
                                                                        <span class="bold theme-font">80%</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fit">
                                                                        <img class="user-pic rounded" src="../assets/pages/media/users/avatar5.jpg"> </td>
                                                                    <td>
                                                                        <a href="javascript:;" class="primary-link">Nick</a>
                                                                    </td>
                                                                    <td> $560 </td>
                                                                    <td> 12 </td>
                                                                    <td> 24 </td>
                                                                    <td>
                                                                        <span class="bold theme-font">67%</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fit">
                                                                        <img class="user-pic rounded" src="../assets/pages/media/users/avatar6.jpg"> </td>
                                                                    <td>
                                                                        <a href="javascript:;" class="primary-link">Tim</a>
                                                                    </td>
                                                                    <td> $1,345 </td>
                                                                    <td> 450 </td>
                                                                    <td> 46 </td>
                                                                    <td>
                                                                        <span class="bold theme-font">98%</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fit">
                                                                        <img class="user-pic rounded" src="../assets/pages/media/users/avatar7.jpg"> </td>
                                                                    <td>
                                                                        <a href="javascript:;" class="primary-link">Tom</a>
                                                                    </td>
                                                                    <td> $645 </td>
                                                                    <td> 50 </td>
                                                                    <td> 89 </td>
                                                                    <td>
                                                                        <span class="bold theme-font">58%</span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption caption-md">
                                                            <i class="icon-bar-chart font-dark hide"></i>
                                                            <span class="caption-subject font-green-steel bold uppercase">Customer Support</span>
                                                            <span class="caption-helper">45 pending</span>
                                                        </div>
                                                        <div class="inputs">
                                                            <div class="portlet-input input-inline input-small ">
                                                                <div class="input-icon right">
                                                                    <i class="icon-magnifier"></i>
                                                                    <input type="text" class="form-control form-control-solid input-circle" placeholder="search..."> </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="scroller" style="height: 338px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                                            <div class="general-item-list">
                                                                <div class="item">
                                                                    <div class="item-head">
                                                                        <div class="item-details">
                                                                            <img class="item-pic rounded" src="../assets/pages/media/users/avatar4.jpg">
                                                                            <a href="" class="item-name primary-link">Nick Larson</a>
                                                                            <span class="item-label">3 hrs ago</span>
                                                                        </div>
                                                                        <span class="item-status">
                                                                            <span class="badge badge-empty badge-success"></span> Open</span>
                                                                    </div>
                                                                    <div class="item-body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </div>
                                                                </div>
                                                                <div class="item">
                                                                    <div class="item-head">
                                                                        <div class="item-details">
                                                                            <img class="item-pic rounded" src="../assets/pages/media/users/avatar3.jpg">
                                                                            <a href="" class="item-name primary-link">Mark</a>
                                                                            <span class="item-label">5 hrs ago</span>
                                                                        </div>
                                                                        <span class="item-status">
                                                                            <span class="badge badge-empty badge-warning"></span> Pending</span>
                                                                    </div>
                                                                    <div class="item-body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat tincidunt ut laoreet. </div>
                                                                </div>
                                                                <div class="item">
                                                                    <div class="item-head">
                                                                        <div class="item-details">
                                                                            <img class="item-pic rounded" src="../assets/pages/media/users/avatar6.jpg">
                                                                            <a href="" class="item-name primary-link">Nick Larson</a>
                                                                            <span class="item-label">8 hrs ago</span>
                                                                        </div>
                                                                        <span class="item-status">
                                                                            <span class="badge badge-empty badge-primary"></span> Closed</span>
                                                                    </div>
                                                                    <div class="item-body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh. </div>
                                                                </div>
                                                                <div class="item">
                                                                    <div class="item-head">
                                                                        <div class="item-details">
                                                                            <img class="item-pic rounded" src="../assets/pages/media/users/avatar7.jpg">
                                                                            <a href="" class="item-name primary-link">Nick Larson</a>
                                                                            <span class="item-label">12 hrs ago</span>
                                                                        </div>
                                                                        <span class="item-status">
                                                                            <span class="badge badge-empty badge-danger"></span> Pending</span>
                                                                    </div>
                                                                    <div class="item-body"> Consectetuer adipiscing elit Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </div>
                                                                </div>
                                                                <div class="item">
                                                                    <div class="item-head">
                                                                        <div class="item-details">
                                                                            <img class="item-pic rounded" src="../assets/pages/media/users/avatar9.jpg">
                                                                            <a href="" class="item-name primary-link">Richard Stone</a>
                                                                            <span class="item-label">2 days ago</span>
                                                                        </div>
                                                                        <span class="item-status">
                                                                            <span class="badge badge-empty badge-danger"></span> Open</span>
                                                                    </div>
                                                                    <div class="item-body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, ut laoreet dolore magna aliquam erat volutpat. </div>
                                                                </div>
                                                                <div class="item">
                                                                    <div class="item-head">
                                                                        <div class="item-details">
                                                                            <img class="item-pic rounded" src="../assets/pages/media/users/avatar8.jpg">
                                                                            <a href="" class="item-name primary-link">Dan</a>
                                                                            <span class="item-label">3 days ago</span>
                                                                        </div>
                                                                        <span class="item-status">
                                                                            <span class="badge badge-empty badge-warning"></span> Pending</span>
                                                                    </div>
                                                                    <div class="item-body"> Lorem ipsum dolor sit amet, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </div>
                                                                </div>
                                                                <div class="item">
                                                                    <div class="item-head">
                                                                        <div class="item-details">
                                                                            <img class="item-pic rounded" src="../assets/pages/media/users/avatar2.jpg">
                                                                            <a href="" class="item-name primary-link">Larry</a>
                                                                            <span class="item-label">4 hrs ago</span>
                                                                        </div>
                                                                        <span class="item-status">
                                                                            <span class="badge badge-empty badge-success"></span> Open</span>
                                                                    </div>
                                                                    <div class="item-body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-cursor font-dark hide"></i>
                                                            <span class="caption-subject font-dark bold uppercase">General Stats</span>
                                                        </div>
                                                        <div class="actions">
                                                            <a href="javascript:;" class="btn btn-sm btn-circle red easy-pie-chart-reload">
                                                                <i class="fa fa-repeat"></i> Reload </a>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="easy-pie-chart">
                                                                    <div class="number transactions" data-percent="55">
                                                                        <span>+55</span>% </div>
                                                                    <a class="title" href="javascript:;"> Transactions
                                                                        <i class="icon-arrow-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="margin-bottom-10 visible-sm"> </div>
                                                            <div class="col-md-4">
                                                                <div class="easy-pie-chart">
                                                                    <div class="number visits" data-percent="85">
                                                                        <span>+85</span>% </div>
                                                                    <a class="title" href="javascript:;"> New Visits
                                                                        <i class="icon-arrow-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="margin-bottom-10 visible-sm"> </div>
                                                            <div class="col-md-4">
                                                                <div class="easy-pie-chart">
                                                                    <div class="number bounce" data-percent="46">
                                                                        <span>-46</span>% </div>
                                                                    <a class="title" href="javascript:;"> Bounce
                                                                        <i class="icon-arrow-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-equalizer font-dark hide"></i>
                                                            <span class="caption-subject font-dark bold uppercase">Server Stats</span>
                                                            <span class="caption-helper">monthly stats...</span>
                                                        </div>
                                                        <div class="tools">
                                                            <a href="" class="collapse"> </a>
                                                            <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                                                            <a href="" class="reload"> </a>
                                                            <a href="" class="remove"> </a>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="sparkline-chart">
                                                                    <div class="number" id="sparkline_bar5"></div>
                                                                    <a class="title" href="javascript:;"> Network
                                                                        <i class="icon-arrow-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="margin-bottom-10 visible-sm"> </div>
                                                            <div class="col-md-4">
                                                                <div class="sparkline-chart">
                                                                    <div class="number" id="sparkline_bar6"></div>
                                                                    <a class="title" href="javascript:;"> CPU Load
                                                                        <i class="icon-arrow-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="margin-bottom-10 visible-sm"> </div>
                                                            <div class="col-md-4">
                                                                <div class="sparkline-chart">
                                                                    <div class="number" id="sparkline_line"></div>
                                                                    <a class="title" href="javascript:;"> Load Rate
                                                                        <i class="icon-arrow-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-bubble font-dark hide"></i>
                                                            <span class="caption-subject font-hide bold uppercase">Recent Users</span>
                                                        </div>
                                                        <div class="actions">
                                                            <div class="btn-group">
                                                                <a class="btn green-haze btn-outline btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Actions
                                                                    <i class="fa fa-angle-down"></i>
                                                                </a>
                                                                <ul class="dropdown-menu pull-right">
                                                                    <li>
                                                                        <a href="javascript:;"> Option 1</a>
                                                                    </li>
                                                                    <li class="divider"> </li>
                                                                    <li>
                                                                        <a href="javascript:;">Option 2</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:;">Option 3</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:;">Option 4</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <!--begin: widget 1-1 -->
                                                                <div class="mt-widget-1">
                                                                    <div class="mt-icon">
                                                                        <a href="#">
                                                                            <i class="icon-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="mt-img">
                                                                        <img src="../assets/pages/media/users/avatar80_8.jpg"> </div>
                                                                    <div class="mt-body">
                                                                        <h3 class="mt-username">Diana Ellison</h3>
                                                                        <p class="mt-user-title"> Lorem Ipsum is simply dummy text. </p>
                                                                        <div class="mt-stats">
                                                                            <div class="btn-group btn-group btn-group-justified">
                                                                                <a href="javascript:;" class="btn font-red">
                                                                                    <i class="icon-bubbles"></i> 1,7k </a>
                                                                                <a href="javascript:;" class="btn font-green">
                                                                                    <i class="icon-social-twitter"></i> 2,6k </a>
                                                                                <a href="javascript:;" class="btn font-yellow">
                                                                                    <i class="icon-emoticon-smile"></i> 3,7k </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end: widget 1-1 -->
                                                            </div>
                                                            <div class="col-md-4">
                                                                <!--begin: widget 1-2 -->
                                                                <div class="mt-widget-1">
                                                                    <div class="mt-icon">
                                                                        <a href="#">
                                                                            <i class="icon-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="mt-img">
                                                                        <img src="../assets/pages/media/users/avatar80_7.jpg"> </div>
                                                                    <div class="mt-body">
                                                                        <h3 class="mt-username">Jason Baker</h3>
                                                                        <p class="mt-user-title"> Lorem Ipsum is simply dummy text. </p>
                                                                        <div class="mt-stats">
                                                                            <div class="btn-group btn-group btn-group-justified">
                                                                                <a href="javascript:;" class="btn font-yellow">
                                                                                    <i class="icon-bubbles"></i> 1,7k </a>
                                                                                <a href="javascript:;" class="btn font-blue">
                                                                                    <i class="icon-social-twitter"></i> 2,6k </a>
                                                                                <a href="javascript:;" class="btn font-green">
                                                                                    <i class="icon-emoticon-smile"></i> 3,7k </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end: widget 1-2 -->
                                                            </div>
                                                            <div class="col-md-4">
                                                                <!--begin: widget 1-3 -->
                                                                <div class="mt-widget-1">
                                                                    <div class="mt-icon">
                                                                        <a href="#">
                                                                            <i class="icon-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="mt-img">
                                                                        <img src="../assets/pages/media/users/avatar80_6.jpg"> </div>
                                                                    <div class="mt-body">
                                                                        <h3 class="mt-username">Julia Berry</h3>
                                                                        <p class="mt-user-title"> Lorem Ipsum is simply dummy text. </p>
                                                                        <div class="mt-stats">
                                                                            <div class="btn-group btn-group btn-group-justified">
                                                                                <a href="javascript:;" class="btn font-yellow">
                                                                                    <i class="icon-bubbles"></i> 1,7k </a>
                                                                                <a href="javascript:;" class="btn font-red">
                                                                                    <i class="icon-social-twitter"></i> 2,6k </a>
                                                                                <a href="javascript:;" class="btn font-green">
                                                                                    <i class="icon-emoticon-smile"></i> 3,7k </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end: widget 1-3 -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="portlet light portlet-fit ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-microphone font-dark hide"></i>
                                                            <span class="caption-subject bold font-dark uppercase"> Recent Works</span>
                                                            <span class="caption-helper">default option...</span>
                                                        </div>
                                                        <div class="actions">
                                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                                <label class="btn red btn-outline btn-circle btn-sm active">
                                                                    <input type="radio" name="options" class="toggle" id="option1">Settings</label>
                                                                <label class="btn  red btn-outline btn-circle btn-sm">
                                                                    <input type="radio" name="options" class="toggle" id="option2">Tools</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mt-widget-2">
                                                                    <div class="mt-head" style="background-image: url(../assets/pages/img/background/32.jpg);">
                                                                        <div class="mt-head-label">
                                                                            <button type="button" class="btn btn-success">Manhattan</button>
                                                                        </div>
                                                                        <div class="mt-head-user">
                                                                            <div class="mt-head-user-img">
                                                                                <img src="../assets/pages/img/avatars/team7.jpg"> </div>
                                                                            <div class="mt-head-user-info">
                                                                                <span class="mt-user-name">Chris Jagers</span>
                                                                                <span class="mt-user-time">
                                                                                    <i class="icon-emoticon-smile"></i> 3 mins ago </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-body">
                                                                        <h3 class="mt-body-title"> Thomas Clark </h3>
                                                                        <p class="mt-body-description"> It is a long established fact that a reader will be distracted </p>
                                                                        <ul class="mt-body-stats">
                                                                            <li class="font-green">
                                                                                <i class="icon-emoticon-smile"></i> 3,7k</li>
                                                                            <li class="font-yellow">
                                                                                <i class=" icon-social-twitter"></i> 3,7k</li>
                                                                            <li class="font-red">
                                                                                <i class="  icon-bubbles"></i> 3,7k</li>
                                                                        </ul>
                                                                        <div class="mt-body-actions">
                                                                            <div class="btn-group btn-group btn-group-justified">
                                                                                <a href="javascript:;" class="btn">
                                                                                    <i class="icon-bubbles"></i> Bookmark </a>
                                                                                <a href="javascript:;" class="btn ">
                                                                                    <i class="icon-social-twitter"></i> Share </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mt-widget-2">
                                                                    <div class="mt-head" style="background-image: url(../assets/pages/img/background/43.jpg);">
                                                                        <div class="mt-head-label">
                                                                            <button type="button" class="btn btn-danger">London</button>
                                                                        </div>
                                                                        <div class="mt-head-user">
                                                                            <div class="mt-head-user-img">
                                                                                <img src="../assets/pages/img/avatars/team3.jpg"> </div>
                                                                            <div class="mt-head-user-info">
                                                                                <span class="mt-user-name">Harry Harris</span>
                                                                                <span class="mt-user-time">
                                                                                    <i class="icon-user"></i> 3 mins ago </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-body">
                                                                        <h3 class="mt-body-title"> Christian Davidson </h3>
                                                                        <p class="mt-body-description"> It is a long established fact that a reader will be distracted </p>
                                                                        <ul class="mt-body-stats">
                                                                            <li class="font-green">
                                                                                <i class="icon-emoticon-smile"></i> 3,7k</li>
                                                                            <li class="font-yellow">
                                                                                <i class=" icon-social-twitter"></i> 3,7k</li>
                                                                            <li class="font-red">
                                                                                <i class="  icon-bubbles"></i> 3,7k</li>
                                                                        </ul>
                                                                        <div class="mt-body-actions">
                                                                            <div class="btn-group btn-group btn-group-justified">
                                                                                <a href="javascript:;" class="btn ">
                                                                                    <i class="icon-bubbles"></i> Bookmark </a>
                                                                                <a href="javascript:;" class="btn ">
                                                                                    <i class="icon-social-twitter"></i> Share </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                                <div class="portlet light portlet-fit ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-microphone font-dark hide"></i>
                                                            <span class="caption-subject bold font-dark uppercase"> Recent Projects</span>
                                                            <span class="caption-helper">default option...</span>
                                                        </div>
                                                        <div class="actions">
                                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                                <label class="btn blue btn-outline btn-circle btn-sm active">
                                                                    <input type="radio" name="options" class="toggle" id="option1">Actions</label>
                                                                <label class="btn  blue btn-outline btn-circle btn-sm">
                                                                    <input type="radio" name="options" class="toggle" id="option2">Tools</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="mt-widget-4">
                                                                    <div class="mt-img-container">
                                                                        <img src="../assets/pages/img/background/34.jpg" /> </div>
                                                                    <div class="mt-container bg-purple-opacity">
                                                                        <div class="mt-head-title"> Website Revamp & Deployment </div>
                                                                        <div class="mt-body-icons">
                                                                            <a href="#">
                                                                                <i class=" icon-pencil"></i>
                                                                            </a>
                                                                            <a href="#">
                                                                                <i class=" icon-map"></i>
                                                                            </a>
                                                                            <a href="#">
                                                                                <i class=" icon-trash"></i>
                                                                            </a>
                                                                        </div>
                                                                        <div class="mt-footer-button">
                                                                            <button type="button" class="btn btn-circle btn-danger btn-sm">Dior</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mt-widget-4">
                                                                    <div class="mt-img-container">
                                                                        <img src="../assets/pages/img/background/46.jpg" /> </div>
                                                                    <div class="mt-container bg-green-opacity">
                                                                        <div class="mt-head-title"> CRM Development & Deployment </div>
                                                                        <div class="mt-body-icons">
                                                                            <a href="#">
                                                                                <i class=" icon-social-twitter"></i>
                                                                            </a>
                                                                            <a href="#">
                                                                                <i class=" icon-bubbles"></i>
                                                                            </a>
                                                                            <a href="#">
                                                                                <i class=" icon-bell"></i>
                                                                            </a>
                                                                        </div>
                                                                        <div class="mt-footer-button">
                                                                            <button type="button" class="btn btn-circle blue-ebonyclay btn-sm">Nike</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mt-widget-4">
                                                                    <div class="mt-img-container">
                                                                        <img src="../assets/pages/img/background/37.jpg" /> </div>
                                                                    <div class="mt-container bg-dark-opacity">
                                                                        <div class="mt-head-title"> Marketing Campaigns </div>
                                                                        <div class="mt-body-icons">
                                                                            <a href="#">
                                                                                <i class=" icon-bubbles"></i>
                                                                            </a>
                                                                            <a href="#">
                                                                                <i class=" icon-map"></i>
                                                                            </a>
                                                                            <a href="#">
                                                                                <i class=" icon-cup"></i>
                                                                            </a>
                                                                        </div>
                                                                        <div class="mt-footer-button">
                                                                            <button type="button" class="btn btn-circle btn-success btn-sm">Honda</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="portlet light portlet-fit ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="icon-microphone font-dark hide"></i>
                                                            <span class="caption-subject bold font-dark uppercase"> Activities</span>
                                                            <span class="caption-helper">default option...</span>
                                                        </div>
                                                        <div class="actions">
                                                            <div class="btn-group">
                                                                <a class="btn red btn-outline btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Actions
                                                                    <i class="fa fa-angle-down"></i>
                                                                </a>
                                                                <ul class="dropdown-menu pull-right">
                                                                    <li>
                                                                        <a href="javascript:;"> Option 1</a>
                                                                    </li>
                                                                    <li class="divider"> </li>
                                                                    <li>
                                                                        <a href="javascript:;">Option 2</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:;">Option 3</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:;">Option 4</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="mt-widget-3">
                                                                    <div class="mt-head bg-blue-hoki">
                                                                        <div class="mt-head-icon">
                                                                            <i class=" icon-social-twitter"></i>
                                                                        </div>
                                                                        <div class="mt-head-desc"> Lorem Ipsum is simply dummy text of the ... </div>
                                                                        <span class="mt-head-date"> 25 Jan, 2015 </span>
                                                                        <div class="mt-head-button">
                                                                            <button type="button" class="btn btn-circle btn-outline white btn-sm">Add</button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-body-actions-icons">
                                                                        <div class="btn-group btn-group btn-group-justified">
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-align-justify"></i>
                                                                                </span>RECORD </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-camera"></i>
                                                                                </span>PHOTO </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-calendar"></i>
                                                                                </span>DATE </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-record"></i>
                                                                                </span>RANC </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mt-widget-3">
                                                                    <div class="mt-head bg-red">
                                                                        <div class="mt-head-icon">
                                                                            <i class="icon-user"></i>
                                                                        </div>
                                                                        <div class="mt-head-desc"> Lorem Ipsum is simply dummy text of the ... </div>
                                                                        <span class="mt-head-date"> 12 Feb, 2016 </span>
                                                                        <div class="mt-head-button">
                                                                            <button type="button" class="btn btn-circle btn-outline white btn-sm">Add</button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-body-actions-icons">
                                                                        <div class="btn-group btn-group btn-group-justified">
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-align-justify"></i>
                                                                                </span>RECORD </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-camera"></i>
                                                                                </span>PHOTO </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-calendar"></i>
                                                                                </span>DATE </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-record"></i>
                                                                                </span>RANC </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mt-widget-3">
                                                                    <div class="mt-head bg-green">
                                                                        <div class="mt-head-icon">
                                                                            <i class=" icon-graduation"></i>
                                                                        </div>
                                                                        <div class="mt-head-desc"> Lorem Ipsum is simply dummy text of the ... </div>
                                                                        <span class="mt-head-date"> 3 Mar, 2015 </span>
                                                                        <div class="mt-head-button">
                                                                            <button type="button" class="btn btn-circle btn-outline white btn-sm">Add</button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-body-actions-icons">
                                                                        <div class="btn-group btn-group btn-group-justified">
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-align-justify"></i>
                                                                                </span>RECORD </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-camera"></i>
                                                                                </span>PHOTO </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-calendar"></i>
                                                                                </span>DATE </a>
                                                                            <a href="javascript:;" class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <i class="glyphicon glyphicon-record"></i>
                                                                                </span>RANC </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
						
						
						
						
						
						
						
						
                        