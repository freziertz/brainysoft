<?php

namespace Brainysoft;

class FixedAsset {
	const END_FINACIAL_MONTH = 12;
	const START_FINANCIAL_MONTH = 01;
	const YEAR_TOTAL_MONTHS = 12;
	
	private function yearAdd($parchaseDate) {
		$duration = 1 . " year";
		$parchaseDate = date_create ( $parchaseDate );
		$currentDate = date_add ( $parchaseDate, date_interval_create_from_date_string ( $duration ) );
		return date_format ( $currentDate, "Y-m-d" );
	}
	
	private function oneMonthAdd($date) {
		$date = date_format ( $date, 'Y-m-d' );
		$duration = 1 . " month";
		$date = date_create ( $date );
		$date = date_add ( $date, date_interval_create_from_date_string ( $duration ) );
		return $date;
	}
	
	private function depreciation($givenMonths, $depreciationRatio, $assetCost) {
		return $assetCost * $depreciationRatio * $givenMonths / 12;
	}
	
	private function depreciationPerYear($depreciationRatio, $assetCost) {
		return $assetCost * $depreciationRatio;
	}
	
	/*
	 * function period_diff($in_dateLow, $in_dateHigh) {
	 *
	 * if ($in_dateLow > $in_dateHigh) {
	 * $tmp = $in_dateLow;
	 * $in_dateLow = $in_dateHigh;
	 * $in_dateHigh = $tmp;
	 * }
	 *
	 * $dateLow = $in_dateLow;
	 * $dateHigh = strftime('%m/%Y', $in_dateHigh);
	 *
	 * $periodDiff = 0;
	 * while (strftime('%m/%Y', $dateLow) != $dateHigh) {
	 * $periodDiff++;
	 * $dateLow = strtotime('+1 month', $dateLow);
	 * }
	 *
	 * return $periodDiff;
	 * }
	 */
	private function period_diff($in_dateLow, $in_dateHigh) {
		if ($in_dateLow > $in_dateHigh) {
			$tmp = $in_dateLow;
			$in_dateLow = $in_dateHigh;
			$in_dateHigh = $tmp;
		}
		
		$dateLow = date_create ( $in_dateLow );
		$dyl = date_format ( $dateLow, "Y" );
		$dml = date_format ( $dateLow, "m" );
		$dateLow = $dyl . $dml . "01";
		$dateLow = date_create ( $dateLow );
		
		$dateHigh = date_create ( $in_dateHigh );
		$dyh = date_format ( $dateHigh, "Y" );
		$dmh = date_format ( $dateHigh, "m" );
		$dateHigh = $dyh . $dmh . "01";
		$dateHigh = date_create ( $dateHigh );
		
		$periodDiff = 0;
		while ( ($dateLow) != ($dateHigh) ) {
			$periodDiff ++;
			$dateLow = $this->oneMonthAdd ( $dateLow );
		}
		
		return $periodDiff;
	}
	
	private function insertDepreciationHistory($assetid, $user_login, $depreciationDate, $depreciationValue, $currentValue, $cummulativeDepreciation, $oldCurrentValue, $oldCummulativeDepreciation) {
		$sqlAssetHistory = "INSERT INTO assetdepreciationhistory (assetid,depreciationdate,depreciationvalue, currentvalue, oldvalue, accumulatedtotalvalue) VALUES (?,?,?,?,?,?)";
		$insertAssetHistory = $user_login->runQuery ( $sqlAssetHistory );
		$insertAssetHistory->execute ( array (
				$assetid,
				$depreciationDate,
				$depreciationValue,
				$currentValue,
				$oldCurrentValue,
				$cummulativeDepreciation 
		) );
	}
	
