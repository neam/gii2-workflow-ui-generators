<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);
// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels

?>
<?php if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')))): ?>

<div ng-include="'crud/<?= lcfirst($modelClassSingularId) ?>/elements/loading-status.html'"></div>

<!--
<div ng-repeat="<?= lcfirst($modelClassSingular) ?> in <?= lcfirst($modelClassPlural) ?>">
    {{<?=lcfirst($modelClassSingular)?>.item_label}}
</div>
-->

<div ng-show="!<?= lcfirst($modelClassPlural) ?>.$refreshing" ng-if="<?= lcfirst($modelClassPlural) ?>.$resolved && <?= lcfirst($modelClassPlural) ?>.$promise.$$state.status !== 2">

    <!--<dna-collection-curation-widget template="" handsontableColumns="handsontableColumns" crud="crud" collection="collection"/>-->

    <div ng-show="<?= lcfirst($modelClassPlural) ?>.$metadata.pageCount > 1">
        <p>Total count: {{ <?= lcfirst($modelClassPlural) ?>.$metadata.totalCount }}</p>
        <p>Current page: {{ <?= lcfirst($modelClassPlural) ?>.$metadata.currentPage }}</p>
        <pagination ng-change="pageChanged()" total-items="<?= lcfirst($modelClassPlural) ?>.$metadata.totalCount" ng-model="<?= lcfirst($modelClassPlural) ?>.$metadata.currentPage" items-per-page="<?= lcfirst($modelClassPlural) ?>.$metadata.perPage" num-pages="<?= lcfirst($modelClassPlural) ?>.$metadata.pageCount" class="pagination-sm" boundary-links="true" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>
    </div>

    <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.add()" ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1" class="btn btn-primary btn-xs">Add new item</a>

    <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.refresh()" ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved" class="btn btn-primary btn-xs">Refresh</a>

    <simple-handsontable
        ng-if="<?= lcfirst($modelClassPlural) ?>.length > 0"
        settings="handsontableSettings"
        data="<?= lcfirst($modelClassPlural) ?>">
    </simple-handsontable>

</div>

<?php else: ?>

    [not a workflow-item]

<?php endif; ?>