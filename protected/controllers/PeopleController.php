<?php

class PeopleController extends Controller
{
	private $_family = null; //to store the related family object
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
			'familyContext + create',  //this will apply the familyContext filter to the actionCreate method
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		/*return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);*/
		
		return array(
			array(
				'allow',
				'actions' => array('index','view'),
				'roles' => array('reader'),
			),
			array(
				'allow',
				'actions' => array('create', 'update'),
				'roles' => array('editor'),
			),
			array(
				'allow',
				'actions' => array('delete','admin'),
				'roles' => array('administrator'),
			),
			array('deny'),
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
		$model=new People;
		
		//set the family id for this new People object. 
		//this _family object has been populated by the filter which runs prior to the actionCreate method.
		$model->family_id = $this->_family->id; 

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['People']))
		{
			$model->attributes=$_POST['People'];
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
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['People']))
		{
			$model->attributes=$_POST['People'];
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
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		//the following 4 line are the original index section
		//$dataProvider=new CActiveDataProvider('People');
		//$this->render('index',array(
		//	'dataProvider'=>$dataProvider,
		//));
		
		$model=new People('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['People']))
			$model->attributes=$_GET['People'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new People('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['People']))
			$model->attributes=$_GET['People'];

		$this->render('admin',array(
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
		$model=People::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='people-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Perform the filter context
	 */
	public function filterFamilyContext($filterChain)
	{
		$familyId = null;
		if (isset($_GET['fid']))
		{
			$familyId = $_GET['fid'];
		}
		else if (isset($_POST['fid']))
		{
			$familyId = $_POST['fid'];
		}
		$this->loadFamily($familyId);
		
		$filterChain->run();
	}
	
	
	//provide the method for load family
	protected function loadFamily($fam_id)
	{
		if ($this->_family===null)
		{
			$this->_family = Family::model()->findByPk($fam_id);
			if ($this->_family===null)
			{
				throw new CHttpException(404, 'The Required Family does not exist.');
			}
			return $this->_family;
		}
	}

	
	
}
