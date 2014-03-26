<?php
/* @var $this UberTestController */
/* @var $model UberTest */

$this->breadcrumbs=array(
	'Uber Tests'=>array('index'),
	$model->niceName,
);

?>

<h1><?php echo Yii::t('uberTest','Overview of {test_nicename}', array('{test_nicename}'=> $model->niceName)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'niceName',
		'descriptionShort',
		'descriptionLong',
	),
)); ?>

<p><?php echo CHtml::link(Yii::t('uberTest','Start Test!'),array('startTest','id'=>$model->idUberTest)); ?></p>
