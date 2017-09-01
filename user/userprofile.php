<?php 
require_once '../user/class.user.php';
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}

if(isset($_GET['id']) && isset($_GET['wtd']))
{
	$personid = $_GET['id'] ;
}



$sqlComm= ("SELECT sum(commission) as commsum FROM sales where personid = ?");
$stmtComm = $user_login->runQuery($sqlComm);
$stmtComm->execute(array($personid));
$rows=$stmtComm->fetch();
$totalcomm = $rows['commsum']=0;

$sqlKS= ("SELECT sum(kitsnumber) as onsalesum FROM kits_view where OnSale='Yes' and personid=?");
$stmtKS = $user_login->runQuery($sqlKS);
$stmtKS->execute(array($personid));
$rows=$stmtKS->fetch();
$totalavailable = $rows['onsalesum'];

$sqlSales= ("SELECT sum(amount) as salesum FROM sales where personid=?");
$stmtSales = $user_login->runQuery($sqlSales);
$stmtSales->execute(array($personid));
$rows=$stmtSales->fetch();
$totalsales = $rows['salesum'];

$sqlKits= ("SELECT sum(kitsnumber) as kitsum FROM kits");
$stmtKits = $user_login->runQuery($sqlKits);
$stmtKits->execute(array());
$rows=$stmtKits->fetch();
$totalkits = $rows['kitsum'];


$sqlRabbits= ("SELECT count(rabbitname) as rabbitsum FROM rabbit where personid=?");
$stmtRabbit = $user_login->runQuery($sqlRabbits);
$stmtRabbit->execute(array($personid));
$rows=$stmtRabbit->fetch();
$totalrabbit = $rows['rabbitsum'];


$sqlLoan= ("SELECT * FROM person where personid = ?");
$smtpLoan = $user_login->runQuery($sqlLoan);
$smtpLoan->execute(array($personid));
$rows=$smtpLoan->fetch();
$personid = $rows['personid'];
$title1= $rows['title'];
$firstname = $rows['firstname'];
$lastname = $rows['lastname'];
$fullname = $rows['firstname']. ' '. $rows['lastname'];
$gender = $rows['gender'];
$maritalstatus = $rows['maritalstatus'];
$branchid = $rows['branchid'];
$groupid = $rows['groupid'];
$namaingoid = $rows['namaingoid'];
$vibidaid= $rows['vibidaid'];
$mobile = $rows['mobile'];
$username= $rows['photopath'];
$email= $rows['useremail'];


$title="User Profile";
include('../inc/header.php'); ?>




<div class="row">	
	</div>
	<div class="panel panel-primary">
<div class="panel-heading ">
<i class="fa fa-info-circle fa-fw "></i> <h4>User Profile</h4>
</div>



<div class="row">
<h1></h1> 
  


 <div class="col-sm-2">
 <div class="panel panel-success">
    <div class="panel-heading"><?php // echo $title1.'. '.$fullname?></div>
    <div class="panel-body"><div><img src='<?php echo "../doc/".$username; ?>' alt="Mountain View" style="width:150px;height:130px;"></div></div>
  </div>
                   
                </div>
                
 <div class="col-sm-8">
 <div class="panel panel-success">
    <div class="panel-heading"><?php echo "Details for ".$title1.'. '.$fullname?></div>
    <div class="panel-body"><div>
    
    
   
<table class="table table-striped table-condensed">
   
    <tbody>
      <tr><td>First Name</td><td><?php echo $firstname;?></td>
      <td>Gender</td><td><?php echo $gender;?></td> </tr>
       <tr> <td>Last Name</td><td><?php echo $lastname;?></td> 
       <td>Marital Status</td><td><?php echo $maritalstatus;?></td>
       </tr>
       <tr> <td>Project Number</td><td><?php echo $personid;?></td>
       <td>Mobile</td><td><?php echo $mobile;?></td> </tr>
        <tr><td>Namaingo Number</td><td><?php echo $namaingoid;?></td> 
        <td>Mobile</td><td><?php echo $mobile;?></td> </tr>
        <tr><td>Branch Number</td><td><?php echo $vibidaid;?></td>
        <td>Email</td><td><?php echo $email;?></td> 
      </tr>
     
      
    </tbody>
  </table>
  
  
                    
                </div>
              
              
  
                


                </div>
                </div>
                 </div>
                  </div>
                  
                  <div class="row">
<h1></h1> 
  


 <div class="col-sm-2">
 <div class="panel panel-success">
    <div class="panel-heading">Rabbit</div>
    <div class="panel-body"><div>   
<table class="table">   
    <tbody>
     
       <tr> <td><?php echo $totalrabbit;?></td> </tr>
         
    </tbody>
  </table>              
      </div>
  </div>
  </div>
                   
                </div>
                
 <div class="col-sm-2">
 <div class="panel panel-success">
    <div class="panel-heading">Kits</div>
    <div class="panel-body"><div>   
<table class="table">   
    <tbody>
     
       <tr> <td><?php echo $totalkits;?></td> </tr>
         
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
 <div class="col-sm-2">
 <div class="panel panel-success">
    <div class="panel-heading">Sales</div>
    <div class="panel-body"><div>   
<table class="table">   
    <tbody>

       <tr> <td class="counter"><?php echo $totalsales;?></td> </tr>
   
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
<div class="col-sm-2">
 <div class="panel panel-success">
    <div class="panel-heading">Amount</div>
    <div class="panel-body"><div>   
<table class="table">   
    <tbody>
      
       <tr> <td><?php echo $totalcomm;?></td> </tr>
         
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
 <div class="col-sm-2">
 <div class="panel panel-success">
    <div class="panel-heading">Commission</div>
    <div class="panel-body"><div>   
<table class="table">   
    <tbody>
     
       <tr> <td><?php echo $totalcomm;?></td> </tr>
          
    </tbody>
  </table>              
      </div>
           </div>
                </div>
                 </div>
                 
                 
                 
                 
                  </div>
                   </div>
 

   
 
     
<?php 
$title = "Dashboard";
include('../inc/footer.php'); ?>
      
      
    

