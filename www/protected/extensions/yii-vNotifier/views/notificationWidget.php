<h1>Notifications</h1>
<div id="notification-area">
	<ul data-bind="foreach: notifications">
		<li data-bind="text: message"></li>
	</ul>
</div>
<?php
	/* @var $this NotificationWidget */
	$config = array(
		'userSecret' => Yii::app()->user->getSecret(),
		'socketioUrl' => $this->getNotifier()->socketioUrl,
	);


	Yii::app()->clientScript->registerScript('vNotifierClient','var notifierClient = new vNotifier.Client('.CJSON::encode($config).')');
?>

