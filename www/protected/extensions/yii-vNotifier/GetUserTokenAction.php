<?php

class GetUserTokenAction extends CAction {
	
	public function run() {

		$response = array(
			'userToken' => Yii::app()->user->getToken(true),
		);
		echo headers_sent() ? '' : header('Content-type: application/json');

		echo CJSON::encode($response);
		Yii::app()->end();
	}
}