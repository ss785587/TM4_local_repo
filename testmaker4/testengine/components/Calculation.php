<?php

class Calculation{
	
	/** member variables */
	private $testRunObj = null;
	
	/** operator constants */
	const SEPERATOR = '.';
	const VARIABLE_FLAG = "#";
	const EQUAL_OPERATOR = '$eq';
	const SUM_OPERATOR = '$sum';
	const MULTIPLY_OPERATOR = '$multiply';
	const MEAN_OPERATOR = '$mean';
	const MEDIAN_OPERATOR = '$median';
	
	/** constructor */
	public function __construct($testRunObj){
		if(!isset($testRunObj)){
			throw new Exception('Could not create Calulation.class object');
		}
		$this->testRunObj = $testRunObj;
	}
	
	/**
	 * Resolves and executes calculation.
	 * 
	 * @param array $paraArray
	 * @throws CException thrown, if given array is null or empty
	 * @return result, after calculaton
	 */
	public function resolveCalculation($paraArray){
		if(!isset($paraArray) || count($paraArray)==0){
			throw new CException('JSON Exception - Invalid operator');
		}
		$paraArray = $this->checkAndResolveValues($paraArray);
		switch ($paraArray[0]) {
			case self::EQUAL_OPERATOR:
				return $this->operatorEqual(array_slice($paraArray,1));
			case self::SUM_OPERATOR:
				return $this->operatorSum(array_slice($paraArray,1));
			break;
			case self::MULTIPLY_OPERATOR:
				return $this->operatorMultiply(array_slice($paraArray,1));
				break;
			case self::MEAN_OPERATOR:
				return $this->operatorMean(array_slice($paraArray,1));
				break;
			case self::MEDIAN_OPERATOR:
				return $this->operatorMedian(array_slice($paraArray,1));
				break;
			default:
				return 'NOT DEFINED';
			break;
		}
	}
	
	/**
	 * Checks if in the given array are variables with have to be resolved. 
	 * If yes, they will be resolved and saved in the given array.
	 * @param array $elements array of values
	 */
	private function checkAndResolveValues($elements){
		//resolve elements, if necessary
		for($i=0; $i<count($elements); $i++){
			$value = $elements[$i];
			if(strpos($value, self::SEPERATOR)){
				//resolve links to values like items.item1.value
				$value = $this->resolveValue($value);
			}
			//strpos returns 0, if a math is found at the beginning, else false
			if(strpos($value, self::VARIABLE_FLAG)===0){
				//resolve variable like #var1
				$value = $this->resolveVariable($value);
			}
			//remove element if it's null
			if(!isset($value)){
				unset($elements[$i]);
			}else{
				$elements[$i] = $value;
			}
		}
		return $elements;
	}
	
	private function resolveVariable($value){
		//cut flag #
		$value = substr($value, 1);
		$tmpVal = $this->testRunObj->getPropValue('variables',$value);
		if(isset($tmpVal)){
			return $tmpVal->value;
		}
		return null;
	}
	
	/**
	 * Resolves a value within the given testrun. If it's impossible to resolve, null will be returned.
	 * @param string $value that should be resolved
	 * @return Object if resolve was successfully, object or array will be returned. Otherwise null will be returned
	 */
	private function resolveValue($value){
		$exploded = explode(self::SEPERATOR, $value);
		$tmpObj = $this->testRunObj;
		foreach ($exploded as $key) {
			//check if $key exists as property or if it's existing as array value
			if(is_object($tmpObj) && property_exists($tmpObj,$key)){
				$tmpObj = $tmpObj->$key; 
			}
			elseif(is_array($tmpObj)){
				$tmpArrVal = self::getNamedValueInArray($tmpObj, $key);
				if(isset($tmpArrVal)){
					$tmpObj = $tmpArrVal;
				}
			}else{
				return null;
				//throw new CException("JSON Error - Can't resolve ".$value);
			}
		}
		return $tmpObj;
	}
	
	/**
	 * Iterates over array and compare given name with name values of array. 
	 * @param array $arr 
	 * @param string $valueName
	 * @return object|NULL
	 */
	private static function getNamedValueInArray($arr, $valueName){
		if(isset($arr) && isset($valueName) && is_array($arr)){
			foreach ($arr as $value){
				if($valueName === $value->name){
					return $value;
				}
			}
		}
		return null;
	}
	
	//========================================== Operators ==================================================
	
	/**
	 * Checks, if all given elements are equal. 
	 * @param array $elements
	 * @return boolean true, if all elements are equl
	 */
	private function operatorEqual($elements){
		$first = $elements[0];
		foreach($elements as $value){
			if($first != $value){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Sums up all given elements. If one element can't resolved, it will be ignored.
	 * @param array $elements
	 * @return integer sum of all elements
	 */
	private function operatorSum($elements){
		$result = 0;
		foreach($elements as $value){
			$result += $value;
		}
		return $result;
	}
	
	/**
	 * Multiplies all given elements. If one element can't resolved, it will be ignored.
	 * @param array $elements
	 * @return integer result of all elements
	 */
	private function operatorMultiply($elements){
		$result = 1;
		foreach($elements as $value){
			$result *= $value;
		}
		return $result;
	}
	
	/**
	 * Calculates mean of all given elements. If one element can't resolved, it will be ignored.
	 * @param array $elements
	 * @return integer result of all elements
	 */
	private function operatorMean($elements){
		$result = 0;
		foreach($elements as $value){
			$result += $value;
		}
		return $result/count($elements);
	}
	
	/**
	 * Calculates median of all given elements. If one element can't resolved, it will be ignored.
	 * @param array $elements
	 * @return integer result of all elements
	 */
	private function operatorMedian($elements){
		$result = 0;
		//sort arry ascending
		sort($elements);
		//get median
		$len = count($elements);
		if(($len%2)==0){
			//even
			$result = 0.5*($elements[$len/2-1] + $elements[$len/2]);
		}else{
			//odd
			$result = $elements[$len/2];
		}
		return $result;
	}
}