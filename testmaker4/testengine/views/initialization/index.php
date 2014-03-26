<h2><?php echo Yii::t('initialization','You have already started a test. Please choose:');?></h2>

<div class="testRun_overview">
	<h3><p><?php echo CHtml::link(Yii::t('initialization','Start new Test!'),array('initUserChoise', 'newTest'=>true)); ?></p></h3>
</div>
<?php 
	//List testruns if exists
	foreach($dataProvider as $data){
		$this->renderPartial('_TestRunView', array('data'=>$data));
	}
 ?>