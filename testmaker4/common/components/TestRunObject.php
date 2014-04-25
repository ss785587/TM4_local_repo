<?php

class TestRunObject{
	
	/** member variables */
	public $variables = null;
	public $scales = null;
	public $items = null;
	public $contentelements = null;
	public $pages = null;
	public $subtests = null;
	public $templates = null;
	public $timelog = null;
	public $user = null;
	public $triggers = null;
	
	/** constructor */
	public function __construct($jsonObj){
		if(!isset($jsonObj)){
			throw new CException('Error: Empty JSON-File');
		}
		
		//validate and save variables
		if(property_exists($jsonObj,'variables')){
			if(ValidatorTestRun::validateVariables($jsonObj->variables)){
				$this->variables = $jsonObj->variables;
			}
		}
		//validate and save scales
		if(property_exists($jsonObj,'scales')){
			if(ValidatorTestRun::validateScales($jsonObj->scales)){
				$this->scales = $jsonObj->scales;
			}
		}
		//validate and save items
		if(property_exists($jsonObj,'items')){
			if(ValidatorTestRun::validateItems($jsonObj->items)){
				$this->items = $jsonObj->items;
			}
		}
		//validate and save contentelements
		if(property_exists($jsonObj,'contentelements')){
			if(ValidatorTestRun::validateContentelements($jsonObj->contentelements)){
				$this->contentelements = $jsonObj->contentelements;
			}			
		}
		//validate and save pages
		if(property_exists($jsonObj,'pages')){
			if(ValidatorTestRun::validatePages($jsonObj->pages)){
				$this->pages = $jsonObj->pages;
			}			
		}
		//validate and save subtests
		if(property_exists($jsonObj,'subtests')){
			if(ValidatorTestRun::validateSubtests($jsonObj->subtests)){
				$this->subtests = $jsonObj->subtests;
			}			
		}else{
			throw new CException('Invalid JSON: subtest missing');
		}
		//validate and save triggers
		if(property_exists($jsonObj,'triggers') && isset($jsonObj->triggers)){
			if(ValidatorTestRun::validateTriggers($jsonObj->triggers)){
				$this->triggers = $jsonObj->triggers;
			}			
		}
		//validate and save templates
		if(property_exists($jsonObj,'templates')){
			if(ValidatorTestRun::validateTemplates($jsonObj->templates)){
				$this->templates = $jsonObj->templates;
			}
		}
		//validate and save triggers
		if(property_exists($jsonObj,'user')){
			$this->user = $jsonObj->user;
		}
	}

	
	/**
	 * Updates all variables, which have a calculation.
	 * @param unknown $testRunObj parsed JSON-Object
	 */
	public function updateVariables(){
		if($this->hasVariables()){
			$calcObj = new Calculation($this);
			foreach($this->variables as $var){
				if(property_exists($var,'calculation')){
					//resolve calculation
					$var->value = $calcObj->resolveCalculation($var->calculation);
				}
			}
		}
	}
	
	/**
	 * Returns value of the given property if it exists. Otherwise method returns null. 
	 * @param string $propertyName name of property
	 * @param string $valueName name of the value
	 * @return Object|NULL
	 */
	public function getPropValue($propertyName, $valueName){
		if(isset($this->$propertyName)){
			foreach ($this->$propertyName as $value){
				if($valueName === $value->name){
					return $value;
				}
			} 
		}
		return null;
	}
	
	/**
	 * Returns value of the given property if it exists by the given id. Otherwise method returns null.
	 * @param string $propertyName name of property
	 * @param string $id id of the value
	 * @return Object|NULL
	 */
	public function getPropValueById($propertyName, $id){
		if(isset($this->$propertyName)){
			foreach ($this->$propertyName as $value){
				if(isset($value->id)){
					if($id === $value->id){
						return $value;
					}	
				}
			}
		}
		return null;
	}
	
	/**
	 * Searches in the whole object for given id. Returnes value if found, else null. 
	 * @param integer $id id of value
	 * @return $value|null
	 */
	public function getValueById($id){
		//iterate over all properties
		foreach($this as $prop=>$propVal){
			if(is_array($propVal)){
				//$propVal is array
				$tmpVal =  $this->getPropValueById($prop,$id);
				if(isset($tmpVal)){
					return $tmpVal;
				}
			}else{
				//$propVal is Object or variable
				if(property_exists($propVal, "id")){
					if($id == $propVal->id){
						return $propVal;
					}
				}
			}
		}
		return null;
	}
	
	/**
	 * Searches given name in items and contentelements. If name does not exist, null will be returned.
	 * @param string $name unique contentelement or item name
	 * @return contentelement | item | null
	 */
	public function getElement($name){
		if(isset($name) && $name!=""){
			$element = $this->getPropValue('items', $name);
			if(isset($element)){
				return $element;
			}
			$element = $this->getPropValue('contentelements', $name);
			if(isset($element)){
				return $element;
			}
		}
		return null;
	}
	
	/**
	 * Overrides variable, if variable exists. Otherwise a new new variable will be created.
	 * Calculation is optional. Returns true, if the new variable could be saved.
	 * @param unknown $varName name of variable
	 * @param unknown $varValue value of variable
	 * @param string $calc optional calculation
	 * @return boolean true if successful
	 */
	public function setVariable($varName, $varValue, $calc=null){
		if(!isset($this->variables)){
			$this->variables = array();
		}
		if(!isset($varName) || !isset($varValue)){
			return false;
		}
		
		//variable already exists?
		$varObj = $this->getPropValue("variables",$varName);
		if(!isset($varObj)){
			//create variable object
			$varObj = new stdClass;
			$varObj->name = $varName;
		}
		//add value and calculation
		$varObj->value = $varValue;
		if(isset($calc)){
			$varObj->calculation = $calc;
		}
		//save vairable document as object to array
		array_push($this->variables, $varObj);
		return true;
	}
	
	
	/**
	 * Helper method, which checks if the testrun has variables.
	 * @return boolean true, if variables exists.
	 */
	public function hasVariables(){
		return (isset($this->variables) && count($this->variables)!=0);
	}
	
	/**
	 * Getter for all properties.
	 * 
	 * @param string $name
	 * @return property or null
	 */
	public function __get($name){
		if(property_exists($this, $name)){
			return $this->$name;
		}
		return null;
	}
}