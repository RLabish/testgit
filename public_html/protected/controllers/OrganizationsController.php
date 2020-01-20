<?php

class OrganizationsController extends Controller
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
		//	'postOnly + delete', // we only allow deletion via POST request
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
		if (!Yii::app()->user->checkAccess('list.update'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
		
		$model=new Organizations;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Organizations']))
		{
			$model->attributes=$_POST['Organizations'];
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
		if (!Yii::app()->user->checkAccess('list.update'))
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
		
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Organizations']))
		{
			$model->attributes=$_POST['Organizations'];
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
		if (!Yii::app()->user->checkAccess('list.update'))
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
		$model=new Organizations('search');			
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Organizations']))
			$model->attributes=$_GET['Organizations'];

		$this->render('index',array(
			'model'=>$model,
		));
	}
	
	public function actionRefill($id)
	{		
		if (!Yii::app()->user->checkAccess('cards.refill')) 
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			

		if(isset($_POST['OrganizationOperations']))	{
			$params = $_POST['OrganizationOperations'];
			$org = Organizations::model()->findByPk($params['orgId']);
			$org->incBalance($params['fuelId'], $params['volume'], $params['note']);			
			$this->redirect(array('view','id'=>$params['orgId']));
		}

		$this->render('refill',array(
			'model'=>new OrganizationOperations,
		));
	}	


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Organizations the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Organizations::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Organizations $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='Organizations-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionAutocompleteByName() {
		$term = Yii::app()->getRequest()->getParam('term');
		if(Yii::app()->request->isAjaxRequest && $term) {
			$criteria = new CDbCriteria;
			$criteria->condition = 'name like "'.$term.'%"';
			$Organizations = Organizations::model()->findAll($criteria);
			$result = array();
			foreach($Organizations as $supplier) {
				$lable = $supplier['name'];
				$result[] = array('id'=>$supplier['id'], 'label'=>$lable, 'value'=>$lable);
			}
			echo CJSON::encode($result);
			Yii::app()->end();
		}
	}	
}
