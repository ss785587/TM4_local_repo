<?php

class TestRunParser
{
	public static function parseJson($jsonString){
		//parse Document
		$jsonObject = self::json_decode($jsonString);
		
		//create and validate TestRunObject
		return new TestRunObject($jsonObject);
	}
	
	/**
	 * Returns json string, created with given object.
	 * @param object $obj object that should be encoded
	 * @return json string
	 */
	public static function encodeToJson($obj){
		if($obj instanceof TestRunObject){
			$obj->presave();
		}
		//encode Object
		return self::json_encode($obj);
	}

	
	
	/**
	 * Helper method to use json_encode with json_last_error_msg.
	 * If you are using php5.5, this method is deprecated.
	 * @param object $obj object to encode
	 * @throws CException thronw, if any error occurs while encoding
	 * @return parsed json string
	 */
	public static function json_encode($obj){
		$decoded = json_encode($obj);
	
		//available in PHP5.5
		if (!function_exists('json_last_error_msg')) {
			function json_last_error_msg() {
				static $errors = array(
						JSON_ERROR_NONE             => null,
						JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
						JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
						JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
						JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
						JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
				);
				$error = json_last_error();
				return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
			}
		}
		$errorMsg = json_last_error_msg();
		if(isset($errorMsg)){
			throw new CException($errorMsg);
		}
		return $decoded;
	}
	
	/**
	 * Helper method to use json_decode with json_last_error_msg.
	 * If you are using php5.5, this method is deprecated.
	 * @param string $doc json string
	 * @throws CException thronw, if any error occurs while parsing
	 * @return parsed json object
	 */
	public static function json_decode($doc){
		$decoded = json_decode($doc);
	
		//available in PHP5.5
		if (!function_exists('json_last_error_msg')) {
			function json_last_error_msg() {
				static $errors = array(
						JSON_ERROR_NONE             => null,
						JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
						JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
						JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
						JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
						JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
				);
				$error = json_last_error();
				return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
			}
		}
		$errorMsg = json_last_error_msg();
		if(isset($errorMsg)){
			throw new CException($errorMsg);
		}
		return $decoded;
	}
	
}