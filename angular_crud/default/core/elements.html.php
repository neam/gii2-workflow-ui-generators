<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);
// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels

?>
<?php
if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')))):
    $stepCaptions = $model->flowStepCaptions();
    ?>

    <div class="panel blank-panel ui-tab">

        <div class="panel-body">
            <tabset>
                <?php foreach ($model->flowSteps() as $step => $stepAttributes): ?>

                    <!-- step: <?= $step ?> -->
                    <tab heading="<?= Html::encode(!empty($stepCaptions[$step]) ? $stepCaptions[$step] : ucfirst($step)) ?>" ng-show="showStep('<?= $step ?>')">
                        <div ng-include="'crud/<?= lcfirst($modelClassSingular) ?>/steps/<?= $step ?>.html'"></div>
                    </tab>

                <?php endforeach; ?>
            </tabset>
        </div>

    </div>

<?php else: ?>

    [not a workflow-item]

<?php endif; ?>