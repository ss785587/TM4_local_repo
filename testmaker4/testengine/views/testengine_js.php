<?php
$new = "Test";

$js_string = 'var x=\''.$new.'\'; alert(x);';

//add javascript to html document
$cs = Yii::app()->getClientScript();
$cs->registerScript(
		'scriptName_AlertTest',
		$js_string,
		CClientScript::POS_END
);
?>
