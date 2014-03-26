<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<h1>TEST-ENGINE</h1>

<?php if(!Yii::app()->user->isGuest):?>
<p>
   You are logged in now!! Welcome <?php echo Yii::app()->user->username; ?>.  
</p>
<?php endif;?>
