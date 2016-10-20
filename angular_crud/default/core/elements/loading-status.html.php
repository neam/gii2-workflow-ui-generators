<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = $generator->modelClass;
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);
// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels

// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", $generator->modelClass);
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

?>

<div class="alert alert-info" ng-show="!activeDataEnvironment.available">
    No active data environment to load <?= strtolower($unprefixedModelClassPluralWords) ?> from...
</div>

<div class="alert alert-info" ng-show="!<?= lcfirst($modelClassPlural) ?>.$activated && !<?= lcfirst($modelClassPlural) ?>.$refreshing && <?= lcfirst($modelClassPlural) ?>.$resolved === null">
    <?= ucfirst(strtolower($unprefixedModelClassPluralWords)) ?> not available yet...
    <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.$activate()" class="btn btn-primary btn-xs">Click here to activate</a>
</div>

<div class="alert alert-info" ng-show="<?= lcfirst($modelClassPlural) ?>.$refreshing">
    Loading <?= strtolower($unprefixedModelClassPluralWords) ?>...
</div>

<div class="alert alert-warning"
     ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1 && <?= lcfirst($modelClassPlural) ?>.length == 0">
    <div ng-show="!<?= lcfirst($modelClassPlural) ?>.filtered()">
        You have no <?= strtolower($unprefixedModelClassPluralWords) ?>.
    </div>
    <div ng-show="<?= lcfirst($modelClassPlural) ?>.filtered()">
        No <?= strtolower($unprefixedModelClassPluralWords) ?> found to display.
    </div>
</div>

<div class="alert alert-danger"
     ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 2">
    A problem was encountered when loading the <?= strtolower($unprefixedModelClassPluralWords) ?>.
    <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.refresh()" class="btn btn-primary btn-xs">Click here to retry</a>
</div>
