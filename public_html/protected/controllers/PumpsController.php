<?php

class PumpsController extends Controller
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
						'users'=>array('@'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}

	public function actionAdmin()
	{
		if (!Yii::app()->user->checkAccess('admin'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
	
		$model=new Pumps('search');			
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Pumps']))
			$model->attributes=$_GET['Pumps'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}	
	
	public function actionCreate()
	{
		$model=new Pumps;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Pumps']))
		{
			$model->attributes=$_POST['Pumps'];
			if($model->save())
				$this->redirect(array('index','id'=>$model->id));
		}

		$this->render('create',array(
						'model'=>$model,
		));
	}	
  
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['Pumps']))
		{
			$model->attributes=$_POST['Pumps'];
			if($model->save())
				$this->redirect(array('index','id'=>$model->id));
		}

		$this->render('update',array(
						'model'=>$model,
		));
	}
  
	public function actionDelete($id)
	{
		// we only allow deletion via POST request
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
		$model=new Pumps('search');			
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Pumps']))
			$model->attributes=$_GET['Pumps'];

		$this->render('index',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Pumps::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='Pumps-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
