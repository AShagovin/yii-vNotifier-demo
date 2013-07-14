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
	private $_token;

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
		
		// load
		$this->loadUserToken();
	}

	/**
	 * Returns the currently logged in user's token
	 * @return mixed
	 */
	public function getToken($forceLoad  = false) {
		if($this->getIsGuest()) {
			return null;
		} else {

			$this->loadUserToken($forceLoad);
			return $this->_token;
		}
	}


	private function loadUserToken($forceLoad = false) {

		if(!isset($this->_token) || $forceLoad) {
			$this->_token = $this->getNotifier()->getUserToken($this->id);

			if(empty($this->token)) {
				// generate a new token
				$this->_token = $this->getNotifier()->generateUserToken($this->id);
			}
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
