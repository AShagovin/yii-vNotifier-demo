<?php

/**
 * The purpose of this file is to "hack" Neatbeans' code completion feature.<br/>
 * This way, Neatbeans will treat Yii::app's return value as a MyWebApp, so we can document appilcation components, etc.
 *
 * @author pgee
 */
class Yii {
	/**
	 * @return MyWebApp
	 */
	public static function app() {
		return 'MyWebApp';
	}
}

?>
