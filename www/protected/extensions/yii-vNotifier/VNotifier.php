<?php

Yii::setPathOfAlias('vNotifier',dirname(__FILE__));
Yii::import('vNotifier.LocalMessageStore');

/**
 * VdxNotification Singleton application component
 *
 * @author pgee
 * @method string getUserSecret($user_id) Returns the specified user's secret hash
 * @method string generateUserSecret($user_id) Generates and save and returns secret hash
 */
class VNotifier extends CApplicationComponent {
	/**
	 * Should we save the notifications to a persistent database or not
	 * @var boolean
	 */
	public $saveHistory = false;


	/**
	 * Url of the notification server
	 * @var string
	 */
	public $socketioUrl;
	/**
	 * The port where socket.io listens
	 * @var string
	 */
	public $socketioPort = 4001;
	/**
	 * Config params of the message store
	 * @var array
	 */
	public $messageStoreConfig = array();
	/**
	 * MessageStore object
	 * @var IMessageStore
	 */
	private $_ms;

	public function init() {
		parent::init();
		
		if(!isset($this->socketioUrl)) {
			// set the default notification server url
			$this->socketioUrl = Yii::app()->request->getHostInfo().':'.$this->socketioPort;
		}

		$this->_ms = Yii::createComponent($this->messageStoreConfig);
		if(! $this->_ms instanceof IMessageStore) {
			throw new CException('Message Store must implement IMessageStore');
		}
	}

	/**
	 * Sends a message to the given user
	 * @param type $user_id
	 * @param type $message
	 */
	public function send($user_id,$message) {
		$this->publish($this->getUserSecret($user_id), $message);
	}
	
	/**
	 * Send a broadcast message
	 * @param type $message
	 */
	public function broadcast($message) {
		$this->publish('broadcast', $message);
	}

	/**
	 * Publish the given message to the message store
	 * @param type $channel
	 * @param type $message
	 */
	private function publish($channel,$message) {
		$this->_ms->publishMessage($channel,$message);
	}

	public function __call($name, $parameters) {
		if(in_array($name, array('getUserSecret','generateUserSecret'))) {
			// proxy request to the message store
			return call_user_func_array(array($this->_ms,$name), $parameters);
		} else {
			return parent::__call($name, $parameters);
		}
	}
}

?>
