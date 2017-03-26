<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

$model = $generator->getModel();

$modelClass = $generator->modelClass;
$modelClassSingular = $modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$modelClassPluralId = Inflector::camel2id($modelClassPlural);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);

// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", $modelClass);
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels
$labelSingular = $unprefixedModelClassSingularWords;
$labelPlural = $unprefixedModelClassPluralWords;

?>
<div ng-if="!<?= lcfirst($modelClassSingular) ?>.$resolved">
    <div class="alert alert-info">Select a transaction to begin</div>
</div>
<div ng-if="<?= lcfirst($modelClassSingular) ?>.$resolved && <?= lcfirst($modelClassSingular) ?>.$promise.$$state.status !== 2">
    <div class="row wrapper border-bottom white-bg page-heading ">
        <h1 class="pull-left">{{<?= lcfirst($modelClassSingular) ?>.item_label}}</h1>
        <div class="pull-right top-buttons">

            <crud-<?= $modelClassSingularId ?>-elements-current-classification-form-controls <?= $modelClassSingularId ?>-form="$ctrl.<?= lcfirst($modelClassSingular) ?>Form"></crud-<?= $modelClassSingularId ?>-elements-current-classification-form-controls>

        </div>
    </div>
    <div data-ui-view="currentClassificationStep"></div>

    <div class="row wrapper border-bottom white-bg page-heading ">
        <h1 class="pull-left">{{<?= lcfirst($modelClassSingular) ?>.item_label}}</h1>
        <div class="pull-right top-buttons">

            <crud-<?= $modelClassSingularId ?>-elements-current-classification-form-controls <?= $modelClassSingularId ?>-form="$ctrl.<?= lcfirst($modelClassSingular) ?>Form"></crud-<?= $modelClassSingularId ?>-elements-current-classification-form-controls>

        </div>
    </div>
</div>
