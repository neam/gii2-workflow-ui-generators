<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

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

<div class="alert alert-info" ng-show="!<?= lcfirst($modelClassPlural) ?>.$refreshing && <?= lcfirst($modelClassPlural) ?>.$resolved === null">
    No active data environment to load <?= strtolower($modelClassPluralWords) ?> from...
</div>

<div class="alert alert-info" ng-show="<?= lcfirst($modelClassPlural) ?>.$refreshing">
    Loading <?= strtolower($modelClassPluralWords) ?>...
</div>

<div class="alert alert-warning"
     ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1 && <?= lcfirst($modelClassPlural) ?>.length == 0">
    <div ng-show="!<?= lcfirst($modelClassPlural) ?>.filtered()">
        You have no <?= strtolower($modelClassPluralWords) ?>.
    </div>
    <div ng-show="<?= lcfirst($modelClassPlural) ?>.filtered()">
        No <?= strtolower($modelClassPluralWords) ?> matched the current filters.
    </div>
</div>

<div class="alert alert-danger"
     ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 2">
    A problem was encountered when loading the <?= strtolower($modelClassPluralWords) ?>.
    <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.refresh()" class="btn btn-primary btn-xs">Click here to retry</a>
</div>
