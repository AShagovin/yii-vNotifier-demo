<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			'usertoken' => 'ext.yii-notifier.GetUserTokenAction',
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		if($user = Yii::app()->request->getQuery('user')) {
			$loginForm = new LoginForm();
			$loginForm->username = $user;
			$loginForm->password = $user;
			$loginForm->login();
			if(Yii::app()->user->isGuest) {
				throw new CException("Not a valid user");
			}
			$this->renderPartial('index',null,false,true);
		} else {
			$this->render('index');
		}
	}

	public function actionSend() {
		$message = Yii::app()->request->getPost('message');
		$to = Yii::app()->request->getPost('to');
		if($message && $to) {
			if($to == 'broadcast') {
				Yii::app()->notifier->broadcast($message);
			} else {
				Yii::app()->notifier->send($to, $message);
			}
			Yii::app()->end();
		}
		
		Yii::app()->clientScript->registerCoreScript('jquery');
		$this->render('send');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}