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
<ul class="nav menu-sortings"
    ng-if="(restrictUi.show<?= $modelClassSingular ?>Sortings) && restrictUi.asPrivateBetaFeature() && $ctrl.renderInMenu">

    <li class="nav-title">Sortings</li>

</ul>

<pre ng-if="restrictUi.restrictUi.byUserType(restrictUi.userTypes.DEVELOPER)">
stateParamSyncedSortings: {{ stateParamSyncedSortings | json }}
sortings: {{ sortings | json }}
</pre>

<ul class="nav menu-sortings" ui-sortable="sortingOptions" ng-model="sortings"
    ng-if="(restrictUi.show<?= $modelClassSingular ?>Sortings) && restrictUi.asPrivateBetaFeature() && $ctrl.renderInMenu">

    <li ng-repeat="sorting in sortings">

        <label>{{ sorting.label }}</label>
        <!--
        <button
                ng-if="!restrictUi.show<?= $modelClassSingular ?>AccountByAccount"
                ng-click="restrictUi.show<?= $modelClassSingular ?>AccountByAccount = true" class="btn btn-sm btn-primary"><i
                class="fa fa-list-alt"></i> Alphabetical
        </button>
        <button href="javascript:void(0)"
                ng-if="restrictUi.show<?= $modelClassSingular ?>AccountByAccount"
                ng-click="restrictUi.show<?= $modelClassSingular ?>AccountByAccount = false" class="btn btn-sm btn-warning"><i
                class="fa fa-list-alt"></i> Reverse
        </button>
        -->

        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 action-button" style="">
                <action-list-item-cta data-action="{
                    item: sorting,
                    booleanProperty: 'reverse',
                    buttonClass: 'btn-warning',
                    iconClass: sorting.iconClass,
                    caption: '',
                    truthy: {
                        iconClass: sorting.reverseIconClass,
                        buttonClass: 'btn-warning',
                        caption: '',
                    },
                }"></action-list-item-cta>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 action-button sorting-handle" style="">
                <span class="btn btn-circle btn-lg btn-default sorting-handle"><i class="fa fa-arrows"></i></span>
            </div>
        </div>
    </li>

</ul>

<ul class="not-a-list" ng-if="(restrictUi.show<?= $modelClassSingular ?>Sortings) && restrictUi.asPrivateBetaFeature() && !$ctrl.renderInMenu">
    TODO
</ul>