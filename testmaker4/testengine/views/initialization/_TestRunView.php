<div class="testRun_overview">
	<div class="tr_name"><?php echo CHtml::encode($data->idTestRun); ?></div>
	<div class="tr_attr_wrapper">
		<div class="tr_started_lbl">Started on:</div>
		<div class="tr_started_value"><?php //echo CHtml::encode($data->started); ?></div>
	</div>
	<div class="tr_attr_wrapper">
		<div class="tr_started_lbl">Process:</div>
		<div class="tr_started_value"><?php //echo CHtml::encode($data->process); ?></div>
	</div>
	<div class="link_wrapper">
		<p><?php echo CHtml::link(Yii::t('initialization','Continue Test!'),array('initUserChoise','testRunId'=>$data->idTestRun)); ?></p>
	</div>
</div>