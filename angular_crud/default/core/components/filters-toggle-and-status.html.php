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
<span>

    <a href="javascript:void(0)"
       ng-if="<?= lcfirst($modelClassPlural) ?>.$activated"
       ng-click="restrictUi.show<?= $modelClassSingular ?>Filters = !restrictUi.show<?= $modelClassSingular ?>Filters" class="label"
       ng-class="{'label-primary': !restrictUi.show<?= $modelClassSingular ?>Filters, 'label-warning': restrictUi.show<?= $modelClassSingular ?>Filters}"><i
            class="fa fa-filter"></i></a>

</span>
<span>

    <a href="javascript:void(0)"
       ng-if="$state.params.cf_<?= $modelClassSingular ?>_from_date"
       ui-state="$state.current.name"
       ui-state-params="{'cf_<?= $modelClassSingular ?>_from_date': null}"
       class="label"
    ><i class="fa fa-close"></i> From: {{ $state.params.cf_<?= $modelClassSingular ?>_from_date }}</a>

    <a href="javascript:void(0)"
       ng-if="$state.params.cf_<?= $modelClassSingular ?>_to_date"
       ui-state="$state.current.name"
       ui-state-params="{'cf_<?= $modelClassSingular ?>_to_date': null}"
       class="label"
    ><i class="fa fa-close"></i> To: {{ $state.params.cf_<?= $modelClassSingular ?>_to_date }}</a>

    <a href="javascript:void(0)"
       ng-if="$state.params.cf_<?= $modelClassSingular ?>_search"
       ui-state="$state.current.name"
       ui-state-params="{'cf_<?= $modelClassSingular ?>_search': null}"
       class="label"
    ><i class="fa fa-close"></i> Search: {{ $state.params.cf_<?= $modelClassSingular ?>_search }}</a>

    <a href="javascript:void(0)"
       ng-if="$state.params.cf_<?= $modelClassSingular ?>_foo_id"
       ui-state="$state.current.name"
       ui-state-params="{'cf_<?= $modelClassSingular ?>_foo_id': null}"
       class="label"
    ><i class="fa fa-close"></i> Specific foo</a>

</span>
