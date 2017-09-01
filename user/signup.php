<?php
require_once '../user/class.user.php';
require ('../inc/fileUploader.php');
session_start();
$reg_user = new USER();

if($reg_user->is_logged_in())
{
	$reg_user->redirect('../user/index.php');
}

if(isset($_POST['btn-signup']))
{
$uname = trim($_POST['username']);
 $email = trim($_POST['email']);
 $upass = trim($_POST['password']);
 $title = trim($_POST['title']);
 $firstname = trim($_POST['firstname']);
 $othername = trim($_POST['othername']);
 $lastname = trim($_POST['lastname']);
  if (isset($_POST['dateofbirth'])){
  $dateofbirth = trim($_POST['dateofbirth']);
  }else {
	 $dateofbirth = null;
 }
  
 if (isset($_POST['maritalstatus'])){
 $maritalstatus= trim($_POST['maritalstatus']);
 }else {
	 $maritalstatus = null;
 }
 
 if (isset($_POST['gender'])){
 $gender = trim($_POST['gender']);
 
 }else {
	$gender= null;
 }
 $mobile = trim($_POST['mobile']);
 $code = md5(uniqid(rand()));
 
 
 $flup = new fileUploader\fileUploader ();
 $perPhoto = $flup->upload ( "kilelenew/doc/docpath/", $_FILES ['photopath'], $uname );
 $photopath = $GLOBALS ['nameOfFile'];
 
 $stmt = $reg_user->runQuery("SELECT * FROM users WHERE username=:username");
 $stmt->execute(array(":username"=>$uname));
 $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
 if($stmt->rowCount() > 0)
 {
  $msg = "
        <div class='alert alert-error'>
    <button class='close' data-dismiss='alert'>&times;</button>
     <strong>Sorry !</strong>  User allready exists , Please Try another one
     </div>
     ";
 }
 else
 {
  if($reg_user->register($uname,$upass,$title,$firstname,$lastname,$othername,$dateofbirth,$maritalstatus,$gender,$mobile,$photopath,$email,$code))
  {
  	
  $id = $reg_user->lastID();  
 
   $key = base64_encode($id);
   $id = $key;
   
   $flup = new fileUploader\fileUploader ();
   $perPhoto = $flup->upload ( "usercomp/doc/", $_FILES ['photopath'], $uname );
   $photopath = $GLOBALS ['nameOfFile'];
   

   
   $message = "     
      Hello $lastname,
      <br /><br />
      Welcome to Brainy Rabbit Apps!<br/>
      To complete your registration  please , just click following link<br/>
      <br /><br />
      <a href='http://localhost/kilelenew/user/verify.php?id=$id&code=$code'>Click HERE to Activate :)</a>
      <br /><br />
      Thanks,";
      
   $subject = "Confirm Registration";
   
   echo '<script type="text/javascript"> alert("Member Created Successfully.");</script>';
   
  $reg_user->send_mail($email,$message,$subject); 
   $msg = "
     <div class='alert alert-success'>
      <button class='close' data-dismiss='alert'>&times;</button>
      <strong>Success!</strong>  We've sent an email to $email.
                    Please click on the confirmation link in the email to create your account. 
       </div>
     ";
  }
  else
  {
   echo "sorry , Query could no execute...";
  }  
 }
}
?>

<head>
    <title>Kilele Loan Apps</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  </head>

    <?php
    
    
 //include('../inc/header.php');
 
 if(isset($msg)) echo $msg;  ?>

<div class="row">

  <div class="col-sm-12">
  
  <div class="panel panel-primary">
      <div class="panel-heading"><h4>Register</h4></div>
      <div class="panel-body">
      
      
 <div class="col-sm-6">
    
 <form class="form-horizontal" name="allocate"
						onsubmit="return checkForm(this);" accept-charset="utf-8"
						method="post" enctype="multipart/form-data">
						<input type="hidden" name="op" value="new">   
    <!--  start row1 -->
<div class="row">

<!--  start row1 with three column -->


  
  <div class="col-sm-12">
  <div class="form-group">
	<label class="control-label col-sm-4" for="cell">Title</label>
	<div class="col-sm-4">
	 <select class="form-control"	name="title" >
		<option value="" disabled selected>Title</option>
		<option value="Mr">Mr</option>
		<option value="Mrs">Mrs</option>
		<option value="Dr">Dr</option>
		<option value="Prof">Prof</option>

				</select>
			</div>
			</div>
			</div>
								
									
 <div class="col-sm-12">
  <div class="form-group ">
 
						<label  class="control-label col-sm-4">First Name *</label>
						<div class="col-sm-8">
						<div class="input-group">											
							<input type="text" placeholder="First Name" pattern="[A-Za-z]+"
								class="form-control" name="firstname" required>
						</div>
							</div>
					</div>
					</div>
