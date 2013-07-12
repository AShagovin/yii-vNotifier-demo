<?php

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

	
	/**
	 * afterLogin "callback"
	 * @param type $fromCookie
	 */
	public function afterLogin($fromCookie) {
		parent::afterLogin($fromCookie);
		
		// genereate a tokent after login
		// TODO: we should'nt generete a new token when a client is currently connected whit this user id.
		$this->_secret = $this->getNotifier()->generateUserToken($this->id);
	}

	/**
	 * Returns the currently logged in user's token
	 * @return mixed
	 */
	public function getToken() {
		if($this->getIsGuest()) {
			return null;
		} else {
			if(!isset($this->_secret))	{
				$this->_secret = $this->getNotifier()->getUserToken($this->id);
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
	public function notify($message,$type='notification') {
		$this->getNotifier()->send($this->id,$message,$type);
	}

}

?>
