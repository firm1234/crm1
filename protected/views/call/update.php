<?php
/* @var $this CallController */
/* @var $model Contact*/

$this->breadcrumbs=array(
    'Contacts'=>array('index'),
    'Update',
);
?>


<h3>Update <?php echo $model->id; ?></h3>

<?php $this->renderPartial('_form', array(
    'model'=>$model,
)); ?>