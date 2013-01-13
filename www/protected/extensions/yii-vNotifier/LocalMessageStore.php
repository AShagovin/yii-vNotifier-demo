<?php

require dirname(__FILE__).'/Predis/Autoloader.php';

Predis\Autoloader::register();

Yii::import('vNotifier.IMessageStore');

/**
 * Description of LocalMessageStore
 *
 * @author pgee
 */
class LocalMessageStore extends CComponent implements IMessageStore {
	/**
	 * Redis connection string
	 * @var string
	 */
	public $redisConnectionString = null;
	/**
	 * Our Redis Client
	 * @var Predis\Client
	 */
	private $_rc;

	public function __construct() {
		$this->_rc = new \Predis\Client($this->redisConnectionString);	
	}

	/**
	 * Publish the given message to redis
	 * @param type $channel
	 * @param type $message
	 */
	public function publishMessage($channel,$message) {
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
