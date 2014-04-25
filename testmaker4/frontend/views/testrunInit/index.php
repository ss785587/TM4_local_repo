<h2><?php echo Yii::t('initialization','You have already started a test. Please choose:');?></h2>

<?php 
	//List testruns if exists
	foreach($dataProvider as $data){
		$this->renderPartial('//testrunInit/_TestRunView', array('data'=>$data));
	}
 ?>