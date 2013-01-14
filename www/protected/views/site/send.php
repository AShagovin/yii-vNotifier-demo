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

<div class="left iframe">
	<p>Iframe: Logged in as Admin user (/site/index?user=admin)</p>
	<iframe src="/site/index?user=admin"></iframe>
</div>
<div class="right iframe">
	<p>Iframe: Logged in as Demo user (/site/index?user=demo)</p>
	<iframe src="/site/index?user=demo"></iframe>
</div>

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