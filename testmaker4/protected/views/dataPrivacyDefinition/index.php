<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("DataPrivacyStatement");
$this->breadcrumbs=array(
	Yii::t('dataPrivacy','data privacy statement')
);
?>


<p><?php $this->renderPartial('//dataPrivacyDefinition/_view', array('statementDescription'=>$statementDescription)); ?> </p>

<div class="form">

	<div class="row submit">
		<?php echo CHtml::link(Yii::t('dataPrivacy','accept'),array('dataprivacyAccepted')); ?>
		<?php echo CHtml::link(Yii::t('dataPrivacy','decline'),array('dataprivacyDecline')); ?>
		<?php if(!Yii::app()->user->isGuest){echo CHtml::link(Yii::t('dataPrivacy','delete my account'),array('deleteAccount'));} ?>
	</div>
</div><!-- form -->