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
<ul class="nav menu-filters"
    ng-if="(restrictUi.show<?= $modelClassSingular ?>Filters) && restrictUi.asPrivateBetaFeature() && $ctrl.renderInMenu">

    <li class="nav-title">Filters</li>

    <li>
        <label for="cf_<?= $modelClassSingular ?>_search">Free-text search</label>
        <input ng-model="$state.params.cf_<?= $modelClassSingular ?>_search" name="cf_<?= $modelClassSingular ?>_search"
               name="cf_<?= $modelClassSingular ?>_search"
               type="text"
               ng-model-options='{ debounce: 600 }' ng-change="$state.go($state.current.name, {'cf_<?= $modelClassSingular ?>_search': $state.params.cf_<?= $modelClassSingular ?>_search})"/>
    </li>
</ul>

<ul class="not-a-list"
    ng-if="(restrictUi.show<?= $modelClassSingular ?>Filters) && restrictUi.asPrivateBetaFeature() && !$ctrl.renderInMenu">

    <li>
        Free-text search
        <input ng-model="$state.params.cf_<?= $modelClassSingular ?>_search" name="cf_<?= $modelClassSingular ?>_search" type="text"
               ng-model-options='{ debounce: 600 }' ng-change="$state.go($state.current.name, {'cf_<?= $modelClassSingular ?>_search': cf_<?= $modelClassSingular ?>_search})"/>
    </li>
</ul>