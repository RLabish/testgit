<?php

class UsersController extends Controller
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
			array('allow', 
				'actions'=>array('index','view','create','update','delete','excel','newpassword'),					
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if (!Yii::app()->user->checkAccess('admin'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.')); 
		$model=new Users;
		$model->scenario = 'create';

		if(isset($_POST['Users']))
		{
			$model->attributes=$_POST['Users'];			
			if($model->save()) 
						$this->redirect(array('index',));
		}
		$this->render('create',array('model'=>$model,));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if (!Yii::app()->user->checkAccess('admin'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.')); 
	
		$model=$this->loadModel($id);
		$model->scenario = 'update';		

		if(isset($_POST['Users']))
		{
			$model->attributes=$_POST['Users'];		
			if($model->save())
				$this->redirect(array('index','id'=>$model->id));
		}
		$this->render('update',array('model'=>$model,));
	}

	public function actionNewpassword($id)
	{
		if (!Yii::app()->user->checkAccess('admin'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.')); 

		$model=$this->loadModel($id);
		$model->scenario = 'newpassword';		

		if(isset($_POST['Users']))
		{
			$model->attributes=$_POST['Users'];		
			if($model->save())
				$this->redirect(array('update','id'=>$model->id));
		}
		$this->render('newpassword',array('model'=>$model,));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->user->checkAccess('admin'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.')); 
	
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		if (!Yii::app()->user->checkAccess('admin'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.')); 
	
		$model=new Users('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Users']))
			$model->attributes=$_GET['Cards'];		
		$this->render('index',array(
				'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Users('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Users']))
			$model->attributes=$_GET['Users'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Users the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Users::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Users $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
