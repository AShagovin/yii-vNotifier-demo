<?php

/**
 * VdxNotification Singleton application component
 *
 * @author pgee
 */
class VNotifier extends CApplicationComponent {
	/**
	 * Hostname of the apiserver
	 * @var string
	 */
	public $apiUrl = 'http://localhost:4000';
	public $appSecret = 'Coming Soon --';

	/**
	 * Should we save the notifications to a persistent database or not
	 * @var boolean
	 */
	public $saveHistory = false;

	/**
	 * Returns the url where socket.io listens
	 * @return  string
	 */
	public function getSocketIOUrl() {
		return $this->apiUrl;
	}

	/**
	 * Sends a message to the given user
	 * @param type $user_id
	 * @param type $message
	 */
	public function send($user_id,$message,$type = 'notification') {
		$this->publish($this->getUserToken($user_id), $message, $type);
	}
	
	/**
	 * Send a broadcast message
	 * @param type $message
	 */
	public function broadcast($message,$type = 'notification') {
		$this->publish('broadcast', $message, $type);
	}

	/**
	 * Publish the given message
	 * @param type $channel
	 * @param type $message
	 */
	public function publish($channel,$message, $type) {
		$this->api('/publish',array(
			'channel' => $channel,
			'message' => CJSON::encode(array(
				'message' => $message,
				'type' => $type,
			)),
		));	
	}

	/**
	 * Gets the user's token from the notification server
	 * @param type $user_id
	 * @return type
	 */
	public function getUserToken($user_id) {
		$response = $this->api('/getusertoken', array(
			'user_id' => $user_id,
		));

		return $response['userToken'];
	}

	/**
	 * Generates a uniqe token for the given user
	 * @param type $user_id
	 * @return type
	 */
	public function generateUserToken($user_id,$refresh = false) {
		$response = $this->api('/generateusertoken',array(
			'user_id' => $user_id,
		));

		return $response['userToken'];
	}
	/**
	 * Makes an api call
	 * @param string $url the action as a pathname
	 * @param array $data the params of the specified action
	 * @return array the response from the api server
	 */
	private function api($url,$data) {
		$ch = curl_init();

		$data['__app_secret__'] = $this->appSecret; 
		
		curl_setopt($ch,CURLOPT_URL, $this->apiUrl.$url);
		curl_setopt($ch,CURLOPT_POSTFIELDS, CJSON::encode($data));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
		//	'Content-Length: ' . strlen($data_string)),
		));                          
		$response = curl_exec($ch);

		$responseHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($responseHttpCode == 403) {
			throw new CException('Your app secret is not valid');
		} elseif($responseHttpCode == 200) {
			// nop
		} else {
			throw new CException('Uknown Error: ' . $responseHttpCode );
		}

		//close connection
		curl_close($ch);

		return CJSON::decode($response);
	}

	

}

?>
