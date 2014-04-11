/**
 * Creates and renders elements of the testrun.
 */

/** constants*/
var TEMPLATE_VAR_PREFIX = "{{";
var TEMPLATE_VAR_SUFFIX = "}}";

/**
 * ENTRY POINT
 * Iterates over all page elements, resolves variables, create templates and add elements to DOM.
 */
function renderElements(elements, testRunData){
	if(elements==null || testRunData== null || testRunData.getOriginalData().templates == null){
		return;
	}
	
	for(var i=0; i<elements.length; i++){
		element = elements[i];
		//resolve vars and create html from template
		var html = createHtmlElement(element,testRunData);
		//add to DOM if not already exists and shown
		if($("#"+element.name).length){
			//element exists
			if($("#"+element.name).is(":visible")==false){
				//not visible
				//override with new template
				$("#"+element.name).html(html);
			}
		}else{
			$("#TE_content").append(html);
		}
		
		//set visiblity
		if(element.display==true || element.display=="true"){
			$("#"+element.name).show();
		}else{
			$("#"+element.name).hide();
		}
		
	}
};

/**
 * Creates and returnes HTML-code for givien element.
 * If the elements has any variables as values, they will be replaced if possible.
 * 
 * @param element - page element to be rendered
 * @param testRunData data of testrun to resolve variables
 * @returns {String} html-string
 */
function createHtmlElement(element,testRunData){
	//replace variables with values
	element = replaceVarsForRendering(element, testRunData);
	
	//create html from template
	var templateObj = testRunData.getPropValue("templates",element.type);
	var html = createHtmlFromTemplate(element,templateObj,testRunData);
	//add identification tag
	return "<div id='"+element.name+"'>" + html + "</div>"
};


/**
 * Recursive function to fill the template of the element with values.
 * Addition templates has to have the same name as the related property name.
 */
function createHtmlFromTemplate(element, templateObj, testRunData){
	
	if(typeof(templateObj)!="undefined" && templateObj!=null){
		//prepare while loop
		var templateJson = templateObj.value;
		var curPos = 0;
		var startIndex = startIndex = templateJson.indexOf(TEMPLATE_VAR_PREFIX,curPos);
		//start loop, search while there are variables in the string
		while(startIndex>0){
			var endIndex = templateJson.indexOf(TEMPLATE_VAR_SUFFIX,startIndex);
			var varName = templateJson.slice(startIndex+TEMPLATE_VAR_PREFIX.length, endIndex);
			//get value of variable
			var resolved = testRunData.resolveValue(varName, element);
			//if resolved is array or object, check for additional template
			//only called if resolved value is an array or object
			if((resolved!=null && typeof(resolved)!="string") && (typeof(resolved)=="object" || Array.isArray(resolved))){
				resolved =checkForAdditionTemplate(resolved, varName, element, templateObj, testRunData );
			}
			//if variable couldnt resolved, replace it with whitespace
			resolved = (resolved==null)?'':resolved;
			//repalce resolved variable to template
			templateJson = templateJson.replace(TEMPLATE_VAR_PREFIX+varName+TEMPLATE_VAR_SUFFIX, resolved);
			//check loop conditions
			curPos = startIndex+resolved.length;
			startIndex = templateJson.indexOf(TEMPLATE_VAR_PREFIX,curPos);
		}
	}
	//return resolved template
	return templateJson;	
};

/**
 * Helper function for recursion. Checks if an addition template is availible.
 * If yes, function createHtmlFromTemplate() is called again.
 */
function checkForAdditionTemplate(resolved, varName, element, templateObj, testRunData ){
	if((resolved!=null && typeof(resolved)!="string") && (typeof(resolved)=="object" || Array.isArray(resolved))){
		//check, if another template exists in addition
		if("addition" in templateObj){
			var additionTmp = testRunData.getValueinArrbyName(templateObj.addition,varName);
			if(additionTmp!=null && typeof(additionTmp)!="undefined"){
				var addiTemplStr = "";
				//difference between array and object
				if(Array.isArray(resolved)){
					//iterate over array and call recursive function with addition template
					for(var j=0; j<resolved.length; j++){
						//change element structure. Replace array with array object
						element[varName] = resolved[j];
						//call  recursion and save all results in one template string
						addiTemplStr += createHtmlFromTemplate(element, additionTmp, testRunData);
					}
					//restore array to data
					element[varName] = resolved;
				}else{//object
					//start recursion with addition template
					addiTemplStr = createHtmlFromTemplate(element,additionTmp, testRunData);
				}
				//return resolved template
				return addiTemplStr;
			}
		}
	}
};

/**
 * Replaces variables in element with variables value, if exists.
 * Property values "Id", "name" and "type" are skiped.
 */
function replaceVarsForRendering(element, testRunData){
	//run over all properties of the element-object except id, name, type
	for(prop in element){
		if(prop=="id" || prop=="name" || prop=="type"){
			continue;
		}
		//check properties recusive
		if(typeof(element[prop])!="string" && typeof(element[prop]=="object")){
			element[prop] = replaceVarsForRendering(element[prop], testRunData);
		}else{
			var tmpVal = testRunData.checkAndResolveValue(element[prop]);
			if(tmpVal != null){
				element[prop]=tmpVal;
			}
		}
	}
	return element;
};

/**
 * Renders note, if not all items are finished.
 */
function renderNoteOpenMandatories(){
	alert("At least one mandatory true. Please finish itemes.");
};
