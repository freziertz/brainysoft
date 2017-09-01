<?php

require '../user/PasswordHash.php';
require_once '../inc/dbconfig.php';

class USER
{ 
	// In a real application, these should be in a config file instead
	
	
	// Base-2 logarithm of the iteration count used for userpasswordword stretching
	
	//$hash_cost_log2 = 8;
	const HASH_COST_LOG = 8;
	// Do we require the hashes to be portable to older systems (less secure)?
	//$hash_portable = FALSE;
	const HASH_PORTABLE = FALSE;
	
	// Are we debugging this code?  If enabled, OK to leak server setup details.
	//$debug = TRUE;
	const DEBUG = TRUE;

 private $db_con;

 
 public function fail($pub, $pvt = '')
 {
 	global $debug;
 	$msg = $pub;
 	if ($debug && $pvt !== '')
 		$msg .= ": $pvt";
 		/* The $pvt debugging messages may contain characters that would need to be
 		 * quoted if we were producing HTML output, like we would be in a real app,
 		 * but we're using text/plain here.  Also, $debug is meant to be disabled on
 		 * a "production install" to avoid leaking server setup details. */
 		exit("An error occurred ($msg).\n");
 }
 
 public function get_post_var($var)
 {
 	$val = $var;
 	if (get_magic_quotes_gpc())
 		$val = stripslashes($val);
 		return $val;
 }
 
public function headerc(){
header('Content-Type: text/plain');
}
 
 public function __construct()
 {
  $database = new Database();
  $db = $database->dbConnection();
  $this->db_con = $db;
    }
 
 public function runQuery($sql)
 {
  $stmt = $this->db_con->prepare($sql);
  return $stmt;
 }
 
 public function beginTransaction()
 {
 	$stmt = $this->db_con->beginTransaction();
 	return $stmt;
 }
 
 public function commit()
 {
 	$stmt = $this->db_con->commit();
 	return $stmt;
 }
 
 public function rollback()
 {
 	$stmt = $this->db_con->rollBack();
 	return $stmt;
 }
 
 public function lastID()
 {
  $stmt = $this->db_con->lastInsertId();
  return $stmt;
 }
 
 
 
