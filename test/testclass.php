<?php
//namespace Brainysoft;
class Test{

	public function insertTest($user_login,$testname){
		
		//Start Inserting Event into the Database
		$sqlTest =( "INSERT INTO testtable (tastetablevalue) VALUES (?)" );
		$insertTest = $user_login->runQuery ($sqlTest);
		$a=$insertTest->execute ( array (
				
				$testname
				
		) );
		if ($a==1){
			return $a;
		}else{
			return 0;
		}
	}
	public function updatetest($user_login,$testid,$testname){
		//Start Inserting Event into the Database
		$sqlTest =( "UPDATE testtable SET tastetablevalue = ? WHERE tasttableid = ?" );
		$updateTest = $user_login->runQuery ($sqlTest);
		$a=$updateTest->execute ( array (
				
				$testname,
				$testid
				
		) );
		if ($a==1){
			return $a;
		}else{
			return 0;
		}
		
	}
	public function deletetest($user_login,$testid){		
		$sqlTest =( "DELETE FROM testtable WHERE tasttableid = ?" );
		$deleteTest = $user_login->runQuery ($sqlTest);
		$a=$deleteTest->execute ( array (				
				
				$testid
				
		) );
		if ($a==1){
			return $a;
		}else{
			return 0;
		}
		
	}
	public function viewtest($user_login){
		$sqlTest =( "SELECT * FROM testtable" );
		$stmtSelectTest = $user_login->runQuery ($sqlTest);
		$stmtSelectTest->execute ( array () );
		$row = $stmtSelectTest->fetchAll();		
		return $row;
		
	}
	
	public function viewSpecific($user_login,$testid){
		$sqlTest =( "SELECT * FROM testtable WHERE tasttableid = ?" );
		$stmtSelectTest = $user_login->runQuery ($sqlTest);
		$stmtSelectTest->execute ( array ($testid) );
		$row = $stmtSelectTest->fetch();		
		return $row;
		
	}
	
	
	
}