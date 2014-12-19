<?php

class CallController extends Controller
{
    private $tab_name = "call";
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            //'postOnly + delete', // we only allow deletion via POST request
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
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'create', 'update', "updateajax", 'delete', 'admin', 'grid', 'days'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
                'users' => array('admin'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */

    public function actionTest()
    {
        $dp = new CActiveDataProvider('Contact');/**/
        $model = new Contact();
        $model->client = new Client();
        $this->renderPartial('_grid',array(
            'model'=>$model,
            'dataProvider'=>$dp,
        ),false, true);/**/
    }
    public function actionCreate()
    {

        $dataProvider = new CActiveDataProvider(Contact::model()->fromToday(0));
        Yii::app()->session['action'] = 'call/create';

        $model = new Contact();
        $model->client = new Client();

        if (isset($_POST['Client'])) {
            $model->client = Client::model()->phone($_POST['Client']['phone'])->find();/**/
            if($model->client === null)
                $model->client = new Client();
            $model->client->attributes = $_POST['Client'];
            $model->attributes = $_POST['Contact'];
            if($model->save())
                $this->refresh();
        }

		$this->render('create',array(
            'dataProvider'=>$dataProvider,
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
        Yii::app()->session['action'] = 'call/update&id='.$id;
        $model=$this->loadModel($id);
        if(isset($_POST['Client']))
        {
            $model->client->attributes = $_POST['Client'];
            $model->attributes = $_POST['Contact'];
            if($model->save())
                $this->redirect(array('call/create'));
        }
        // Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$this->render('update',array(
			'model'=>$model,
		));/**/
	}
    private function loadDays(){
        if(isset($_POST['Contact']['days']))
        {
            Yii::app()->session['days'] = $_POST['Contact']['days'];
        }
        return Yii::app()->session['days'];
    }
    public function actionGrid()
    {

        $model = new Contact('search');
        $model->client=new Client('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Contact'])) {
            $model->attributes = $_GET['Contact'];
            $model->clientName = $_GET['Contact']['clientName'];
            $model->clientPhone = $_GET['Contact']['clientPhone'];
        }

        $model->Days = $this->loadDays();

        $this->renderPartial('_grid',array(
            'model'=>$model,
        ));
    }
//if(isset($_GET['Contact']['days'])) {
    //   Yii::app()->session['days'] = $_GET['Contact']['days'];
    //}
    //$dataProvider = $model->fromToday(Yii::app()->session['days'])->search();
    //$model->Days = Yii::app()->session['days'];
    //$dataProvider->pagination->route = 'call/grid';
    //$dataProvider->pagination->pageSize = 3;

    /**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        Contact::model()->deleteByPk($id);
        $this->redirect(array('call/create'));
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		/*if(!isset($_GET['ajax']))

			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));/**/
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        Yii::app()->session['tab'] = ContactType::tabId($this->tab_name);
        Yii::app()->session['days'] = 0;
        $this->actionCreate();
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Client('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Client'])) {
            $model->attributes = $_GET['Client'];
        }
		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Client the loaded model
	 * @throws CHttpException
	 */
    public function loadModel($id)
    {
        if($this->_model===null) {
            $this->_model = Contact::model()->with('client', 'studyForms', 'purposes', 'results')->findByPk($id);
        }
        if($this->_model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $this->_model;
    }

	/**
	 * Performs the AJAX validation.
	 * @param Client $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='client-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    public function actionUpdateAjax()
    {
        $model = new Contact();
        if (isset($_POST['Client']) && empty($_POST['Client']['name'])) {
            $model->client = Client::model()->phone($_POST['Client']['phone'])->find();/**/
        }
        if($model->client === null) {
            $model->client = new Client();
            $model->client->attributes = $_POST['Client'];
        }
        $model->attributes = $_POST['Contact'];

        $this->renderPartial('_form',array(
            'model'=>$model,
        ),false, true);
    }


}
