<?php

use yii\helpers\Inflector;

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
<i class="fa fa-delete" ng-if="!$root.activeDataEnvironment.available"></i>

<span ng-if="!$root.activeDataEnvironment.available">
    _
</span>

<i class="fa fa-exclamation" ng-if="!<?= lcfirst($modelClassPlural) ?>.$activated && !<?= lcfirst($modelClassPlural) ?>.$refreshing && <?= lcfirst($modelClassPlural) ?>.$resolved === null"></i>

<span ng-if="!<?= lcfirst($modelClassPlural) ?>.$activated && !<?= lcfirst($modelClassPlural) ?>.$refreshing && <?= lcfirst($modelClassPlural) ?>.$resolved === null">
    !
</span>

<i class="fa fa-circle-o-notch fa-spin" ng-if="<?= lcfirst($modelClassPlural) ?>.$refreshing"></i>

<span ng-if="<?= lcfirst($modelClassPlural) ?>.$activated && !<?= lcfirst($modelClassPlural) ?>.$refreshing && <?= lcfirst($modelClassPlural) ?>.$resolved">{{ <?= lcfirst($modelClassPlural) ?>.$metadata.totalCount }}</span>
