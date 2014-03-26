<?php
/* @var $this UberTestController */
/* @var $data UberTest */
?>

<div class="uberTest_overview">
	<div class="uberTest_media"><?php echo CHtml::encode($data->mediaURL); ?></div>
	<div class="uberTest_rightWrapper">
		<div class="uberTest_name"><?php echo CHtml::encode($data->niceName); ?></div>
		<div class="uberTest_shortDescription"><?php echo CHtml::encode($data->descriptionShort); ?></div>
		<div class="uberTest_metadata">Meta Meta</div>
		<div class="uberTest_startBt"><?php echo CHtml::link(Yii::t('uberTest','Start Test!'),array('view','id'=>$data->idUberTest)); ?></div>
	</div>
	<div style="clear:both"></div>
</div>