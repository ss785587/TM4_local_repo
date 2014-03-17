<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<?php if(!Yii::app()->user->isGuest):?>
<p>
   You are logged in now!! Welcome <?php echo Yii::app()->user->username; ?>.  
</p>
<?php endif;?>