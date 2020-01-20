<?php

Yii::import('application.extensions.MyUtils');

class CardsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	public $select = '';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

    public function accessRules()
    {
        return array(      		
            array('allow', // allow authenticated users to access all actions
                'users'=>array('@'),
            ),
            array('deny'),
//              array('allow'),
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
		if (!Yii::app()->user->checkAccess('cards.update')) 
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			
		
		$model=new Cards;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Cards']))
		{
			$model->attributes=$_POST['Cards'];
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
		if (!Yii::app()->user->checkAccess('cards.update')) 
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			
				
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Cards']))
		{
			$model->attributes=$_POST['Cards'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->user->checkAccess('cards.update')) 
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
		$model=new Cards('search');			
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['type']))
			$model->type = $_GET['type'];		
		$model->expire = 1;
		if(isset($_GET['Cards']))
			$model->attributes=$_GET['Cards'];

		$this->render('index',array(
			'model'=>$model,
		));				
	}
	
	public function actionExcel()
	{
		$model=new Cards('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Cards']))
			$model->attributes=$_GET['Cards'];
	
		$this->render('excel',array(
				'model'=>$model,
		));
	}	
	
	public function actionRefill($id)
	{		
		if (!Yii::app()->user->checkAccess('cards.refill')) 
			throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			

		if(isset($_POST['CardOperations']))	{
			$params = $_POST['CardOperations'];
			$connection=Yii::app()->db;
			$transaction=$connection->beginTransaction();
			try {
				if ($connection->createCommand('SHOW PROCEDURE STATUS WHERE Db = DATABASE() AND name = "cardRefill2"')->queryRow()) {
					$sql = 'call cardRefill2(:in_date, :in_userId, :in_cardId, :in_fuelId, :in_volume, :in_document)';
					$command = $connection->createCommand($sql);
					$command->bindValues(array (
						'in_date'=>MyUtils::datetimeFormat('yyyy-MM-dd HH:mm:ss', time()),
						'in_userId'=>Yii::app()->user->id,
						'in_cardId'=>$params['cardId'],
						'in_fuelId'=>$params['fuelId'],
						'in_volume'=>$params['volume'],
						'in_document'=>(isset($params['refillDescription'])) ? $params['refillDescription'] : ''
					));				
				}
				else {
					$sql = 'call cardRefill(:in_userId, :in_cardId, :in_fuelId, :in_volume, :in_document)';
					$command = $connection->createCommand($sql);
					$command->bindValues(array (
						'in_userId'=>Yii::app()->user->id,
						'in_cardId'=>$params['cardId'],
						'in_fuelId'=>$params['fuelId'],
						'in_volume'=>$params['volume'],
						'in_document'=>(isset($params['refillDescription'])) ? $params['refillDescription'] : ''
					));
				}
				$command->execute();
				$transaction->commit();
			} catch(Exception $e) {
				$transaction->rollBack();
				throw $e;
			}						
			$card = Cards::model()->findByPk($params['cardId']);
			$card->update_date = Yii::app()->DateFormatter->format('yyyy-MM-dd HH:mm:ss', time());
			$card->save();
			
			$this->redirect(array('view','id'=>$params['cardId']));
		}

		$this->render('refill',array(
			'model'=>new CardOperations,
		));
	}	

	/**
	 * Manages all models.
	 */
	/*
	public function actionAdmin()
	{
		$model=new Cards('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Cards']))
			$model->attributes=$_GET['Cards'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	*/

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Cards::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='cards-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionAutoCompleteByOwner() {
		$term = Yii::app()->getRequest()->getParam('term');
		if(Yii::app()->request->isAjaxRequest && $term) {
			$criteria = new CDbCriteria;
			$criteria->select = 't.owner';
			$criteria->distinct = true;
			$criteria->compare('owner', $term, true);
//			$criteria->condition = 'owner like "'.$term.'%"';
			$cards = Cards::model()->findAll($criteria);
			$result = array();
			foreach($cards as $card) {
				$label = $card->owner;
				$result[] = array('id'=>$label, 'label'=>$label, 'value'=>$label);
			}
			echo CJSON::encode($result);
			Yii::app()->end();
		}
	}
	
	public function actionAutoCompleteByCardDescription() {
		$term = Yii::app()->getRequest()->getParam('term');
		if(Yii::app()->request->isAjaxRequest && $term) {
			$criteria = new CDbCriteria;
			$criteria->select = 't.description';
			$criteria->distinct = true;
			$criteria->compare('description', $term, true);
//			$criteria->condition = 'description like "'.$term.'%"';
			$cards = Cards::model()->findAll($criteria);
			$result = array();
			foreach($cards as $card) {
				$label = $card->description;
				$result[] = array('id'=>$label, 'label'=>$label, 'value'=>$label);
			}
			echo CJSON::encode($result);
			Yii::app()->end();
		}
	}
		
	public function actionAutoCompleteByDriver() {
		$term = Yii::app()->getRequest()->getParam('term');
		if(Yii::app()->request->isAjaxRequest && $term) {
			$criteria = new CDbCriteria;
			$criteria->condition = '(ownerType = 0 or ownerType = 1) and  owner like "'.$term.'%"';
			$cards = Cards::model()->findAll($criteria);
			$result = array();
			foreach($cards as $card) {
				$label = $card->owner;
				$result[] = array('id'=>$label, 'label'=>$label, 'value'=>$label);
			}
			echo CJSON::encode($result);
			Yii::app()->end();
		}
	}

	public function actionAutoCompleteByAuto() {
		$term = Yii::app()->getRequest()->getParam('term');
		if(Yii::app()->request->isAjaxRequest && $term) {
			$criteria = new CDbCriteria;
			$criteria->condition = 'ownerType = 2 and owner like "'.$term.'%"';
			$cards = Cards::model()->findAll($criteria);
			$result = array();
			foreach($cards as $card) {
				$label = $card->owner;
				$result[] = array('id'=>$label, 'label'=>$label, 'value'=>$label);
			}
			echo CJSON::encode($result);
			Yii::app()->end();
		}
	}		
	
}
