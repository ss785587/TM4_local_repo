<?php

class TestRunObject{
	
	/** member variables */
	public $variables = null;
	public $scales = null;
	public $items = null;
	public $contentelements = null;
	public $pages = null;
	public $subtests = null;
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
		if(property_exists($jsonObj,'triggers')){
			if(ValidatorTestRun::validateTriggers($jsonObj->triggers)){
				$this->triggers = $jsonObj->triggers;
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