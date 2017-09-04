<?php

namespace Brainysoft;

class FixedAsset {
	/* private function yearNumberDays($year){
		$numberOfDaysFebruaryGivenYear = cal_days_in_month(CAL_GREGORIAN,2,$year);
		if ($numberOfDaysFebruaryGivenYear == 28){
			return 365;
		}else{
			return 366;
		}		
	}	
	 */
	private function yearAdd($parchaseDate) {
		$duration = 1 . " year";
		$parchaseDate = date_create ( $parchaseDate);
		$currentDate = date_add ( $parchaseDate, date_interval_create_from_date_string ( $duration ) );
		return date_format ( $currentDate, "Y-m-d" );
	}
	
	private function depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost){		
		$totalNumberOfDaysGivenYear = $this->yearNumberDays($givenYear);		
		return $assetCost * $depreciationRatio * $givenDays / $totalNumberOfDaysGivenYear;
	}
	
	private function insertDepreciationHistory($user_login,$depreciationDate,$depreciationValue,$oldCurrentValue,$OldCummulativeDepreciation){
		$currentValue = $oldCurrentValue - $depreciationValue;
		$cummulativeDepreciation = $OldCummulativeDepreciation + $depreciationValue;
		//insert depreciation history
		$sqlAssetHistory = "INSERT INTO assetdepreciationhistory (depreciationdate,depreciationvalue, currentvalue, oldvalue, accumulatedtotalvalue) VALUES (?,?,?,?,?)";
		$insertAssetHistory = $user_login->runQuery($sqlAssetHistory);
		$insertAssetHistory->execute(array($depreciationDate,$depreciationValue,$currentValue,$oldCurrentValue,$cummulativeDepreciation));
		
	}
	
	public function fixedLineDepreciation($assetid, $user_login) {
		$sqlAsset = ("SELECT assetid,asset.assetclassid,assetcost, assetpurcharseddate, assetcurrentdepreciation, assetcummulativedepreciation, assetcurrentvalue, assetdisposaldate, assetupdatedate, expiredate,depreciationstatus,assetdepreciationratio,datediff(current_date(),
						assetpurcharseddate) AS numberofdaysincepurchase,depreciationlastrundate,
						extract(year from assetpurcharseddate) as purchasedyear,
						extract(year from current_date()) as currentyear,
						datediff(concat(extract(year from assetpurcharseddate),'-12-31'),assetpurcharseddate )as parcharsedyeardays,
						datediff(current_date(),depreciationlastrundate) as numberofdaysincelastrun FROM asset,assetclass
						WHERE assetclass.assetclassid = asset.assetclassid AND assetid = ? ");
		$smtAssert = $user_login->runQuery ( $sqlAsset );
		$smtAssert->execute ( array (
				$assetid 
		) );
		
		$rowAsset = $smtAssert->fetch ();
		//asset cost
		$assetCost = $rowAsset ['assetcost'];
		//purchased year
		$purchasedYear = $rowAsset ['purchasedyear'];
		$purchasedDate = $rowAsset ['assetpurcharseddate'];
		
		//current year
		$currentYear = $rowAsset ['currentyear'];
		//depreciation last run year
		//$lastRunYear = $rowAsset ['lastrunyear'];
		//depreciation ratio
		$depreciationRatio = $rowAsset ['assetdepreciationratio'];
		//depreciation status, this is changed when residul balance is less than depreciation value
		$depreciationStatus = $rowAsset ['depreciationstatus'];
		//
		$oldCurrentValue = $rowAsset ['assetcurrentvalue'];
		//
		$oldCummulativeDepreciation= $rowAsset ['assetcummulativedepreciation'];
		//Depreciation last run date
		$depreciationLastRunDate = $rowAsset ['depreciationlastrundate'];
		//number of days for first financial year since asset purchased
		$numberOfDayForFirstYear =  $rowAsset ['parcharsedyeardays'];
		//number of days since purchased		
		$numberOfDaysSincePurcharse =  $rowAsset ['numberofdaysincepurchase'];
		//number of days since last run date see if since last run
		
		//number of days since last run for financial year if was not run on the end of financial year
		
		//number of days since last run
		$numberOfDaysSinceLastRun =  $rowAsset ['numberofdaysincelastrun'];
	
		
		//total number of days in given year
		$totalNumberOfDays = $this->yearNumberDays($currentYear);
	
		$depreciationArray = array();	
		
		//If purchase day is equal to run day or already run,
		//no need to insert history and no need to update value
		if ($numberOfDaysSincePurcharse == 0 AND $numberOfDaysSinceLastRun == 0){
			$depreciation = 0;
			
	
		}elseif($numberOfDaysSincePurcharse > $totalNumberOfDays AND  $numberOfDaysSinceLastRun == Null AND $depreciationStatus == 1){
			//Array to old depreciation value for each year
			
			
			//			
			$totalNumberOfDaysYearPurchased = $this->yearNumberDays($purchasedYear);			
			
			if ($numberOfDaysSincePurcharse <= $totalNumberOfDaysYearPurchased){
				
				$givenYear = $purchasedYear;
				$givenDays = $numberOfDayForFirstYear;
				$depreciationDate =  $purchasedDate;
				
				$depreciationArray[] = $depreciationValue = $this->depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost);
				//$depreciationValue = $this->depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost);
				
				
				$commulativeValue = $oldCummulativeDepreciation + $depreciationValue;
 				$currentValue = $oldCurrentValue - $depreciationValue;
 				$sqlUpdate = "UPDATE asset	SET assetcummulativedepreciation = ?, assetcurrentvalue = ?
 					WHERE assetid = ?";
 				$smtAssert = $user_login->runQuery ($sqlUpdate);
				$smtAssert->execute ( array (
						$currentValue,
						$commulativeValue,
						$assetid
				) );
				
				
				
				$this->insertDepreciationHistory($user_login,$depreciationDate,$depreciationValue,$oldCurrentValue,$oldCummulativeDepreciation);
						
				
			}else{
				$givenYear = $purchasedYear;
				$givenDays = $numberOfDayForFirstYear;
				$depreciationDate = $purchasedDate;
				$depreciationArray[] = $depreciationValue = $this->depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost);
				//$depreciationValue = $this->depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost);
				
 				$commulativeValue = $oldCummulativeDepreciation + $depreciationValue;
				$currentValue = $oldCurrentValue - $depreciationValue;
				
 				$sqlUpdate = "UPDATE asset	SET assetcummulativedepreciation = ?, assetcurrentvalue = ?
					WHERE assetid = ?";
				$smtAssert = $user_login->runQuery ($sqlUpdate);
 				$smtAssert->execute ( array (
						
 						$currentValue,
 						$commulativeValue,
 						$assetid
 				) );
				
				
				$this->insertDepreciationHistory($user_login,$depreciationDate,$depreciationValue,$oldCurrentValue,$oldCummulativeDepreciation);
				
				
				//deduct number of days before financial year
				$totalNumberOfDaysYearPurchased = $totalNumberOfDaysYearPurchased - $numberOfDaysSincePurcharse;
				
				
				
				while ($numberOfDaysSincePurcharse >= $totalNumberOfDaysYearPurchased){	
 					
					$sqlAsset = ("SELECT assetid,assetcummulativedepreciation, assetcurrentvalue,assetpurcharseddate
 						 FROM asset	WHERE assetid = ? ");
					$smtAssert = $user_login->runQuery ( $sqlAsset );
					$smtAssert->execute ( array (
							$assetid
					) );
					
					$oldCurrentValue = $rowAsset ['assetcurrentvalue'];
					
					//
					$parchaseDate = $rowAsset ['assetpurcharseddate'];
					//
					$oldCummulativeDepreciation = $rowAsset ['assetcummulativedepreciation'];
					
					
					//
					$depreciationDate = $this->yearAdd($depreciationDate);
					
					$totalNumberOfDaysYearPurchased= $this->yearNumberDays($purchasedYear);
					
					$givenYear = $purchasedYear;
					$givenDays = $totalNumberOfDaysYearPurchased;
					$depreciationArray[] = $depreciationValue = $this->depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost);
					
					//$depreciationValue = $this->depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost);
					
					$commulativeValue = $oldCummulativeDepreciation + $depreciationValue;
					
					echo $commulativeValue." <br />";
					
					
					$currentValue = $oldCurrentValue - $depreciationValue;
					
					echo $currentValue." <br />";
					$sqlUpdate = "UPDATE asset	SET assetcummulativedepreciation = ?, assetcurrentvalue = ?
				WHERE assetid = ?";
				$smtAssert = $user_login->runQuery ($sqlUpdate);
				$smtAssert->execute ( array (
							
						$commulativeValue,
						$currentValue,						
						$assetid
				) );
					
					
					$this->insertDepreciationHistory($user_login,$depreciationDate,$depreciationValue,$oldCurrentValue,$oldCummulativeDepreciation);
					
					
					
					$numberOfDaysSincePurcharse = $numberOfDaysSincePurcharse - $totalNumberOfDaysYearPurchased;
					$remainingDayForCurrentYear = $numberOfDaysSincePurcharse;
					
					if (($depreciationDate + 1) == date('Y')){
						
						break;
					}
				}
				$depreciationDate = $this->yearAdd($depreciationDate);				
				$givenYear= date_format ( date_create($depreciationDate), "Y" );			
				
				//$depreciationDate = $depreciationDate + 1;
				$givenDays = $remainingDayForCurrentYear;
				$depreciationArray[] = $depreciationValue = $this->depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost);
				//$depreciationValue = $this->depreciation($givenYear,$givenDays,$depreciationRatio,$assetCost);
				$sqlAsset = ("SELECT assetid,assetcummulativedepreciation, assetcurrentvalue,assetpurcharseddate
 						 FROM asset	WHERE assetid = ? ");
				$smtAssert = $user_login->runQuery ( $sqlAsset );
				$smtAssert->execute ( array (
						$assetid
				) );
				
				$oldCurrentValue = $rowAsset ['assetcurrentvalue'];
				
				
				//
				$parchaseDate = $rowAsset ['assetpurcharseddate'];
				//
				$oldCummulativeDepreciation = $rowAsset ['assetcummulativedepreciation'];
				//
			
				$commulativeValue = $oldCummulativeDepreciation + $depreciationValue;
				$currentValue = $oldCurrentValue - $depreciationValue;
				
				$sqlUpdate = "UPDATE asset	SET assetcummulativedepreciation = ?, assetcurrentvalue = ?
				WHERE assetid = ?";
				$smtAssert = $user_login->runQuery ($sqlUpdate);
				$smtAssert->execute ( array (
						
						$currentValue,
						$commulativeValue,
						$assetid
			) );
				
				$this->insertDepreciationHistory($user_login,$depreciationDate,$depreciationValue,$oldCurrentValue,$oldCummulativeDepreciation);
				//update database				
			}
		}
	return $depreciationArray;
		
		
	}
	
	private function fixedLineCurrentValue($assetid) {
		
	}
	private function reducingBalanceDepreciation($assetid) {
	}
}

?>

