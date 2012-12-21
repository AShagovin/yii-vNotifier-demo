<?php 
	echo CHtml::form('#','post',array('id'=>'notification-form'));
?>
	<div class="row">
		<?php echo CHtml::dropDownList('to', null, array('broadcast' => 'Broadcast','admin'=>'Admin','demo'=>'Demo'));?>
	</div>
	<div class="row">
		<?php echo CHtml::textArea('message');?>
	</div>
	<div class="row">
		<?php echo CHtml::submitButton('send');?>
	</div>
<?php
	echo CHtml::endForm();
?>
<script type="text/javascript">
	$(function() {
		$('#notification-form').submit(function(event) {
			console.log('submit');
			event.preventDefault();
			$.ajax({
				url : '/site/send',
				type : 'post',
				data : $(this).serialize()
			});
		});
	});	
</script>