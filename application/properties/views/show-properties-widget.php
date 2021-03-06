<?php
/**
 * @var \app\models\View $this
 * @var string $widget_id
 * @var \app\models\Submission $model
 * @var \kartik\widgets\ActiveForm $form
 */
?>

<div id="properties-widget widget-<?= $widget_id ?>">

<?php
    if (!empty($object_property_groups)) {
        foreach ($object_property_groups as $i => $opg) {
            $options = [
                'id' => 'pg-' . $opg->group->id,
                'class' => 'object-property-group',
            ];
            if ($i == 0) {
                \kartik\helpers\Html::addCssClass($options, 'active');
            }
            echo \kartik\helpers\Html::beginTag('div', $options);

            /** @var \app\models\Property[] $properties */
            $properties = app\models\Property::getForGroupId($opg->group->id);

            foreach ($properties as $prop) {
                $property_values = $model->getPropertyValuesByPropertyId($prop->id);
                echo $prop->handler($form, $model->getAbstractModel(), $property_values, 'frontend_render_view');
            }
            echo "</div>";
        }
    } else {
        echo 'Empty properties';
    }
?>

</div> <!-- /properties-widget -->
