<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("DataPrivacyStatement");
$this->breadcrumbs=array(
	UserModule::t("DataPrivacyStatement"),
);
?>


<p><?php $this->renderPartial('//dataPrivacyDefinition/_view', array('statementDescription'=>$statementDescription)); ?> </p>

<div class="form">
<?php echo CHtml::beginForm(); ?>

	<div class="row submit">
		<?php echo CHtml::link('accept',array('dataprivacyAccepted')); ?>
		<?php echo CHtml::link('decline',array('dataprivacyDecline')); ?>
		<?php if(!Yii::app()->user->isGuest){echo CHtml::link('delete my account',array('deleteAccount'));} ?>
	</div>
	
<?php echo CHtml::endForm(); ?>
</div><!-- form -->