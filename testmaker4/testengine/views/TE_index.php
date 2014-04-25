<div class='TE_wrapper'>
	<div id='TE_content'>
		<?php 
			Yii::app()->getClientScript()->registerScript(
				'init_TE',
				'initData();',
				CClientScript::POS_END);
		?>
	</div>
	<div id='TE_navigation'>
		<?php echo CHtml::button("Back",array('id'=>'TE_nav_back_bt','name'=>"Back",'onclick'=>'js:TE_nav_btBack();')); ?>
		<?php echo CHtml::button("Next",array('id'=>'TE_nav_next_bt','onclick'=>'js:TE_nav_btNext();')); ?>
	</div>
	<div id='error_msg'></div>
</div>