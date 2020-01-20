<?php

class ShiftsController extends Controller
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
				array('allow', 'users'=>array('@'),),
				array('deny', 'users'=>array('*'),),
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
		new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			

		$model=new Shifts;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Shifts']))
		{
			$model->attributes=$_POST['Shifts'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
		new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			

		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Shifts']))
		{
			$model->attributes=$_POST['Shifts'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
		new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			

		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Shifts');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{	
		new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			
	}

	public function actionChange()
	{
		if (!Yii::app()->user->checkAccess('manager'))  
			new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			

		$shift = new Shifts();
		$shift->unsetAttributes();		
		$shift->change($_GET['azsId']);
		$this->redirect($_GET['returnPath']);
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Shifts the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Shifts::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Shifts $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='shifts-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionRebuild()
	{
		echo 'actionRebuild<br>';
		$criteria = new CDbCriteria();
		$criteria->with = array('bookState');	
		$criteria->order = 't.azsId, t.id';	
		
		$tanks = Tanks::model()->findAll($criteria);		
		foreach ($tanks as $tank) {
			$criteria = new CDbCriteria();
			$criteria->with = array('shift');
			$criteria->compare('t.tankId', $tank->id);	
			$criteria->order = 't.shiftId';			
			$rgtanks = ShiftTanks::model()->findAll($criteria);		
			$bookvolume = $rgtanks[0]->bookStartFuelVolume;
			foreach ($rgtanks as $rgtank) {
     			$criteria = new CDbCriteria();
	    		$criteria->select = "sum(t.volume) as volume";
	    		$criteria->with = array('pumptransaction');
		    	$criteria->compare('pumptransaction.tankId', $tank->id);	
		    	$criteria->compare('t.date', '>='.Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $rgtank->shift->dateStart));	
		    	$criteria->compare('t.date', '<='.Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', $rgtank->shift->dateStop));	
				$op = CardOperations::model()->find($criteria);		
				if (isset($op))
					$v = -$op->volume;
				else
					$v = 0;
				echo 'shift: '.$rgtank->shiftId.', tank: '.$rgtank->tankId.', volume: '.$v.'<br>';
				$rgtank->bookStartFuelVolume = $bookvolume;
				$rgtank->saleVolume = $v;
				
				$bookvolume = $rgtank->bookStartFuelVolume - $rgtank->saleVolume;
				$rgtank->bookStopFuelVolume = $bookvolume;				
				$rgtank->save();
				
			}
			$tank->bookState->fuelVolume = $bookvolume;
			$tank->bookState->save();
		}
	}
	
}
