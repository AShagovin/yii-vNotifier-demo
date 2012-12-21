<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NotifiedWebuser
 *
 * @author pgee
 */
class NotifiedWebUser extends CWebUser {
	/**
	 * Name of the notifier application component
	 * @var string 
	 */
	public $notifierComponent = 'notifier';
	
	/**
	 * Unique Hash
	 * @var string
	 */
	private $_secret;

	/**
	 * Returns the notifier application component
	 * @return VNotifier
	 */
	private function getNotifier() {
		return Yii::app()->{$this->notifierComponent};
	}

	
	public function afterLogin($fromCookie) {
		parent::afterLogin($fromCookie);
		
		// genereate a secret after login
		// TODO: we should'nt generete the secret when a client is currently connected whit this user id.
		$this->_secret = $this->getNotifier()->generateUserSecret($this->id);
	}

	/**
	 * Returns the currently logged in user's secret
	 * @return mixed
	 */
	public function getSecret() {
		if($this->getIsGuest()) {
			return null;
		} else {
			if(!isset($this->_secret))	{
				$this->_secret = $this->getNotifier()->getUserSecret($this->id);
			}

			return $this->_secret;
		}
	}

	/**
	 * Returns whether user can be notfied
	 * @return boolean
	 */
	public function canReceiveNotification() {
		return !$this->isGuest;
	}

	/**
	 * Shortcut to notity the currently logged in user
	 * @param type $message
	 */
	public function notify($message) {
		$this->getNotifier()->send($this->id,$message);
	}

}

?>
