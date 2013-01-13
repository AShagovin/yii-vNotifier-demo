<?php

/**
 * Interface for message stores
 */
interface IMessageStore {
	public function generateUserSecret($user_id);
	public function getUserSecret($user_id);
	public function publishMessage($channel,$message);
}

?>