<div class="col-sm-12">
  <div class="form-group">
 
	<label  class="control-label col-sm-4">Middle Name</label>
						<div class="col-sm-6">
						<div class="input-group">											
							<input type="text" placeholder="Other Name" pattern="[A-Za-z]+"
								class="form-control" name="othername" >
					
						</div>				</div>
									</div>
									</div>

 <div class="col-sm-12">
   <div class="form-group ">
	<label  class="control-label col-sm-4">Last Name *</label>
<div class="col-sm-8">
	<div class="input-group">											
	<input type="text" placeholder="Last Name" pattern="[A-Za-z]+"
	class="form-control" name="lastname" required>
		</div>
		</div>
		</div>
	</div>

  <div class="col-sm-12">
  <div class="form-group ">

		<label class="control-label col-sm-4">Date of Birth</label>
		<div class="col-sm-8">
			<div class="input-group">										
				<input type="date" placeholder="Date of Birth"
					class="form-control" name="dateofbirth" >
					</div>
				</div>
			
			</div>
			</div>
  <div class="col-sm-12"> 
  <div class="form-group ">
		
										<label  class="control-label col-sm-4">Gender</label>
											<div class="col-sm-4">
										 <select class="form-control"
											
											name="gender" >
											<option value="" disabled selected>Gender</option>
											<option value="Male">Male</option>
											<option value="Female">Female</option>

										</select>
									</div>
									
			</div>
			
			</div>
			
<div class="col-sm-12">
  <div class="form-group ">
  
		<label class="control-label col-sm-4">Marital Status</label>
<div class="col-sm-4">
							 <select class="form-control"	name="maritalstatus" >
								<option value="" disabled selected>Marital Status</option>
								<option value="Married">Married</option>
								<option value="Single">Single</option>
								<option value="Divorced">Divorced</option>
								

										</select>
									</div>
									</div>
				</div>
		
			
  
  <div class="col-sm-12">

<div class="form-group ">
						<label  class="control-label col-sm-4">Email *</label>
						<div class="col-sm-8">
						<div class="input-group">											
							<input type="email" placeholder="Email"
								class="form-control" name="email" required>
						</div>
					</div>
					</div>
					</div>

<!--  start row1 with three column -->

<!--  end row1 --> 
</div>
<!--  start row2 -->
<div class="row">

<!--  start row1 with three column -->
  
								
  
			
  <div class="col-sm-12">
<div class="form-group ">

						<label  class="control-label col-sm-4">Mobile *</label>
						<div class="col-sm-8">
						<div class="input-group">											
							<input type="number" placeholder="Mobile"
								class="form-control" name="mobile" required>
						</div>
						</div>
					
					</div>
</div>
<!--  start row1 with three column -->

<!--  end row2 --> 
</div>
<!--  start row3 -->
<div class="row">

<!--  start row1 with three column -->
  
  
			
  <div class="col-sm-12">

<div class="form-group ">
						<label  class="control-label col-sm-4">Username *</label>
						<div class="col-sm-8">
						<div class="input-group">											
							<input type="text" placeholder="User Name" pattern="[A-Za-z]+"
								class="form-control" name="username" >
						</div>
				
					</div>
					</div>
</div>
<!--  start row1 with three column -->

<!--  end row3 --> 
</div>
<!--  start row4 -->
<div class="row">

<!--  start row1 with three column -->
  <div class="col-sm-12">
 
<div class="form-group ">
						<label  class="control-label col-sm-4">Password *</label>
						<div class="col-sm-8">
						<div class="input-group">											
							<input type="password" placeholder="Password"
								class="form-control" name="password" >
						</div>
						</div>
					</div>
					</div>


					
								
  <div class="col-sm-12">

  <div class="form-group ">
		<label class="control-label col-sm-4">Photopath *</label> 
		<div class="col-sm-8">
			<div class="input-group">										
				<input type="file" placeholder="Photo Path"
					class="form-control" name="photopath" required>
					</div>
				</div>
			</div>
		
			</div>
 

<!--  start row1 with three column -->

      <div class="form-group"> 
       <div class="form-group"> 
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-large btn-primary" name="btn-signup">Sign Up</button>
       <a href="index.php" class="btn btn-large">Sign In</a>
    </div>
  </div>
   </div>
        
      </form>
   

    </div>
    
    <!-- /container -->
    
    
<?php //include('../inc/footer.php'); ?>
