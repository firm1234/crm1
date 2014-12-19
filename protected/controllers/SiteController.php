<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'

       // $model = new Users;


       /* Yii::trace("*******************************","error");
        Yii::trace(var_export($model->search(),true),"error");
        Yii::trace("*******************************","error");*/
/*
        if (Yii::app()->getRequest()->getIsAjaxRequest()) {
            header( 'Content-type: application/json' );
            $this->renderPartial('_grid', compact('model'));
            Yii::app()->end();
        }else*/

        /**
        $criteria->compare('id',$this->id);
        $criteria->compare('username',$this->username,true);
        $criteria->compare('password',$this->password,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('activkey',$this->activkey,true);
        //	$criteria->compare('superuser',$this->superuser);
        $criteria->compare('status',$this->status);
        /*	$criteria->compare('create_at',$this->create_at,true);
        $criteria->compare('lastvisit_at',$this->lastvisit_at,true);*/

/*
        $gridColumns = array(
            array('name'=>'id', 'header'=>'id', 'htmlOptions'=>array('style'=>'width: 160px')),
            array('name'=>'username', 'header'=>'username'),
            array('name'=>'password', 'header'=>'password'),
            array('name'=>'email', 'header'=>'email'),
            array('name'=>'activkey', 'header'=>'activkey'),
            array('name'=>'superuser', 'header'=>'superuser'),
            array('name'=>'status', 'header'=>'status'),
            array('name'=>'create_at', 'header'=>'create_at'),
            array('name'=>'lastvisit_at', 'header'=>'lastvisit_at'),
         //   array('name'=>'hours', 'header'=>'Hours worked'),

            /*
             array(
    'htmlOptions' => array('nowrap'=>'nowrap'),
    'class'=>'bootstrap.widgets.TbButtonColumn',
    'viewButtonUrl'=>'Yii::app()->createUrl("/item/view", array("id"=>$data["id"], "sector" => $data["sector"]["slug"],"title" => $data["slug"]))',
    'updateButtonUrl'=>'Yii::app()->createUrl("/item/update", array("id"=>$data["id"]))',
    'deleteButtonUrl'=>null,
)
             * */
     /*       array(
                'htmlOptions' => array('nowrap'=>'nowrap'),
                'class'=>'booster.widgets.TbButtonColumn',
                'viewButtonUrl'=>'Yii::app()->createUrl("/users/view", array("id"=>$data["id"]))',
                'updateButtonUrl'=>'Yii::app()->createUrl("/users/update", array("id"=>$data["id"]))',
                'deleteButtonUrl'=>'Yii::app()->createUrl("/users/delete", array("id"=>$data["id"]))',
            )
        );*/

     //   $dataProvider=new CActiveDataProvider('Users', array(
           /* 'criteria'=>array(
                'condition'=>'status=1',
                'order'=>'create_time DESC',
                'with'=>array('author'),
            ),
            'countCriteria'=>array(
                'condition'=>'status=1',
                // 'order' and 'with' clauses have no meaning for the count query
            ),
            'pagination'=>array(
                'pageSize'=>20,
            ),*/
       // ));

        $this->render('index');//, array(/*"model"=>$model, "gridColumns"=>$gridColumns*/)
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
    {

        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}