	private function selectAsset($assetid, $user_login) {
		$sqlAsset = ("SELECT assetid,assetcost, assetpurcharseddate, assetcurrentdepreciation, assetcummulativedepreciation, assetcurrentvalue, assetdisposaldate,expiredate,depreciationstatus,assetdepreciationratio,
						depreciationlastrundate,depreciationperyear
						FROM asset,assetclass
						WHERE assetclass.assetclassid = asset.assetclassid AND assetid = ? ");
		$smtAssert = $user_login->runQuery ( $sqlAsset );
		$smtAssert->execute ( array (
				$assetid 
		) );
		
		$rowAsset = $smtAssert->fetch ();
		return $rowAsset;
	}
	
	private function updateAssetDepreciation($assetid, $user_login,$lastRunDate, $cummulativeDepreciation, $currentValue) {
		$sqlUpdate = "UPDATE asset	SET depreciationlastrundate = ?, assetcummulativedepreciation = ?, assetcurrentvalue = ?
				WHERE assetid = ?";
		$smtAssert = $user_login->runQuery ( $sqlUpdate );
		$smtAssert->execute ( array (
				
				$lastRunDate,
				$cummulativeDepreciation,
				$currentValue,
				$assetid 
		) );
	}
	
	public function fixedLineDepreciation($assetid, $user_login, $runDate) {
		echo "Setting ...............<br />";
		
		$rowAsset = $this->selectAsset ( $assetid, $user_login );
		// asset cost
		$assetCost = $rowAsset ['assetcost'];
		// last run date: for first time this is set as purchasing date
		$lastRunDate = $rowAsset ['depreciationlastrundate'];
		// depreciation ratio
		$depreciationRatio = $rowAsset ['assetdepreciationratio'];
		// depreciation status, this is changed when residul balance is less than depreciation value
		$depreciationStatus = $rowAsset ['depreciationstatus'];
		// current value
		$currentValue = $rowAsset ['assetcurrentvalue'];
		// depreciation value per year
		$depreciationValuePerYear = $rowAsset ['depreciationperyear'];
		// cummulative depreciation
		$cummulativeDepreciation = $rowAsset ['assetcummulativedepreciation'];
		
		$endfinancial = "-12-31";
		
		echo "150 Last run date " . $lastRunDate . "<br />";
		
		$totalMonthSinceLastRun = $this->period_diff ( $lastRunDate, $runDate );
		
		echo "155 Total months since last run is " . $totalMonthSinceLastRun . "<br />";
		
		$yearLastRun = date_format ( date_create ( $lastRunDate ), "Y" );
		
		$endOfFinancialYearOfLastRun = date_create ( $yearLastRun . $endfinancial );
		
		echo "163 End financial year for last run date " . date_format ( $endOfFinancialYearOfLastRun, "Y-m-d" ) . "<br />";
		
		$depreciationArray = array ();
		
		// If purchase day is equal to run day or already run,
		// no need to insert history and no need to update value
		if ($totalMonthSinceLastRun == 0) {
			echo "175 no depreciation last run equal to run month or purchase month <br />";
			$depreciation = 0;
			
			echo "178 Debug first If purchase day is equal to run day or already run,
		//no need to insert history and no need to update value 111 <br />";
		} elseif (date_create ( $runDate ) <= $endOfFinancialYearOfLastRun and $depreciationStatus == 1) {
			
			echo "183 End financial year for last run date " . date_format ( $endOfFinancialYearOfLastRun, 'Y-m-d' );
			echo "<br />";
			echo "185 Run date is " . $runDate;
			echo "<br />";
			echo "186 Debug run date is less than end of financial year of last run 222 <br />";
			
			$givenYear = date_format ( date_create ( $runDate ), "Y" );
			$givenMonths = $totalMonthSinceLastRun;
			$depreciationDate = $runDate;
			$lastRunDate = $runDate;
			
			echo "193 number of month since last run is " . $givenMonths . "<br />";
			echo "194 Depreciation date is " . $depreciationDate . "<br />";
			echo "195 last run is " . $lastRunDate . "<br />";
			
			$depreciationArray [] = $depreciationValue = $this->depreciation ( $givenMonths, $depreciationRatio, $assetCost );
			$depreciationValuePerYear = $this->depreciationPerYear ( $depreciationRatio, $assetCost );
			
			echo "203 Depreciation value is " . $depreciationValue . "<br />";
			
			echo "205 Current value is " . $currentValue . "<br />";
			
			$oldCurrentValue = $currentValue;
			$oldCummulativeDepreciation = $cummulativeDepreciation;
			$cummulativeDepreciation = $cummulativeDepreciation + $depreciationValue;
			// Calculate residual Amount and check if residual Amount is
			$residueAmount = $currentValue - $depreciationValuePerYear;
			$currentValue = $currentValue - $depreciationValue;
			
			echo "215 Residual amount is " . $residueAmount . "<br />";
			
			if ($residueAmount > 0) {
				
				$this->updateAssetDepreciation ( $assetid, $user_login, $depreciationDate,$cummulativeDepreciation, $currentValue );
				$this->insertDepreciationHistory ( $assetid, $user_login, $depreciationDate, $depreciationValue, $currentValue, $cummulativeDepreciation, $oldCurrentValue, $oldCummulativeDepreciation );
			} else {
				echo "217 Residual value is reached <br />";
			}
		} else {
			
			echo "230 Number of months greater than 12  <br />";
			
			$givenYear = $year = date_format ( date_create ( $lastRunDate ), "Y" );
			$month = date_format ( date_create ( $lastRunDate ), "m" );
			$day = date_format ( date_create ( $lastRunDate ), "d" );
			$endFinancialYearDate = $year . $endfinancial;
			$depreciationDate = $endFinancialYearDate;
			$lastRunDate = $lastRunDate;
			echo " last run date " . $lastRunDate . "<br />";
			$givenMonths = $numberOfMonthsEndOfYear = $this->period_diff ( $lastRunDate, $endFinancialYearDate );
			
			echo "241 number of month end of year " . $numberOfMonthsEndOfYear . "<br />";
			
			$depreciationValue = $this->depreciation ( $givenMonths, $depreciationRatio, $assetCost );
			
			$oldCurrentValue = $currentValue;
			$oldCummulativeDepreciation = $cummulativeDepreciation;
			$cummulativeDepreciation = $cummulativeDepreciation + $depreciationValue;
			$residueAmount = $currentValue - $depreciationValue;
			$currentValue = $currentValue - $depreciationValue;
			if ($residueAmount > 0) {
				$depreciationArray [] = $depreciationValue;
				$this->updateAssetDepreciation ( $assetid, $user_login,$depreciationDate,$cummulativeDepreciation, $currentValue );
				$this->insertDepreciationHistory ( $assetid, $user_login, $depreciationDate, $depreciationValue, $currentValue, $cummulativeDepreciation, $oldCurrentValue, $oldCummulativeDepreciation );
			} else {
				echo "residual amount is reached " . $residueAmount;
			}
			
			echo "256 Total number of month before " . $totalMonthSinceLastRun . "<br />";
			// deduct number of days before financial year
			$totalMonthSinceLastRun = $totalMonthSinceLastRun - $numberOfMonthsEndOfYear;
			
			echo "256 Total number of month after just before loop " . $totalMonthSinceLastRun . "<br />";
			
			while ( $totalMonthSinceLastRun >= 12 ) {
				
				echo "268 Inside loop " . $totalMonthSinceLastRun . "<br />";
				
				$givenYear = $givenYear + 1;
				echo "given year is " . $givenYear . "<br />";
				$givenMonths = 12;
				$depreciationDate = $givenYear.$endfinancial;
				$depreciationValue = $this->depreciation ( $givenMonths, $depreciationRatio, $assetCost );
				
				// select statement to know current value
				$rowAsset = $this->selectAsset ( $assetid, $user_login );
				
				$oldCurrentValue = $currentValue = $rowAsset ['assetcurrentvalue'];
				$oldCummulativeDepreciation = $cummulativeDepreciation = $rowAsset ['assetcummulativedepreciation'];
				;
				$cummulativeDepreciation = $cummulativeDepreciation + $depreciationValue;
				$residueAmount = $currentValue - $depreciationValue;
				$currentValue = $currentValue - $depreciationValue;
				
				if ($residueAmount > 0) {
					$depreciationArray [] = $depreciationValue;
					$this->updateAssetDepreciation ( $assetid, $user_login, $depreciationDate,$cummulativeDepreciation, $currentValue );
					
					$this->insertDepreciationHistory ( $assetid, $user_login, $depreciationDate, $depreciationValue, $currentValue, $cummulativeDepreciation, $oldCurrentValue, $oldCummulativeDepreciation );
					$totalMonthSinceLastRun = $totalMonthSinceLastRun - 12;
					$remainingMonthsCurrentYear = $totalMonthSinceLastRun;
				} else {
					$remainingMonthsCurrentYear = $totalMonthSinceLastRun;
					echo "Residual amount is reached. " . $residueAmount . "<br />";
					break;
				}
				// update database
				echo $totalMonthSinceLastRun;
				
				echo "<br />";
				echo "remaining month for current year " . $remainingMonthsCurrentYear;
				echo "<br />";
				$nextYear = $givenYear + 1;
				echo "Next year " . $nextYear . "<br />";
				$currentYear = date ( 'Y' );
				if ($nextYear == $currentYear) {
					break;
				}
			}
			
			echo "after loop   555 <br />";
			// $depreciationDate = $this->yearAdd($depreciationDate);
			// $givenYear= date_format ( date_create($depreciationDate), "Y" );
			$givenYear = $givenYear + 1;
			
			$depreciationDate = $runDate;
			echo "current year is " . $givenYear;
			
			// $depreciationDate = $depreciationDate + 1;
			$givenMonths = $remainingMonthsCurrentYear;
			$depreciationValue = $this->depreciation ( $givenMonths, $depreciationRatio, $assetCost );
			
			// SELECT current value
			$rowAsset = $this->selectAsset ( $assetid, $user_login );
			
			$oldCurrentValue = $currentValue = $rowAsset ['assetcurrentvalue'];
			$oldCummulativeDepreciation = $cummulativeDepreciation = $rowAsset ['assetcummulativedepreciation'];
			$cummulativeDepreciation = $cummulativeDepreciation + $depreciationValue;
			$currentValue = $currentValue - $depreciationValue;
			
			$oldCurrentValue = $currentValue;
			$oldCummulativeDepreciation = $cummulativeDepreciation;
			$cummulativeDepreciation = $cummulativeDepreciation + $depreciationValue;
			$currentValue = $currentValue - $depreciationValue;
			
			$residueAmount = $currentValue - $depreciationValue;
			$currentValue = $currentValue - $depreciationValue;
			
			if ($residueAmount > 0) {
				$depreciationArray [] = $depreciationValue;
				$this->updateAssetDepreciation ( $assetid, $user_login, $depreciationDate,$cummulativeDepreciation, $currentValue );
				
				$this->insertDepreciationHistory ( $assetid, $user_login, $depreciationDate, $depreciationValue, $currentValue, $cummulativeDepreciation, $oldCurrentValue, $oldCummulativeDepreciation );
			} else {
				echo "Residual amount is reached. " . $residueAmount . "<br />";
			}
			// update database
		}
		
		return $depreciationArray;
	}
	private function fixedLineCurrentValue($assetid) {
	}
	private function reducingBalanceDepreciation($assetid) {
	}
}

?>

