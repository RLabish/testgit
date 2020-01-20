<?php

class SyslogController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index',),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
//		Syslog::info('test');
		if (!Yii::app()->user->checkAccess('admin'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
		$model=new Syslog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Syslog']))
			$model->attributes=$_GET['Syslog'];
		
		$this->render('index',array(
				'model'=>$model,
		));				
	}



	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Syslog the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Syslog::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Syslog $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='syslog-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
