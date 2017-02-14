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
'use strict';

var module = angular.module('crud-<?= $modelClassSingularId ?>-components', [
    require('./services.js').default.name,
    require('./controllers.js').default.name,
]);

module
    .component('crud<?= $modelClassSingular ?>Curate', {
        template: require('./curate.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>SideMenu', {
        template: require('./side-menu.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>CompactList', {
        template: require('./compact-list.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>Form', {
        template: require('./form.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>FormTop', {
        template: require('./form.top.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>ElementsFormControls', {
        template: require('./elements/form-controls.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
    .component('crud<?= $modelClassSingular ?>ElementsLoadingStatus', {
        template: require('./elements/loading-status.html'),
        controller: 'list<?=Inflector::pluralize($modelClass)?>Controller'
    })
;

export default module;
