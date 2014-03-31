<?php

class ValidatorTestRun{

	/**
	 * Validates a variable. Returns true, if a variable has a name and a value.
	 *
	 * @param object $var given variable to validate
	 * @throws CException thrown, if variable has not a name or a value as property
	 * @return boolean true, if given variable has no missing properties
	 */
	public static function validateVariable($var){
		if(!property_exists($var,'name') || !property_exists($var, 'value')){
			throw new CException('Invalid variable: '.print_r($var, true));
		}
		return true;
	}

	/**
	 * Validates a scale. Returns true, if a scale has following properties: name, value and compare.
	 *
	 * @param object $scale given object to validate
	 * @throws CException thrown, if object has not the required properties
	 * @return boolean true, if given variable has all required properties
	 */
	public static function validateScale($scale){
		if(!property_exists($scale,'name') || !property_exists($scale, 'value') || !property_exists($scale, 'compare')){
			throw new CException('Invalid scale: '.print_r($scale, true));
		}
		return true;
	}

	/**
	 * Validates an item. Returns true, if item has following properties: name, value, type, content, answers, display, mandatory.
	 *
	 * @param object $item given object to validate
	 * @throws CException thrown, if object has not the required properties
	 * @return boolean true, if given variable has all required properties
	 */
	public static function validateItem($item){
		if(!property_exists($item,'name') || !property_exists($item, 'value') || !property_exists($item, 'type') ||
		!property_exists($item, 'content') || !property_exists($item, 'answers') || !property_exists($item, 'display') ||
		!property_exists($item, 'mandatory')){
			throw new CException('Invalid item: '.print_r($item, true));
		}
		return true;
	}

	/**
	 * Validates a contentelement. Returns true, if contentelement has following properties: name, content, type, display.
	 *
	 * @param object $element given object to validate
	 * @throws CException thrown, if object has not the required properties
	 * @return boolean true, if given variable has all required properties
	 */
	public static function validateContentElement($element){
		if(!property_exists($element,'name') || !property_exists($element, 'content') || !property_exists($element, 'type') ||
		!property_exists($element, 'display')){
			throw new CException('Invalid contentelement: '.print_r($element, true));
		}
		return true;
	}

	/**
	 * Validates a page. Returns true, if page has following properties: name, elements, subtest, display, navigation.
	 *
	 * @param object $page given object to validate
	 * @throws CException thrown, if object has not the required properties
	 * @return boolean true, if given variable has all required properties
	 */
	public static function validatePage($page){
		if(!property_exists($page,'name') || !property_exists($page, 'elements') || !property_exists($page, 'subtest') ||
		!property_exists($page, 'display') || !property_exists($page, 'navigation')){
			throw new CException('Invalid page: '.print_r($page, true));
		}
		return true;
	}

	/**
	 * Validates a subtest. Returns true, if subtest has following properties: name, instrument, nice_name, pages.
	 *
	 * @param object $sub given object to validate
	 * @throws CException thrown, if object has not the required properties
	 * @return boolean true, if given variable has all required properties
	 */
	public static function validateSubtest($sub){
		if(!property_exists($sub,'name') || !property_exists($sub, 'instrument') || !property_exists($sub, 'nice_name') ||
		!property_exists($sub, 'pages')){
			throw new CException('Invalid subtest: '.print_r($sub, true));
		}
		return true;
	}

	/**
	 * Validates a trigger. Returns true, if trigger has following properties: name, condition, action, params.
	 *
	 * @param object $trigger given object to validate
	 * @throws CException thrown, if object has not the required properties
	 * @return boolean true, if given variable has all required properties
	 */
	public static function validateTrigger($trigger){
		if(!property_exists($trigger,'name') || !property_exists($trigger, 'condition') || !property_exists($trigger, 'action') ||
		!property_exists($trigger, 'params')){
			throw new CException('Invalid subtest: '.print_r($trigger, true));
		}
		return true;
	}
}