<?php

/**
 * VdxNotification Singleton application component
 *
 * @author pgee
 */

require dirname(__FILE__).'/Predis/Autoloader.php';

Predis\Autoloader::register();

class VNotifier extends CApplicationComponent {

	/**
	 * Should we save the notifications to a persistent database or not
	 * @var boolean
	 */
	public $saveHistory = false;

	/**
	 * Redis connection string
	 * @var string
	 */
	public $redisConnectionString = null;

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
	 * Our Redis Client
	 * @var Predis\Client
	 */
	private $_rc;

	public function init() {
		parent::init();
		
		if(!isset($this->socketioUrl)) {
			// set the default notification server url
			$this->socketioUrl = Yii::app()->request->getHostInfo().':'.$this->socketioPort;
		}
		
		$this->_rc = new \Predis\Client($this->redisConnectionString);	
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
	 * Publish the given message to redis
	 * @param type $channel
	 * @param type $message
	 */
	private function publish($channel,$message) {
		if(is_array($message)) {
			$message = CJSON::encode($message);
		}
		$this->_rc->publish($channel,$message);	
	}
	
	/**
	 * Reads the user's secret from redis
	 * @param type $user_id
	 * @return type
	 */
	public function getUserSecret($user_id) {
		return $this->_rc->get($user_id);
	}

	/**
	 * Generates a random hash
	 * @return string
	 */
	private static function genSecret() {
//		return Yii::app()->securityManager->generateRandomKey();
		return sprintf('%08x%08x%08x%08x',mt_rand(),mt_rand(),mt_rand(),mt_rand()); 
	}

	/**
	 * Generates a uniqe secret hash for the given user
	 * @param type $user_id
	 * @return type
	 */
	public function generateUserSecret($user_id) {
		$secret = self::genSecret();
		while($this->_rc->exists($secret)) {
			$secret = self::genSecret();
		}

		$this->_rc->set($user_id,$secret);

		return $secret;
	}

}

?>
