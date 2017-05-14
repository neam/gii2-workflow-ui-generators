<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

$model = $generator->getModel();

$modelClassSingular = $generator->modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$modelClassPluralId = Inflector::camel2id($modelClassPlural);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);

// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", $generator->modelClass);
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels
$labelSingular = $unprefixedModelClassSingularWords;
$labelPlural = $unprefixedModelClassPluralWords;

?>
<ul class="nav" role="navigation">

    <!-- <?= $labelSingular ?> editing -->

    <li class="nav-title">
        <span ng-show="!edit<?= $modelClassSingular ?>Controller.$scope.<?= lcfirst($modelClassSingular) ?>.$resolved">Loading
            <?= strtolower($labelSingular) ?>...</span>
        {{edit<?= $modelClassSingular ?>Controller.$scope.<?= lcfirst($modelClassSingular) ?>.item_label}}
    </li>

    <?php
    if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')))):
        $stepCaptions = $model->flowStepCaptions();
        $flowSteps = $model->flowSteps();
        $flowStepReference = array_keys($flowSteps);
        $firstStepReference = reset($flowStepReference);

        foreach ($flowSteps as $stepReference => $stepAttributes):

            // Determine level of step
            $stepHierarchy = explode(".", $stepReference);
            $step = end($stepHierarchy);
            $stepCaption = !empty($stepCaptions[$step]) ? $stepCaptions[$step] : ucfirst($step);

            switch (count($stepHierarchy)):

                case 1000: ?>

                    <?php break;
                default: ?>

    <li ui-sref-active="active" ng-show="activeDataEnvironment.available">
        <a ui-sref="root.api-endpoints.existing.<?= $modelClassPluralId ?>.existing.edit.<?= $stepReference ?>({dataEnvironment: $root.activeDataEnvironment.slug, active<?= $modelClassSingular ?>Id: $state.params.active<?= $modelClassSingular ?>Id})"
           href="#"><i class="fa fa-check-circle"></i> <span class="nav-label"><?= Html::encode($stepCaption) ?></span></a>
    </li>

                <?php endswitch; ?>
        <?php endforeach; ?>

    <?php else: ?>

        // [not a workflow-item]

    <?php endif; ?></ul>
