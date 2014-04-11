/**
 * Model of testrun. Use delegator design pattern.
 */

function TestRunData(testRunObj){
	/** orginal object */
	this.testRunObj = testRunObj;
	
	/**member variables*/
	var SEPERATOR = '.';
	var VARIABLE_FLAG = "#";
	var self = this;
	
	
	/**
	 * Checks the given value, if this is variable which has to be resolved. 
	 * If yes, it will be resolved and returned.
	 * @param array $value array of values
	 */
	this.checkAndResolveValue = function(value){
		if(typeof value == "string"){
			//dont work with sentence
			if(value!=null && value.indexOf(SEPERATOR)>0 && value.indexOf(" ")<0){
				//resolve links to values like items.item1.value
				value = self.resolveValue(value,testRunObj);
			}
			//indexOf returns 0, if a match is found at the beginning, else false
			if(value!=null && value.indexOf(VARIABLE_FLAG)>=0){
				//resolve variable like #var1
				value = resolveVariable(value);
			}
		}
			//elements can be undefined				
		return value;
	};
	
	/**
	 * Resolves variable and replaces it with variable value.
	 * Variable starts in string with VARIABLE_FLAG and ends with one withespace.
	 */
	function resolveVariable(value){
		//extract variable name
		var index = value.indexOf(VARIABLE_FLAG);
		var endIndex = value.indexOf(" ",index);
		endIndex = (endIndex<0)?value.length : endIndex;
		var vari = value.slice(index+1,endIndex);
		
		var tmpVal = self.getPropValue('variables',vari);
		if(tmpVal!=null){
			vari = tmpVal.value;
		}
		
		return value.slice(0,index)+vari+value.slice(endIndex);
	};
	
	/**
	 * Checks if in the given array are variables which have to be resolved. 
	 * If yes, they will be resolved and saved in the given array. Elements in 
	 * the returned array can be undefined.
	 * @param array $elements array of values
	 */
	this.checkAndResolveValues = function(elements){
		//resolve elements, if necessary
		for(var i=0; i<elements.length; i++){
			var value = elements[i];
			value = this.checkAndResolveValue(value);
			//elements can be undefined
			if(value==null){
				//delete from array
				if(i+1<elements.length){
					elements = elements.slice(0,i).concat(elements.slice(i+1));
				}else{
					elements = elements.slice(0,i);
				}
			}else{
				//if element has a value
				elements[i] = value;
			}
		}
		return elements;
	};
	
	/**
	 * Returns value of the given property if it exists. Otherwise method returns null. 
	 * @param string $propertyName name of property
	 * @param string $valueName name of the value
	 * @return Object|NULL
	 */
	this.getPropValue = function(propertyName, valueName){
		if(typeof(testRunObj[propertyName])!="undefined"){
			return this.getValueinArrbyName(testRunObj[propertyName], valueName);
		}
		return null;
	};
	
	/**
	 * Returns array object by the given name.
	 * Helper function. Wrong place in this object.
	 */
	this.getValueinArrbyName = function(array, valueName){
		if(array==null || valueName==null){
			return null;
		}
		for(var i=0; i<array.length; i++){
			var valueObj = array[i];
			if(valueName === valueObj.name){
				return valueObj;
			}
		}
	};
	
	/**
	 * Resolves a value within the given testrun. If it's impossible to resolve, null will be returned.
	 * @param string $value that should be resolved
	 * @return Object if resolve was successfully, object or array will be returned. Otherwise null will be returned
	 */
	 this.resolveValue = function(value, tmpObj){
		exploded = value.split(SEPERATOR);
		for(var i=0; i<exploded.length; i++ ) {
			var key = exploded[i];
			//check if $key exists as property or if it's existing as array value
			if((typeof(tmpObj)=='object')){
				//array is also an object
				if(Array.isArray(tmpObj)){
					tmpArrVal = getNamedValueInArray(tmpObj, key);
					if(tmpArrVal!= null && typeof(tmpArrVal)!="undefined"){
						tmpObj = tmpArrVal;
						continue;
					}
				}
				//change structure at client side. "Items" and "contentelements" are together one property "elements"
				if(key == "items" || key == "contentelements"){
					key = "elements";
				}
				if(key in tmpObj){
					tmpObj = tmpObj[key];
				}else{
					return null;
				}
				 
			}else{
				return null;
			}
		}
		return tmpObj;
	};
	
	/**
	 * Iterates over array and compare given name with name values of array. 
	 * @param array $arr 
	 * @param string $valueName
	 * @return object|NULL
	 */
	 function getNamedValueInArray(arr, valueName){
		if(Array.isArray(arr) && typeof(valueName)!="undefined"){
			for (var i=0; i<arr.length ; i++){
				var value = arr[i];
				if(valueName == value.name){
					return value;
				}
			}
		}
		return null;
	};
	
	/**
	 * GETTER-functions
	 */
	this.getSeperator = function(){
		return SEPERATOR;
	}
	
	this.getOriginalData = function(){
		return testRunObj;
	};
	
	
};