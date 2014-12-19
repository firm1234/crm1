<?php
/* @var $this CallController */
/* @var $model Contact*/

$this->breadcrumbs=array(
	'Contacts'=>array('index'),
	'Create',
);
?>

<h3>Create</h3>

<div id="form-container">
<?php $this->renderPartial('_form', array(
    'model'=>$model,
)); ?>
</div><!-- form container -->

<div id="grid-container"><?php $this->actionGrid(); ?></div>
