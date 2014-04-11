/**
 * Updates and calculates variables of the testrun.
 */

/**
 * Updates all variables, which have a calculation.
 * @param unknown $testRunObj parsed JSON-Object
 */
function updateVariables(variables,testRunData){
	if (typeof(variables)!="undefined"){
		var calcObj = new Calculation(testRunData);
		for(var i=0; i<variables.length; i++){
			var vari = variables[i];
			if(vari.hasOwnProperty("calculation")){
				//only copy of calculations/ calculations call by value
		    	var calculatoins = variables[i].calculation.slice(0);
				variables[i].value = calcObj.resolveCalculation(calculatoins);
			}
		}
	}
};

