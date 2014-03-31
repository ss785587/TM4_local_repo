<?php

class TestRunObject{
	
	/** member variables */
	public $variables = array();
	public $scales = array();
	public $items = array();
	public $contentelements = array();
	public $pages = array();
	public $subtests = array();
	public $timelog = array();
	public $user = array();
	public $triggers = array();
	
	/** constructor */
	public function __construct($jsonObj){
		if(!isset($jsonObj)){
			throw new CException('Error: Empty JSON-File');
		}
		
		//validate and save variables
		if(property_exists($jsonObj,'variables')){
			foreach ($jsonObj->variables as $var) {
				if(ValidatorTestRun::validateVariable($var)){
					$this->variables[$var->name] = $var;
				}
			} 
		}
		//validate and save scales
		if(property_exists($jsonObj,'scales')){
			foreach ($jsonObj->scales as $scale) {
				if(ValidatorTestRun::validateScale($scale)){
					$this->scales[$scale->name] = $scale;
				}
			} 
		}
		//validate and save items
		if(property_exists($jsonObj,'items')){
			foreach ($jsonObj->items as $item) {
				if(ValidatorTestRun::validateItem($item)){
					$this->items[$item->name] = $item;
				}
			}
		}
		//validate and save contentelements
		if(property_exists($jsonObj,'contentelements')){
			foreach ($jsonObj->contentelements as $element) {
				if(ValidatorTestRun::validateContentElement($element)){
					$this->contentelements[$element->name] = $element;
				}
			}
		}
		//validate and save pages
		if(property_exists($jsonObj,'pages')){
			foreach ($jsonObj->pages as $page) {
				if(ValidatorTestRun::validatePage($page)){
					$this->pages[$page->name] = $page;
				}
			}
		}
		//validate and save subtests
		if(property_exists($jsonObj,'subtests')){
			foreach ($jsonObj->subtests as $sub) {
				if(ValidatorTestRun::validateSubtest($sub)){
					$this->subtests[$sub->name] = $sub;
				}
			}
		}
		//validate and save triggers
		if(property_exists($jsonObj,'triggers')){
			foreach ($jsonObj->triggers as $trigger) {
				if(ValidatorTestRun::validateTrigger($trigger)){
					$this->triggers[$trigger->name] = $trigger;
				}
			}
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
	 * Changes the structure of the properties in this object for Json encoding.
	 * Maps will be transformed to simple arrays.
	 */
	public function presave(){
		//change object structure for json save
		if(isset($this->variables)){
			$this->variables = $this->changeArrayStructure($this->variables);
		}
		if(isset($this->scales)){
			$this->scales = $this->changeArrayStructure($this->scales);
		}
		if(isset($this->items)){
			$this->items = $this->changeArrayStructure($this->items);
		}
		if(isset($this->contentelements)){
			$this->contentelements = $this->changeArrayStructure($this->contentelements);
		}
		if(isset($this->pages)){
			$this->pages = $this->changeArrayStructure($this->pages);
		}
		if(isset($this->subtests)){
			$this->subtests = $this->changeArrayStructure($this->subtests);
		}
		if(isset($this->triggers)){
			$this->triggers = $this->changeArrayStructure($this->triggers);
		}
	}
	
	/**
	 * Changes map structure to simple array structe. 
	 * @param array $map given map
	 * @return simple array
	 */
	private function changeArrayStructure($map){
		$returnArr = array();
		foreach($map as $key=>$value){
			array_push($returnArr, $map[$key]);
		}
		return $returnArr;
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