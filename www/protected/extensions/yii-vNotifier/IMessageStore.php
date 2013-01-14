<?php

/**
 * Interface for message stores
 */
interface IMessageStore {
	/**
	 * Generates a secret hash for the specified user if he or she don't have or $refresh is true and saves it to the store.
	 * @param string $user_id
	 * @param boolean $refresh
	 * @return string The generated hash
	 */
	public function generateUserSecret($user_id,$refresh = false);
	/**
	 * Returns the user's secret hash if has any
	 * @param string $user_id
	 * @return mixed The user's secret hash 
	 */
	public function getUserSecret($user_id);
	/**
	 * Publish a message
	 * @param string $channel
	 * @param mixed $message
	 */
	public function publishMessage($channel,$message);
}

?>
