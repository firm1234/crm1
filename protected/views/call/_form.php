<?php
/* @var $this VisitController */
/* @var $model Client */
/* @var $form CActiveForm */
?>


<?php
/** @var TbActiveForm $form */
$form = $this->beginWidget(
    'booster.widgets.TbActiveForm',
    array(
        'id' => 'verticalForm',
        'htmlOptions' => array('class' => 'well'),
        'action' => $this->createUrl(Yii::app()->session['action'])
    )
);

?>
<div style="width:500px">
    <?php echo $form->errorSummary($model); ?>
    <?php
    echo $form->textFieldGroup($model->client, 'name', array(
        'label'=>false,
    ));
    echo $form->maskedTextFieldGroup($model->client, 'phone', array(
        'label'=> false,
        'name' =>'phone',
        'widgetOptions'=>array(
            'mask' => '(iii)iii-ii-ii',
            'charMap' => array('i' => '[0-9]'),
            'htmlOptions'=>array(
                'class'=>'span5',
                'maxlength'=>17,
                'placeholder'=>'Phone',
                'ajax'=>array(
                    'type'=>'POST', //request type
                    'url'=>$this->createUrl('call/UpdateAjax'), // url to call controller action
                    'beforeSend'=> 'function(jqXHR,setting){
                        if($("#Client_name").val() != ""){
                            return false;
                        }
                        else{
                            $("input").attr("disabled",true);
                        }
                    }',
                    'success'=>' function(data) { $("#form-container").html(data); $("input").attr("disabled",false);}',// function to call onsuccess
                ),/**/
            ),

        )));
    echo $form->maskedTextFieldGroup($model->client, 'phone2', array(
        'label'=> false,
        'name' =>'phone',

        //'class'=>'well',
        'widgetOptions'=>array(
            'mask' => '(iii)iii-ii-ii',
            'charMap' => array('i' => '[0-9]'),
            'htmlOptions'=>array('class'=>'span5','maxlength'=>17,'placeholder'=>'Phone2',),

        )));
    echo $form->maskedTextFieldGroup($model->client, 'phone3', array(
        'label'=> false,
        'name' =>'phone',

        //'class'=>'well',
        'widgetOptions'=>array(
            'mask' => '(iii)iii-ii-ii',
            'charMap' => array('i' => '[0-9]'),
            'htmlOptions'=>array('class'=>'span5','maxlength'=>17,'placeholder'=>'Phone3',),

        )));

    echo $form->textFieldGroup($model, 'comment', array('label'=>false));

    echo $form->checkboxListGroup(
        $model,
        'studyFormArray',
        array(
            'label' => false,
            'widgetOptions' => array(
                'data' => CHtml::listData(StudyForm::model()->findAll("parent_id IS NULL"), 'id', 'name')
            ),
        )
    );/**/

    echo $form->checkboxListGroup(
        $model,
        'purposeArray',
        array(
            'label' => false,
            'widgetOptions' => array(
                'data' => ViewPurpose::checkBoxList(Yii::app()->session['tab']),
            ),
        )
    );

    echo $form->checkboxListGroup(
        $model,
        'resultArray',
        array(
            'label' => false,
            'widgetOptions' => array(
                'data' => ViewResult::checkBoxList(Yii::app()->session['tab'])
            ),
        )
    );/**/

    /*echo $form->radioButtonListGroup(
        $client,
        'recall',
        array(
            'widgetOptions' => array(
                'data' => array(
                    'Option one is this and that - be sure to include why it\'s great',
                    'Option two can is something else and selecting it will deselect option one',
                )
            )
        )
    );/**/


    ?>
</div>
<?php
$this->widget(
    'booster.widgets.TbButton',
    array(
        'buttonType' => 'submit',
        'context' => 'primary',
        'label' => 'Сохранить'
    )
);

$this->endWidget();
unset($form);
?>


