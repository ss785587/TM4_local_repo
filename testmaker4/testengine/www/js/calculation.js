/**
 * Calculation for variables.
 */

function Calculation(testRunData) {
	
	/** member variables */
	this.testRunData = testRunData;
	
	/** operator constants */

	var EQUAL_OPERATOR = '$eq';
	var SUM_OPERATOR = '$sum';
	var MULTIPLY_OPERATOR = '$multiply';
	var MEAN_OPERATOR = '$mean';
	var MEDIAN_OPERATOR = '$median';
	
	
	/**
	 * Resolves and executes calculation.
	 * 
	 * @param array $paraArray
	 * @throws CException thrown, if given array is null or empty
	 * @return result, after calculaton
	 */
	this.resolveCalculation = function(paraArray){
		if((typeof(paraArray)!="undefined") && paraArray.length!=0){
			
			paraArray = testRunData.checkAndResolveValues(paraArray);
		switch (paraArray[0]) {
			case EQUAL_OPERATOR:
				return operatorEqual(paraArray.slice(1));
			case SUM_OPERATOR:
				return operatorSum(paraArray.slice(1));
			break;
			case MULTIPLY_OPERATOR:
				return operatorMultiply(paraArray.slice(1));
				break;
			case MEAN_OPERATOR:
				return operatorMean(paraArray.slice(1));
				break;
			case MEDIAN_OPERATOR:
				return operatorMedian(paraArray.slice(1));
				break;
			default:
				return 'NOT DEFINED';
			break;
		}
		}
	}
	
	
	
	//========================================== Operators ==================================================
	
	/**
	 * Checks, if all given elements are equal. 
	 * @param array $elements
	 * @return boolean true, if all elements are equl
	 */
	function operatorEqual(elements){
		var first = elements[0];
		for(var i=0; i<elements.length; i++){
			if(first != elements[i]){
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
	function operatorSum(elements){
		var result = 0;
		for(var i=0; i<elements.length; i++){
			result += parseInt(elements[i]);
		}
		return result;
	}
	
	/**
	 * Multiplies all given elements. If one element can't resolved, it will be ignored.
	 * @param array $elements
	 * @return integer result of all elements
	 */
	function operatorMultiply(elements){
		var result = 1;
		for(var i=0; i<elements.length; i++){
			result *= parseInt(elements[i]);
		}
		return result;
	}
	
	/**
	 * Calculates mean of all given elements. If one element can't resolved, it will be ignored.
	 * @param array $elements
	 * @return integer result of all elements
	 */
	function operatorMean(elements){
		var result = 0;
		for(var i=0; i<elements.length; i++){
			result += parseInt(elements[i]);
		}
		return result/elements.length;
	}
	
	/**
	 * Calculates median of all given elements. If one element can't resolved, it will be ignored.
	 * @param array $elements
	 * @return integer result of all elements
	 */
	function operatorMedian(elements){
		var result = 0;
		//sort arry ascending
		elements.sort();
		//get median
		var len = elements.length;
		if((len%2)==0){
			//even
			result = 0.5*(elements[len/2-1] + elements[len/2]);
		}else{
			//odd
			result = elements[len/2];
		}
		return result;
	}
};