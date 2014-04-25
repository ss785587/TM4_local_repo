<?php

class RenderController extends Controller
{
	/** properties */
	private $testRunDbObj;
	private $testRunObj;
	
	/** variable names */
	const CUR_PAGE_VAR_NAME = "_curPage";
	const CUR_SUBTEST_VAR_NAME = "_curSubtest";
	
	/**
	 * ENTRY POINT RENDER
	 */
	public function actionIndex(){
		//get session values
		$this->testRunDbObj = Yii::app()->session['TE_testrunDbObj'];
		$this->testRunObj = Yii::app()->session['TE_jsonObj'];
		if(!isset($this->testRunDbObj) || !isset($this->testRunObj)){
			throw new CException('No TestRun in update loop');
		}

		//check which page should be rendered (forward/backwards) => should be set in TestEngine-INPUT routine
		$pageDirection = TestEngineController::TE_PAGE_REFRESH;
		if(isset(Yii::app()->session['TE_PageDirection'])){
			$pageDirection = Yii::app()->session['TE_PageDirection'];
		}
		
		//currentPagePointer and current subtestPointer will be saved in session
		//get page to render
		$pageName = $this->getRenderPageName($pageDirection, $this->testRunObj);
		$page = $this->testRunObj->getPropValue('pages', $pageName);
		
		//display page? If not same method with next page
		//TODO: $page->display kann variable enthalten
		if(!$page->display){
			Yii::app()->session['TE_PageDirection'] = TestEngineController::TE_PAGE_FORWARD;
			$this->actionIndex();
			die();
		}
		
		//create deepcopy of page
		$outputPage = TestRunParser::decode(TestRunParser::encodeToJson($page));
		//add content of elements to page, so it can be send to the client
		$outputPage = $this->mergeElementsToPage($outputPage);
		//output to AJAX call
		Yii::import('TestEngineController');
		TestEngineController::responeAjaxRequest(TestRunParser::encodeToJson($outputPage));
		Yii::app()->session['TE_step'] = TestEngineController::TE_STEP_INPUT;
		Yii::app()->session['TE_PageDirection'] = TestEngineController::TE_PAGE_REFRESH;
	}
	
	/**
	 * Copys element objects to pages-elements-name array. If an object cant be found, it will be remove at the page.
	 * @param object $page current page object
	 * @return modified page object
	 */
	private function mergeElementsToPage($page){
		foreach($page->elements as $i=>$elementName){
			$element = $this->testRunObj->getElement($elementName);
			if(!isset($element)){
				unset($page->elements[$i]);
			}else{
				$page->elements[$i] = $element;
				//add template to page
				if(!isset($page->templates)){
					$page->templates = array();
				} 
				
				$template = $this->testRunObj->getPropValue('templates', $element->type);
				if(!in_array($template, $page->templates)){
					if(isset($template)){
						array_push($page->templates, $template);
					}
				}
			}			
		}
		
		//add variables to page
		if($this->testRunObj->hasVariables()){			
			$page->variables = $this->testRunObj->variables;
		}
		
		return $page;
	}
	
	/**
	 * Gets next page for rendering process. The pageDirection specify which page will be chosen.
	 * @param integer $pageDirection previous, next or refresh page
	 * @param TestRunObject $testRunObj object to the current testrun
	 */
	private function getRenderPageName($pageDirection, $testRunObj){
		//get current subtest	
		$tmpObj = $this->testRunObj->getPropValue("variables", self::CUR_SUBTEST_VAR_NAME); 
		if(isset($tmpObj)){
			$curSubtestPointer = $tmpObj->value;
		}else{
			//check curr
			$curSubtestPointer = $this->getNextSubtestIndex(null);
		}
		//get currentpage
		$tmpObj = $this->testRunObj->getPropValue("variables", self::CUR_PAGE_VAR_NAME);
		if(isset($tmpObj)){
			$curPageArrPointer = $tmpObj->value;
		}else{
			$curPageArrPointer = 0;
		}
		
		switch ($pageDirection) {
			case TestEngineController::TE_PAGE_REFRESH:
				$subtest = $testRunObj->subtests[$curSubtestPointer];
				return $subtest->pages[$curPageArrPointer];
			
			case TestEngineController::TE_PAGE_FORWARD:
				return $this->getNextPage($curSubtestPointer,$curPageArrPointer);
				
			case TestEngineController::TE_PAGE_BACKWARD:
				return $this->getPreviousPage($curSubtestPointer,$curPageArrPointer);
				
			case TestEngineController::TE_PAGE_ABORT:
				//fall through
			default:
				Yii::app()->session['TE_step'] = TestEngineController::TE_STEP_FINISH;
				$this->redirect($this->createUrl("testEngine/index"));
				break;
		}
	}
	
