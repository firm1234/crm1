<?php

$form = $this->beginWidget(
    'booster.widgets.TbActiveForm',
    array(
        'htmlOptions' => array('class' => 'well'),
        'action' => $this->createUrl('call/grid')
    )
);

echo $form->dropDownListGroup($model, 'days',  array(

    'widgetOptions' => array(
        'data' => array(-1 => 'All', 0 => 'Today', 7 => 'Week', 30 => 'Month', 365 =>'Year'),
        'htmlOptions' => array(
            'style' => 'width: 100px',
            'ajax' => array(
                'type' => 'POST',
                'url' => $this->createUrl('call/grid'),
                'success' => 'function(data){$("#grid-container").html(data); reinstallWidgets()}',
            )
        ),
    )
));

$this->endWidget();
unset($form);


$dataProvider = $model->fromToday(Yii::app()->session['days'])->search();
$dataProvider->pagination->route = 'call/grid';

$this->widget('booster.widgets.TbExtendedGridView', array(
    'id'=>'contact-grid',
    'dataProvider'=>$dataProvider,
    //'beforeAjaxUpdate' => 'function(){alert("beforeAjaxUpdate");}',
    'afterAjaxUpdate' => "reinstallWidgets", //reload DatePicker
    'filter'=>$model,
    'fixedHeader' => true,
    'responsiveTable' => true,
    'template' => "{items}{pager}",
    'ajaxUrl' => array($dataProvider->pagination->route),
    'columns'=>array(
        array('name'=>'datetime',
            'filter'=> $this->widget('booster.widgets.TbDatePicker', array(
                'model' => $model,
                'attribute' => 'datetime',
                'htmlOptions' => array(
                    'size' => '10',         // textField size
                    'maxlength' => '10',    // textField maxlength
                ),
                'options' => array(
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                    'language' => 'ru',
                    'todayHighlight' => true,
                ),
            ), true)
        ),
        array('name'=>'clientName', 'header'=>'Name'),
        array('name'=>'clientPhone',
            'filter' =>$this->widget(
                'CMaskedTextField', array(
                    'model' => $model,
                    'attribute' => 'clientPhone',
                    'mask' => '(iii)iii-ii-ii',
                    'charMap' => array('i' => '[0-9]'),
                ), true ),/**/
        ),
        array('name'=>'comment'),
        array('name'=>'StudyFormString', 'header'=>'study forms', 'filter'=>false),
        array('name'=>'PurposeString', 'header'=>'purposes', 'filter'=>false),
        array('name'=>'ResultString', 'header'=>'results', 'filter'=>false),
        array(
            'htmlOptions' => array('nowrap' => 'nowrap'),
            'class' => 'booster.widgets.TbButtonColumn',
            'template' => '{update}{delete}'
        )
    ),
));

//reload DatePicker
Yii::app()->clientScript->registerScript('re-install-widgets', "
function reinstallWidgets(id, data) {
    jQuery('#Contact_datetime').datepicker({'format':'yyyy-mm-dd','autoclose':true,'language':'ru','todayHighlight':true});
    jQuery.mask.definitions={'i':'[0-9]'};
    jQuery('#Contact_clientPhone').mask('(iii)iii-ii-ii');
    alert('after');
    }
");
?>
