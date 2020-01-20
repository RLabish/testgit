<?php

class TankInventoryController extends Controller
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
//			'postOnly + delete', // we only allow deletion via POST request
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if (!Yii::app()->user->checkAccess('fuel.update'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
		
		$model=new TankInventory;
		$model->scenario = 'modify';
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['TankInventory']))
		{
			$model->attributes=$_POST['TankInventory'];
			if($model->save())
				$this->redirect(array('index','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if (!Yii::app()->user->checkAccess('fuel.update'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
		
		$model=$this->loadModel($id);
		$model->scenario = 'modify';
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['TankInventory']))
		{
			$model->attributes=$_POST['TankInventory'];
			if($model->save())
				$this->redirect(array('index','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->user->checkAccess('fuel.update'))
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
		$model=new TankInventory('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TankInventory']))
			$model->attributes=$_GET['TankInventory'];
		else {
			if (Yii::app()->theme->name == 'demo') {
				$model->date_range['from'] =  Yii::app()->params['reports.date_from'];
				$model->date_range['to'] = Yii::app()->params['reports.date_to'];
			}
			else {			
				$now = getdate(time());
				$dt = date_create();
				date_date_set($dt, $now['year'], $now['mon'], 1);
				$model->date_range['from'] = date_format($dt, 'd.m.Y');
				date_add($dt, new DateInterval('P1M'));
				date_sub($dt, new DateInterval('P1D'));
				$model->date_range['to'] = date_format($dt, 'd.m.Y');
			}
		}
		$this->render('index',array(
				'model'=>$model,
		));				
	}
	
	public function actionExcel()
	{
		$model=new TankInventory('search');
		$model->unsetAttributes();
		if(isset($_GET['TankInventory']))
			$model->attributes=$_GET['TankInventory'];
	
		$this->render('excel',array(
				'model'=>$model,
		));
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return TankInventory the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=TankInventory::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param TankInventory $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='tank-inventory-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
