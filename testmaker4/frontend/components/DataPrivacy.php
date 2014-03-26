<?php
/**
 * Handles the data privacy statements.
 */
class DataPrivacy extends CComponent
{
	/**
	 * Returns the lastest data privacy statment.
	 * Default language is english.
	 * @return long description of data privacy statement
	 */
	public function getLastestDataPrivacyDescription($lang){
		$privacyStatementObj = $this->getLatestDataPrivacyStatementObject($lang);
		if(isset($privacyStatementObj)){
			return $privacyStatementObj->longDescription;
		}else{
			$defaultLang = System::getValue(System::FRONTEND_LANGUAGE_DEFAULT);
			if($lang != $defaultLang){
				//fall back to default language
				return $this->getLastestDataPrivacyDescription($defaultLang);
			}
		}
	}
	
	public function getDataPrivacyStatementObject($version, $language){
		//TODO: sql statement to get given version and language
	}
	
	public function getLatestDataPrivacyStatementObject($language){
		$criteria=new CDbCriteria;
		$criteria->select='max(id) AS maxColumn';
		$privacyDef = DataPrivacyDefinition::model()->find($criteria);
		$ver = $privacyDef['maxColumn'];
	
		//read max id in database table "DataPrivacyStatement" with given language
		$privacyStatementObj = DataPrivacyStatement::model()->find('version=:version AND language=:language',array(':version'=>$ver, ':language'=>$language));
		return $privacyStatementObj;
	}
}