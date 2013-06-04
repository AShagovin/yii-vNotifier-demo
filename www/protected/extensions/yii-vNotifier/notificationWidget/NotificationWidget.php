<?php

/**
 * Description of NotificationWidget
 *
 * @author pgee
 */
class NotificationWidget extends CWidget {
	/**
	 * Name of the notfier component
	 * @var string
	 */
	public $notifierComponent = 'notifier';

	public function init() {
		parent::init();

		if(Yii::app()->user->canReceiveNotification()) {
			$commonAssetUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias('ext.yii-vNotifier.assets'),false,-1,true);
			$widgetAssetUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias('ext.yii-vNotifier.notificationWidget.assets'),false,-1,true);
			Yii::app()->clientScript->registerCssFile($widgetAssetUrl.'/notification-widget.css');
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerScriptFile($widgetAssetUrl.'/knockout-2.2.0.js');
			Yii::app()->clientScript->registerScriptFile($this->getNotifier()->socketioUrl.'/socket.io/socket.io.js');
			Yii::app()->clientScript->registerScriptFile($commonAssetUrl.'/socket.io.client.js');
		}
	}

	public function run() {
		parent::run();

		if(Yii::app()->user->canReceiveNotification()) {
			$this->render('notificationWidget');
		}
	}

	public function getNotifier() {
		return Yii::app()->{$this->notifierComponent};
	}
	
}

?>
