<?php
/* @var $this UberTestController */
/* @var $data UberTest */
?>

<div class="uberTest_overview">
	<div class="uberTest_media"><?php echo CHtml::encode($data->values->mediaURL); ?></div>
	<div class="uberTest_rightWrapper">
		<div class="uberTest_name"><?php echo CHtml::encode($data->values->niceName); ?></div>
		<div class="uberTest_shortDescription"><?php echo CHtml::encode($data->values->descriptionShort); ?></div>
		<div class="uberTest_metadata">Meta Meta</div>
		<div class="uberTest_startBt">
		<?php if($data->meta["startBt"]) echo CHtml::link(Yii::t('uberTest','Start Test!'),array('view','id'=>$data->values->idUberTest)); ?>
		<?php if($data->meta["continueBt"]) echo CHtml::link(Yii::t('uberTest','Continue!'),array('continue','id'=>$data->values->idUberTest)); ?>
		<?php if($data->meta["showCertBt"]) echo CHtml::link(Yii::t('uberTest','Show Certificate!'),array('showCertificate','id'=>$data->values->idUberTest)); ?>
		</div>
	</div>
	<div style="clear:both"></div>
</div>