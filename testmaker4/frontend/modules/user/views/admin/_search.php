<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>

    <div class="row">
        <?php echo $form->label($model,'idUser'); ?>
        <?php echo $form->textField($model,'idUser'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'username'); ?>
        <?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>20)); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'email'); ?>
        <?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'activationKey'); ?>
        <?php echo $form->textField($model,'activationKey',array('size'=>60,'maxlength'=>128)); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'userSince'); ?>
        <?php echo $form->textField($model,'userSince'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'lastActive'); ?>
        <?php echo $form->textField($model,'lastActive'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'superuser'); ?>
        <?php echo $form->dropDownList($model,'superuser',$model->itemAlias('AdminStatus')); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'status'); ?>
        <?php echo $form->dropDownList($model,'status',$model->itemAlias('UserStatus')); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton(UserModule::t('Search')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->