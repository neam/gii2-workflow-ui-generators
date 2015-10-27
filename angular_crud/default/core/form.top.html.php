<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
// TODO: use item type choiceformat label for labels instead of inflector

?>
<div class="row wrapper border-bottom white-bg page-heading ">
    <h1 class="pull-left">{{$state.current.data.stepCaption}}</h1>
    <div class="pull-right top-buttons" ng-include="'crud/<?= $modelClassSingularId ?>/elements/form-controls.html'"></div>
</div>
