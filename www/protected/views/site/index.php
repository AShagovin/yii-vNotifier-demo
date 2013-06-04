<?php
/* @var $this SiteController */
?>

<h1>Hello <?php echo Yii::app()->user->id;?></h1>
	
<?php $this->widget('ext.yii-vNotifier.notificationWidget.NotificationWidget'); ?>

