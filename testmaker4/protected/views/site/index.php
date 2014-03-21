<?php
/* @var $this SiteController */
//called from UberTestController
$this->pageTitle=Yii::app()->name;
?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<?php if(!Yii::app()->user->isGuest):?>
<p>
   You are logged in now!! Welcome <?php echo Yii::app()->user->username; ?>.  
</p>
<?php endif;?>

<?php 
	//List ubertests
	foreach($dataProvider as $data){
		$this->renderPartial('//uberTest/_smallView', array('data'=>$data));
	}
 ?>