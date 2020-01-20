<?php

class TanksController extends Controller
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
	
		$model=new Tanks('search');			
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Tanks']))
			$model->attributes=$_GET['Tanks'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}	
	
	public function actionCreate()
	{
		$model=new Tanks;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Tanks']))
		{
			$model->attributes=$_POST['Tanks'];
			if($model->save())
				$this->redirect(array('admin','id'=>$model->id));
		}

		$this->render('create',array(
						'model'=>$model,
		));
	}	
  
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['Tanks']))
		{
			$model->attributes=$_POST['Tanks'];
			if($model->save())
				$this->redirect(array('admin','id'=>$model->id));
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
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}  
  
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria = new CDbCriteria();
		$criteria->with = array('fuel', 'bookState', 'realState', 'azs');
		$criteria->order = 'azs.name, t.name';	
		$criteria->condition = 't.visible <> 0';
		$data = Tanks::model()->findAll($criteria);
		$this->render('index',array('items'=>$data,));
	}
	
	public function actionRealStates()
	{
		$model=new Tanks('search');			
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Tanks']))
			$model->attributes=$_GET['Tanks'];
		$model->visible = true;

		$this->render('realStates',array(
			'model'=>$model,
		));
	}
	
	public function actionUpdateRealState()
	{
		if (!Yii::app()->user->checkAccess('tanks.update_real_state'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
			
		$model=new Tanks('updateRealState');	
		//$model->scenario = 'modify';
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Tanks']))
		{
			$model->attributes=$_POST['Tanks'];			
			if($model->saveRealState())
				$this->redirect(array('realStates','id'=>$model->id));			
		}

		$this->render('updateRealState',array(
			'model'=>$model,
		));
	}
	
	public function actionLevelToVolume()
	{
		if(isset($_GET['id']) && isset($_GET['level'])) {
			$tankId = (int)$_GET['id'];
			$level = (double)$_GET['level'];			
		}
		if(isset($_POST['id']) && isset($_POST['level'])) {
			$tankId = (int)$_POST['id'];
			$level = (double)$_POST['level'];			
		}
		if(isset($tankId)) {
			$tank = Tanks::model()->findByPk($tankId);			
			echo $tank->levelToVolume($level);
		}
		else
			echo '';
		return;
	}	
	
	public function actionTanksByAzs() {
		$azsId = Yii::app()->getRequest()->getParam('azsId');
		if(Yii::app()->request->isAjaxRequest && $azsId) {
			$criteria = new CDbCriteria;
			$criteria->compare('azsId',  $azsId);
			$criteria->compare('visible',  2);
			$criteria->order = 'name';
			$tanks = Tanks::model()->findAll($criteria);
			$result = array();
			foreach($tanks as $x) {
				$result[] = array('id'=>$x->id, 'label'=>$x->name);
			}
			echo CJSON::encode($result);
			Yii::app()->end();
		}
	}	
	
	public function actionTanksByAzs2() {
		$azsId = Yii::app()->getRequest()->getParam('azsId');
		if(Yii::app()->request->isAjaxRequest && $azsId) {
			$criteria = new CDbCriteria;
			$criteria->compare('azsId',  $azsId);
			$criteria->order = 'name';
			$tanks = Tanks::model()->findAll($criteria);
			$result = array();
			foreach($tanks as $x) {
				$result[] = array('id'=>$x->id, 'label'=>$x->name);
			}
			echo CJSON::encode($result);
			Yii::app()->end();
		}
	}	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Tanks::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='tanks-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
