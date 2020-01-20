<?php

class TerminalEventsController extends Controller
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
		$model=new TerminalEvents('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TerminalEvents']))
			$model->attributes=$_GET['TerminalEvents'];
		else if (Yii::app()->theme->name == 'demo') {
			$model->date_range['from'] =  Yii::app()->params['reports.date_now'];
			$model->date_range['to'] = Yii::app()->params['reports.date_now'];
		}		

		
		$this->render('index',array(
				'model'=>$model,
		));				
	}

	public function actionExcel()
	{
		$model=new TerminalEvents('excel');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TerminalEvents']))
			$model->attributes=$_GET['TerminalEvents'];
	
		$this->render('excel',array(
				'model'=>$model,
		));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return TerminalEvents the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=TerminalEvents::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param TerminalEvents $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='terminal-events-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
