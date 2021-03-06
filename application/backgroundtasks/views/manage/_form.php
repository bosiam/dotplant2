<?php

use app\backgroundtasks\models\Task;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\backgroundtasks\models\Task $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="tasks-form">

	<?= \app\widgets\Alert::widget([
		'id' => 'alert',
	]); ?>

	<?php $form = ActiveForm::begin(['id' => 'tasks-form']); ?>

		<?php if(!$model->isNewRecord): ?>
		<?= $form->field($model, 'id')->textInput(['disabled' => 'disabled']); ?>
		<?php endif; ?>

		<?= $form->field($model, 'type', ['template' => '{input}'])->input('hidden'); ?>

		<?= $model->isNewRecord ? $form->field($model, 'initiator', ['template' => '{input}'])->input('hidden') : $form->field($model->initiatorUser, 'username')->textInput(['disabled' => 'disabled']); ?>

		<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'action')->hint($model->attributeHints()['action'])->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'params')->hint($model->attributeHints()['params'])->textArea() ?>

		<?= $form->field($model, 'description')->textArea() ?>

		<?= $form->field($model, 'cron_expression')->textArea() ?>

		<?php if(!$model->isNewRecord): ?>
		<?= $form->field($model, 'ts')->textInput(['disabled' => 'disabled']) ?>
		<?php endif; ?>

		<?= $form->field($model, 'status')->dropDownList(Task::getStatuses(Task::TYPE_REPEAT)); ?>

		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
