<?php

class CardOperationsController extends Controller
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
			array('deny',  
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$model=new CardOperations('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CardOperations']))
			$model->attributes=$_GET['CardOperations'];
		else if (Yii::app()->theme->name == 'demo') {
			$model->date_range['from'] =  Yii::app()->params['reports.date_now'];
			$model->date_range['to'] = Yii::app()->params['reports.date_now'];
		}		
		
		$this->render('index',array(
				'model'=>$model,
		));
	}
		
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}	
	
	public function actionDelete($id)
	{
		if (!Yii::app()->user->checkAccess('oper.delete'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
		
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}	
	
	public function actionExcel()
	{
		$model=new CardOperations('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CardOperations']))
			$model->attributes=$_GET['CardOperations'];
	
		$this->render('excel',array(
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
		$model=CardOperations::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='card-operations-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
