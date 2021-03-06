<?php

use kartik\dynagrid\DynaGrid;
use kartik\icons\Icon;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= app\widgets\Alert::widget([
    'id' => 'alert',
]); ?>


<?php
$this->beginBlock('add-button');
?>
        <a href="<?= Url::toRoute('/backend/product/edit') ?>" class="btn btn-success">
            <?= Icon::show('plus') ?>
            <?= Yii::t('app', 'Add') ?>
        </a>
<?php
$this->endBlock();
?>

<div class="row">
    <div class="col-md-4">
    <?=
        app\backend\widgets\JSTree::widget([
            'model' => new app\models\Category,
            'routes' => [
                'getTree' => [Url::toRoute('getTree')],
                'open' => [Url::toRoute('index')],
                'edit' => ['/backend/category/edit'],
                'delete' => ['/backend/category/delete'],
                'create' => ['/backend/category/edit'],
            ],
        ]);
?>
    </div>
    <div class="col-md-8" id="jstree-more">
<?=
    DynaGrid::widget([
        'options' => [
            'id' => 'Product-grid',
            'storage' => DynaGrid::TYPE_SESSION
        ],
        'columns' => [
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'id',
            ],
            [
                'class' => 'app\backend\columns\TextWrapper',
                'attribute' => 'name',
                'callback_wrapper' => function($content, $model, $key, $index, $parent) {
                    if (1 === $model->is_deleted) {
                        $content = '<div class="is_deleted"><span class="fa fa-trash-o"></span>'.$content.'</div>';
                    }

                    return $content;
                }
            ],
            'slug',
            [
                'class' => 'app\backend\columns\BooleanStatus',
                'attribute' => 'active',
            ],
            'price',
            'old_price',
            [
                'class' => 'app\backend\components\ActionColumn',
                'buttons' => function($model, $key, $index, $parent) {
                    if (1 === $model->is_deleted) {
                        return [
                            [
                                'url' => 'edit',
                                'icon' => 'pencil',
                                'class' => 'btn-primary',
                                'label' => 'Edit',
                            ],
                            [
                                'url' => 'restore',
                                'icon' => 'refresh',
                                'class' => 'btn-success',
                                'label' => 'Restore',
                            ],
                            [
                                'url' => 'delete',
                                'icon' => 'trash-o',
                                'class' => 'btn-danger',
                                'label' => 'Delete',
                            ],
                        ];
                    }
                    return null;
                }
            ],
        ],
        'theme' => 'panel-default',
        'gridOptions'=>[
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'hover'=>true,
            'panel'=>[
                'heading'=>'<h3 class="panel-title">'.$this->title.'</h3>',
                'after' => $this->blocks['add-button'],
            ],
        ]
    ]);
?>
    </div>
</div>
