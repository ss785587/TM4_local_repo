/**
 * Client TestEngine - Testmaker 4
 * Requires JQuery
 */

/** HTTP AJAX STATUS Constants*/
	const HTTP_STATUS_OK = 200;
	const HTTP_STATUS_RENDERING_OK = 201; 
	const HTTP_STATUS_REDIRECT = 308;
	
/** html object ids */
	var TE_NAV_NEXT_ID = "#TE_nav_next_bt";
	var TE_NAV_BACK_ID = "#TE_nav_back_bt";
	
/** PAGE DIRECTIONS Constants */
	var TE_PAGE_FORWARD = 1;
	var TE_PAGE_BACKWARD = 0;
	var TE_PAGE_REFRESH = 2;
	var TE_PAGE_ABORT = 3;

/** exclude result values */
	var EXCLUDE_RESULT_TYPES = new Array("html","css");
	
/** global variables */
	var testRunData;

/**
 * TestEngine navigation function.
 *  Requestes next page, if all elements with an mandatory attribute are finished by the user.
 */
function TE_nav_btNext(){
   //TODO: get next page, if all mandatory elements are solved
	if(testRunData != null && typeof(testRunData)!="undefined"){
		if(allMandatoriesFinished(testRunData.getOriginalData().elements)){
			var results = saveAllResults();
			$("#TE_content").empty();
			ajaxDataCall(HTTP_STATUS_OK,TE_PAGE_FORWARD,results);
		}else{
			//render note
			renderNoteOpenMandatories();
		}
	}
   };

/**
 * TestEngine navigation function.
 * Requests previous page.
 */
function TE_nav_btBack(){
	//TODO: gelt last page
};

/**
 * ENTRY POINT
 * Requests first page of the testRun
 */
function initData(){
	//ajax call to server to get page
	ajaxDataCall(HTTP_STATUS_OK,TE_PAGE_REFRESH, "");
};


/**
 * AJAX call to send timelog, userinput and page direction to server.
 * Response will be json data for next page or redirection.
 * @param flag
 * @param pageDirection
 * @param userInput
 */
function ajaxDataCall(flag, pageDirection, userInput){
    jQuery.ajax({
        url: 'index.php?r=testengine/index',
        type: "POST",
        data: {status: flag, direction:pageDirection,  ajaxData: JSON.stringify(userInput)},  
        error: function(xhr,tStatus,e){
            if(!xhr){
            	 $('#error_msg').html(tStatus);
                 $('#error_msg').append(e.message);
            }else{
                $('#error_msg').html(e);
                $('#error_msg').append(xhr.responseText);
            }
            },
        success: function(resp){
        	//check status for redirection
        	if("status" in resp && resp["status"] == HTTP_STATUS_REDIRECT){
        		if("data" in resp){
        			var url = resp["data"];
        			window.location = url;
        		}
        	}
        	//else if status OK start client testengine
        	else if("status" in resp && resp["status"] == HTTP_STATUS_OK){
        		if("data" in resp){
        			initTestEngine(resp["data"]);
        		}
        	}
          }
        });
    };
    
    /**
     * AJAX call to send flag to server.
     * @param flag
     */
    function ajaxSendFlag(flag){
        jQuery.ajax({
            url: 'index.php?r=testengine/index',
            type: "POST",
            data: {status: flag},  
            error: function(xhr,tStatus,e){
                if(!xhr){
                   //TODO: error
                }else{
                    $('#error_msg').html(e);
                    $('#error_msg').append(xhr.responseText);
                }
                },
            success: function(resp){
            	//check status
            	if("status" in resp && resp["status"] == HTTP_STATUS_OK){
            		//TODO: add answer to timelog
            		
            	}
              }
            });
        };
    
    /**
     * Initialisation after received page from server.
     * @param jsonData 
     */
 function initTestEngine(jsonData){
    	//parse JSON
  	  var testRunObj = JSON.parse(jsonData);
  	  //create Delegator
  	  this.testRunData = new TestRunData(testRunObj);
  	  
  	  //set back/next button
  	  if("navigation" in testRunObj){
  		$(TE_NAV_NEXT_ID).prop('disabled',!testRunObj.navigation.forward);
  		$(TE_NAV_BACK_ID).prop('disabled',!testRunObj.navigation.backward);
  	  }
  	  
  	  //create html elements/items
  	  if("elements" in testRunObj && "templates" in testRunObj){
  		if("variables" in testRunObj){
  			TE_update(testRunData);
  		}
  		
  		//call render action 
  		TE_render(testRunData);
  		addEventHandler(testRunData);
  		
  		//send OK to server
  		ajaxSendFlag(HTTP_STATUS_RENDERING_OK);
  	  }
  };
    
  /**
   * Updates all variables. 
   * @param testRunData
   */
    function TE_update(testRunData){
    	updateVariables(testRunData.getOriginalData().variables,testRunData);
    };

    /**
     * Renders all elements of the current page.
     * @param testRunData
     */
    function TE_render(testRunData){
    	//deep copy of elements
    	var copyOfElements = jQuery.extend(true,[],testRunData.getOriginalData().elements);
  		renderElements(copyOfElements,testRunData);
    };
    
    /**
     * Saves user input to item object.
     * @param itemName 
     * @param value
     */
 function saveUserInput(itemName, value){
	 //save result in item object
	 var item = testRunData.getPropValue("elements",itemName);
	 if(item != null && typeof(item)!="undefined"){
		 item.value = value;
	 }
 };
 
 /**
  * Collects all results of the current page and saves them into an array.
  * @returns {Array}
  */
 function saveAllResults(){
	//save results in another structure for sending to server
	 var results = new Array();
	 var elements = testRunData.getOriginalData().elements;
	 //iterate over all page elements
	 for(var i=0; i<elements.length; i++){
		 var element = elements[i];
		 if(jQuery.inArray(element.type, EXCLUDE_RESULT_TYPES)>=0){
			 continue;
		 }
		 var resultObj = new Object();
		 resultObj.element_id =  element.id;
		 resultObj.value = element.value;
		 resultObj.type = element.type;
		 if($("#"+element.name).length){
				//element exists
				if($("#"+element.name).is(":visible")==false){
					resultObj.display = false;
				}else{
					resultObj.display = true;
				}
		 }
		 results.push(resultObj);
	 }
	 return results;
 }
 
 /**
  * Checks if all elements with mandatories have a value
  */
 function allMandatoriesFinished(elements){
	for(var i=0; i<elements.length; i++){
		var element = elements[i];
		if("mandatory" in element && (element.mandatory==true || element.mandatory=="true")){
			if(element.value == null || element.value =="null" || typeof(element.value)=="undefined"){
				return false;
			}
		}
	}
	return true;
 };
    
/**
 * Adds event handler to current page.
 */
function addEventHandler(){
	$("#TE_content input[type='radio']").click(function(){
		var itemName = $(this).attr('name');
		var value = $(this).attr('value');
		//save user input in item object
		saveUserInput(itemName, value);
		//update and render again
        TE_update(testRunData);
        TE_render(testRunData);
	});
       
};