	/**
	 * Returns the unique name of the next page based on the given current subtest and page.
	 * If there is no next page, the test ends
	 * @param integer $curSubtestPointer
	 * @param integer $curPageArrPointer Page index reference to array in subtest object.
	 * @return string page name
	 */
	private function getNextPage($curSubtestPointer,$curPageArrPointer){
		//gets next page to the given subtest and current page pointer
		//if page pointer doesnt find any more pages, continue with the next subtest
		$subtest = $this->testRunObj->subtests[$curSubtestPointer];
		if($curPageArrPointer < count($subtest->pages)-1){
			++$curPageArrPointer;
		}else{
			//next subtest
			$subIndex = $this->getNextSubtestIndex($curSubtestPointer);
			if($subIndex < 0 ){
				//end of test
				Yii::app()->session['TE_step'] = TestEngineController::TE_STEP_FINISH;
				$this->redirect($this->createUrl("testEngine/index"));
				die();
			}else{
				//set pointer to next subtest and first page
				$curSubtestPointer = $subIndex;
				$curPageArrPointer = 0;
			}
		}
		//save to testrun
		$this->saveSubTestAndPagePointer($curSubtestPointer,$curPageArrPointer);
		
		//get page name
		$subtest = $this->testRunObj->subtests[$curSubtestPointer];
		return $subtest->pages[$curPageArrPointer];
	}
	
	/**
	 * Saves the subtest pointer and the page pointer to the session and TestRunObj (into json format)
	 * @param integer $curSubtestPointer
	 * @param integer $curPageArrPointer
	 */
	public function saveSubTestAndPagePointer($curSubtestPointer,$curPageArrPointer){
		//save to jsonObj
		//current page
		$this->testRunObj->setVariable(self::CUR_PAGE_VAR_NAME, $curPageArrPointer);		
		//current subtest
		$this->testRunObj->setVariable(self::CUR_SUBTEST_VAR_NAME, $curSubtestPointer);
		
		//save to json and to database
		TE_Utils::saveTestRunToDb($this->testRunDbObj, $this->testRunObj);
	}
	
	/**
	 * Returns the unique name of the previous page based on the given current subtest and page.
	 * If there is no previous page, the given page index will be returned
	 * @param integer $curSubtestPointer
	 * @param integer $curPageArrPointer page index reference to array in subtest object
	 * @return string page name
	 */
	private function getPreviousPage($curSubtestPointer,$curPageArrPointer){
		//gets previous page to the given subtest and current page pointer
		//if page pointer doesnt find any more pages, continue with previous subtest
		$subtest = $this->testRunObj->subtests[$curSubtestPointer];
		if($curPageArrPointer >  0){
			--$curPageArrPointer;
		}else{
			//previous subtest
			$subIndex = $this->getNextSubtestIndex($curSubtestPointer, TestEngineController::TE_PAGE_BACKWARD);
			//set pointer to previous subtest and last page
			$subtest = $this->testRunObj->subtests[$subIndex];
			$curSubtestPointer = $subIndex;
			$curPageArrPointer = count($subtest->pages)-1;
		}
		//save to testrun
		$this->saveSubTestAndPagePointer($curSubtestPointer,$curPageArrPointer);
		//get page name
		$subtest = $this->testRunObj->subtests[$curSubtestPointer];
		return $subtest->pages[$curPageArrPointer];
	}
	
	/**
	 * Gets next or previous subtest index based on given current index.
	 * If no previous subtest can be found, given subtest will be returned.
	 * If no next subtest can be found, a negative value will be returned.
	 * @param integer $curSubtestPointer
	 * @param integer $pageDirection
	 * @return integer
	 */
	private function getNextSubtestIndex($curSubtestPointer, $pageDirection = TestEngineController::TE_PAGE_FORWARD){
		//gets previous or next(default) subtest
		if(!isset($curSubtestPointer)){
			//beginning of the test, get first subtest
			return 0;
		}
		//next subtest
		elseif($pageDirection == TestEngineController::TE_PAGE_FORWARD){
			if($curSubtestPointer==count($this->testRunObj->subtests)-1){
				//end of all subtests
				$curSubtestPointer = -1;
			}else{
				++$curSubtestPointer;
			}
		}
		//previous subtest
		elseif($pageDirection == TestEngineController::TE_PAGE_BACKWARD){
			if($curSubtestPointer>0){
				--$curSubtestPointer;
			}
		}
		//set session value
		//save to testrun
		$this->saveSubTestAndPagePointer($curSubtestPointer, null);
		return $curSubtestPointer;
	}
	
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('index'),
						'users'=>array('@'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	


	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('//site/error', $error);
		}
	}

}