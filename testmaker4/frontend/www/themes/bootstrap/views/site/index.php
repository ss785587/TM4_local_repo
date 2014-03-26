<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>'Welcome to '.CHtml::encode(Yii::app()->name),
)); ?>

<p>Subtitle.... Subtitle...</p>

<?php $this->endWidget(); ?>

<p>Let's try the testmaker4.</p>
<p>All the content is static. Please change it...</p>

<?php if(!Yii::app()->user->isGuest):?>
<p>
   You are logged in now!! Welcome <?php echo Yii::app()->user->username; ?>.  
</p>
<?php endif;?>

    
  



