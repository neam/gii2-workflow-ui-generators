<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
// TODO: use item type choiceformat label for labels instead of inflector

?>
<h2><?= $modelClassPluralWords ?></h2>

<!-- Hint start -->
<?=\ItemTypes::hint($modelClassSingular) . "\n"?>
<!-- Hint stop -->

<div class="alert alert-info" ng-show="!<?= lcfirst($modelClassPlural) ?>.$resolved">
    Loading <?= strtolower($modelClassPluralWords) ?>...
</div>

<div class="alert alert-info" ng-show="<?= lcfirst($modelClassPlural) ?>.$refreshing">
    Refreshing <?= strtolower($modelClassPluralWords) ?>...
</div>

<div class="alert alert-warning"
     ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1 && <?= lcfirst($modelClassPlural) ?>.length == 0">
    <div ng-show="<?= lcfirst($modelClassPlural) ?>.filtered()">
        You have no <?= strtolower($modelClassPluralWords) ?>.
    </div>
    <div ng-show="!<?= lcfirst($modelClassPlural) ?>.filtered()">
        No <?= strtolower($modelClassPluralWords) ?> matched the current filters.
    </div>
</div>

<div class="alert alert-danger"
     ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 2">
    A problem was encountered when loading the <?= strtolower($modelClassPluralWords) ?>. Please re-load the page.
</div>

<!--
<div ng-repeat="<?= lcfirst($modelClassSingular) ?> in <?= lcfirst($modelClassPlural) ?>">
    {{<?=lcfirst($modelClassSingular)?>.item_label}}
</div>
-->

<div ng-if="<?= lcfirst($modelClassPlural) ?>.$resolved && <?= lcfirst($modelClassPlural) ?>.$promise.$$state.status !== 2">

    <p>Total count: {{ <?= lcfirst($modelClassPlural) ?>.$metadata.totalCount }}</p>
    <p>Current page: {{ <?= lcfirst($modelClassPlural) ?>.$metadata.currentPage }}</p>

    <pagination ng-change="pageChanged()" total-items="<?= lcfirst($modelClassPlural) ?>.$metadata.totalCount" ng-model="<?= lcfirst($modelClassPlural) ?>.$metadata.currentPage" items-per-page="<?= lcfirst($modelClassPlural) ?>.$metadata.perPage" num-pages="<?= lcfirst($modelClassPlural) ?>.$metadata.pageCount" class="pagination-sm" boundary-links="true" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>

    <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.add()" ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1" class="btn btn-primary btn-xs">Add new item</a>

    <simple-handsontable
        settings="handsontableSettings"
        data="<?= lcfirst($modelClassPlural) ?>">
    </simple-handsontable>

</div>