 public function register($uname,$upass,$title,$firstname,$lastname,$othername,$dateofbirth,$maritalstatus,$gender,$mobile,$photopath,$email,$code)
 {
 	$utitle=$uemail=$ufirstname=$ulastname=$uothername=$ucode=$udateofbirth=$umaritalstatus=$ugender=$umobile=$uphotopath = "";
 	$username = $this->get_post_var($uname);
 	
 	/* Sanity-check the usernamename, don't rely on our use of prepared statements
 	 * alone to prevent attacks on the SQL server via malicious usernamenames. */
 	if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $username))
 		$this->fail('Invalid usernamename');
 	
 		$userpassword = $this->get_post_var($upass);
 		/* Don't let them spend more of our CPU time than we were willing to.
 		 * Besides, bcrypt happens to use the first 72 characters only anyway. */
 		if (strlen($userpassword) > 72)
 			$this->fail('The supplied userpasswordword is too long');
  try
  { 
  	$hasher = new passwordHash(USER::HASH_COST_LOG, USER::HASH_PORTABLE);
  	//$code = $hasher->Hashpassword(uniqid(rand()));
  	
  	$ucode =$code;  	
  	$hash = $hasher->Hashpassword($userpassword);
  	$utitle = $title;
  	$ufirstname = $firstname;
  	$ulastname = $lastname;
  	$uothername = $othername;
  	$udateofbirth = $dateofbirth;
  	$umaritalstatus = $maritalstatus;
  	$ugender = $gender;
  	$umobile = $mobile;
  	$uphotopath = $photopath;
  	$uemail = $email;
  
  	
  	
  	
  	
  	if (strlen($hash) < 20)
  		fail('Failed to hash new userpasswordword');
  		unset($hasher);
  	
  		$stmt = $this->db_con->prepare("INSERT INTO users(username, userpassword,title,firstname,lastname,othername,usertoken,dateofbirth,maritalstatus,gender,mobile,photopath,useremail)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
  		$a=array($username,$hash,$utitle,$ufirstname,$ulastname,$uothername,$ucode,$udateofbirth,$umaritalstatus,$ugender,$umobile,$uphotopath,$uemail);
  		if (!$stmt->execute($a)) {
  		
  			/* Figure out why this failed - maybe the usernamename is already taken?
  			 * It could be more reliable/portable to issue a SELECT query here.  We would
  			 * definitely need to do that (or at least include code to do it) if we were
  			 * supporting multiple kinds of database backends, not just MySQL.  However,
  			 * the prepared statements interface we're using is MySQL-specific anyway. */
  			if ($db_con->errno === 1062 /* ER_DUP_ENTRY */){
  				fail('This usernamename is already taken');
  				$log->addWarning('Foo \n');
  			}
  			else{
  				
  				fail('MySQL execute',$db_con->error);
  			}
  		}
  		
  		//$what = 'username created';
  		//echo $what;
  		return true;
  		
  
  }
  catch(PDOException $ex)
  {
   echo "test".$ex->getMessage();
  }
 }
 
 public function login($uname,$upass) {
 	
 	$username = $this->get_post_var($uname);
 	/* Sanity-check the usernamename, don't rely on our use of prepared statements
 	 * alone to prevent attacks on the SQL server via malicious usernamenames. */
 	if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $username))
 		$this->fail('Invalid usernamename');
 	
 		$userpassword = $this->get_post_var($upass);
 		/* Don't let them spend more of our CPU time than we were willing to.
 		 * Besides, bcrypt happens to use the first 72 characters only anyway. */
 		if (strlen($userpassword) > 72)
 			$this->fail('The supplied userpasswordword is too long');
  try
  {
  	
  	$hasher = new passwordHash(USER::HASH_COST_LOG, USER::HASH_PORTABLE);
  	
  	$hash = '*'; // In case the username is not found
  	$stmt = $this->db_con->prepare('select userid,userpassword,userstatus from users where username=?');
  	$stmt->execute(array($username));
  	$row  = $stmt->fetch();
  	$hash = $row['userpassword'];
  	$validity  = $row['userstatus'];
  	$userid = $row['userid'];
  	
  	if ($hasher->CheckPassword($userpassword, $hash)) {
  		if($validity === "Y"){
  			$_SESSION['userSession'] = $userid;
  			$what = 'Authentication succeeded';
  			return true;
  	
  		}else{
  			header("Location: index.php?inactive");
  			echo $validity;
  			exit;
  		}
  			
  	
  	} else {
  		$what = 'Authentication failed wrong password or username';
  		header("Location: index.php?error");
  		echo $what;
  		$op = 'fail'; // Definitely not 'change'
  		//exit;
  	
  	
  	}}
  catch(PDOException $ex)
  {
   echo $ex->getMessage();
  }
 }
 
 public function changePassword($uname,$upass,$unewpass){
 	$newpassword = $this->get_post_var($unewpass);
 	if (strlen($newpassword) > 72)
 		fail('The new userpasswordword is too long');
 	
 		$hasher = new passwordHash(USER::HASH_COST_LOG, USER::HASH_PORTABLE);
 		$hash = $hasher->Hashpassword($newpassword);
 		if (strlen($hash) < 20)
 			$this->fail('Failed to hash new userpasswordword');
 			unset($hasher);
 			
 			if ($this->login($uname,$upass)){
 				try{
 	
 			$stmt = $this->db_con->prepare('update users set userpassword=? where username=?');
 			$stmt->execute(array($hash,$username));
 			if($stmt->execute())
 				$what = 'userpassword word changed';
 			
 			}
 			catch(PDOException $ex)
 			{
 				echo $ex->getMessage();
 			}
 			}
 	
 		
 }
 	
 
 public function forgetPassword($uname,$upass){
 	$username = $this->get_post_var($uname);
 	/* Sanity-check the usernamename, don't rely on our use of prepared statements
 	 * alone to prevent attacks on the SQL server via malicious usernamenames. */
 	if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $username))
 		$this->fail('Invalid usernamename');
 	
 		$userpassword = $this->get_post_var($upass);
 		/* Don't let them spend more of our CPU time than we were willing to.
 		 * Besides, bcrypt happens to use the first 72 characters only anyway. */
 		if (strlen($userpassword) > 72)
 			$this->fail('The supplied userpasswordword is too long');
 		
 	try {
 		$hasher = new passwordHash(USER::HASH_COST_LOG, USER::HASH_PORTABLE);
 		$hash = $hasher->Hashpassword($userpassword);
 		$stmt = $this->db_con->prepare('update users set userpassword=? where username=?');
 		$stmt->execute(array($hash,$username));
 		if ($stmt->execute())
 			$what = 'userpassword word changed';
 			//$op = 'fail'; // Definitely not 'change'
 	
 	}
 	catch(PDOException $ex)
 	{
 		echo $ex->getMessage();
 	}
 	}
 

  	
 
 
 
 public function is_logged_in()
 {
  if(isset($_SESSION['userSession']))
  {
   return true;
  }
 }
 
 public function redirect($url)
 {
  header("Location: $url");
 }
 
 public function logout()
 {
  session_destroy();
  $_SESSION['userSession'] = false;
 }
 
public function send_mail($email,$message,$subject)
 {
 	
  require '../lib/vendor/autoload.php';
  
  $mail = new PHPMailer();
  $mail->IsSMTP(); 
  $mail->SMTPDebug  = 0;                     
  $mail->SMTPAuth   = true;                  
  $mail->SMTPSecure = "ssl";                 
  $mail->Host       = "smtp.gmail.com";      
  $mail->Port       = 465;             
  $mail->AddAddress($email);
  $mail->Username="freziern@gmail.com";  
  $mail->Password="mungumkuu7591";            
  $mail->SetFrom('freziern@gmail.com','Brainy Loan');
  $mail->AddReplyTo("freziern@gmail.com","Brainy Loan");
  $mail->Subject    = $subject;
  $mail->MsgHTML($message);
  $mail->Send();
 } 
 
 public function importcsv($filename){
 	
 	
 	$query = <<<eof
    LOAD DATA INFILE '$fileName'
     INTO TABLE tableName
     FIELDS TERMINATED BY '|' OPTIONALLY ENCLOSED BY '"'
     LINES TERMINATED BY '\n'
    (field1,field2,field3,etc)
eof;
 	
 	$stmt = $this->db_con->prepare($query);
 	$stmt->execute();
 	
 }
}