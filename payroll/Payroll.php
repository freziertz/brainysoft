<?php
namespace Brainysoft;

class Payroll{
	
	
	
	
	private function staffSecurityFund($user_login,$baseSalary,$securityFundRatio,$employeeId){	
		
		$sqlContributionOrganization = ("SELECT empcontributionid, employeeid, contributionorganizationid FROM payrollempcontribution WHERE employeeid = ?");
		$stmtContributionOrganization = $user_login->runQuery($sqlContributionOrganization);
		$stmtContributionOrganization->execute(array($employeeId));
		$rowContributionOrganization = $stmtContributionOrganization->fetchAll();	
		
		foreach ($rowContributionOrganization as $row){
			
			$sqlContributionSetting	= ("SELECT statcontid, statcontdescription, statcontdate, statemployeeratio, statemployerratio, statcontributionbaseid, statcontributiontypeid, statorganizationid FROM payrollstatutorycontribution WHERE statcontid = ?");
			$stmtContributionSetting= $user_login->runQuery($sqlContributionSetting);
			$stmtContributionSetting->execute(array($row['contributionorganizationid']));
			$rowContributionOrganization = $stmtContributionSetting->fetchAll();
			
			
			
		}
			
	}
	
	
	private function staffHealthInsurance($baseSalary,$healthInsuranceRatio){
		return $baseSalary * $healthInsuranceRatio;
		
	}
		
	private function employerSecurityFund($baseSalary,$securityFundRatio,$employerContributionMultiplier){
		return ($baseSalary * $securityFundRatio)* $employerContributionMultiplier;
	}
	
	private function employerHealthInsurance($baseSalary,$healthInsuranceRatio,$employerContributionMultiplier){
		return $baseSalary * $healthInsuranceRatio*$employerContributionMultiplier;
		
	}
	
	private function totalSecurityFund($baseSalary,$securityFundRatio,$employerContributionMultiplier){
		return staffSecurityFund($baseSalary,$securityFundRatio) + mployerSecurityFund($baseSalary,$securityFundRatio,$employerContributionMultiplier);
	}
	
	private function totalHealthInsurance($baseSalary,$healthInsuranceRatio,$employerContributionMultiplier){
		return staffHealthInsurance($baseSalary,$healthInsuranceRatio) +  employerHealthInsurance($baseSalary,$healthInsuranceRatio,$employerContributionMultiplier);
		
	}
		
	public function payee($grossSalary,$user_login){
		
		$sqlPayeeStting= ("SELECT payeesettingid, payeesetingscale, payeesettingminimumsalary, payeesettingmaximumsalary, payeesettingdeductionratio, payeesettingoffsetvalue FROM payrollpayeesetting");
		$stmtPayeeStting= $user_login->runQuery($sqlPayeeStting);
		$stmtPayeeStting->execute(array());
		$rowPayeeStting= $stmtPayeeStting->fetchAll();		
		
		foreach ($rowPayeeStting as $row){			
		if (($grossSalary > $row['payeesettingminimumsalary']) && ($grossSalary <= $row['payeesettingmaximumsalary'])){
			return (($grossSalary- $row['payeesettingminimumsalary']) * $row['payeesettingdeductionratio']) + $row['payeesettingoffsetvalue'];
			break;
		}
		
		}
		
	}
	
	private function houseAllowance(){
		
	}
	
	private function taxablePay($grossSalary){
		return $grossSalary - totalSecurityFund($baseSalary,$securityFundRatio,$employerContributionMultiplier);
	}
	
	private function workerFund(){
		return 0.01 * $grossSalary;
	}
	
	private function advanceSalaryDuduction(){
		return $advancesalaryDeductionForThisMonth;
	}
	
	private function netPay(){
		$grossSalary - $totalcontribution - $totalloandeduction;
	}
	
	private function allowance(){
		return $totalAllowance;
	}
	
	private function benefit(){
		return $totalBenefit;
		
	}
	
	
	private function sdl($grossSalary){
		//if employee is greater than 4 and salary greater
		return $grossSalary * 0.045;
	}
	
	public function insertPayHistory($user_login,$employeeid,$date, $basicsalary, $houseallowance, $allowance, $benefit,
			$overtime, $transportallowance, $nsfemployee, $healthinsuranceemployee, $nsfemployer,
			$nsftotal, $payee, $taxablepay, $healthinsuranceemployer, $healthinsurancetotal, $workerfund,
			$advancesalarydeduction, $netpay, $month, $year, $nsfid, $healthinsuranceid){
		
	
		$sqlPayHistory=	( "INSERT INTO payrollhistory 
					(employeeid,date, basicsalary, houseallowance, plallowance, benefit, 
					overtime, transportallowance, nsfemployee, healthinsuranceemployee, nsfemployer, 
					nsftotal, payee, taxablepay, healthinsuranceemployer, healthinsurancetotal, workerfund, 
					advancesalarydeduction, netpay, month, year, nsfid, healthinsuranceid) 
					VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" );
		$stmtPayHistory= $user_login->runQuery ($sqlPayHistory);
		$a=$stmtPayHistory->execute ( array (
				
				$employeeid,$date, $basicsalary, $houseallowance, $plallowance, $benefit,
				$overtime, $transportallowance, $nsfemployee, $healthinsuranceemployee, $nsfemployer,
				$nsftotal, $payee, $taxablepay, $healthinsuranceemployer, $healthinsurancetotal, $workerfund,
				$advancesalarydeduction, $netpay, $month, $year, $nsfid, $healthinsuranceid
				
		) );
		if ($a==1){
			return $a;
		}else{
			return 0;
		}
	}
	
	private function overtime($hours){
		return $overtime;
		
	}
	
	private function basicSalary($basicPayAmount,$payCircle,$numberOfPayCircle = 1){
		
			return $numberOfPayCircle * $basicPayAmount;			
		
		
	}
	
	private function grossSalary(){
		$basicSalary + $totalallowance + $overtime;
	}
	
	private function taxablePay(){
		$basicSalary + $totalallowance + $overtime - $nsf;
	}
	
	private function houseAllowance(){
		
	}
	
	private function currency(){
		
		return $currency;
		
	}
	
	private function totalAllowance(){
		
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