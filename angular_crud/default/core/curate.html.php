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
<h2><?= $modelClassPluralWords ?></h2>
<?php if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')))): ?>

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

<div> <!-- Commented due to Handsontable rendering bug: ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved && <?= lcfirst($modelClassPlural) ?>.$promise.$$state.status !== 2" -->

    <p>Total count: {{ <?= lcfirst($modelClassPlural) ?>.$metadata.totalCount }}</p>
    <p>Current page: {{ <?= lcfirst($modelClassPlural) ?>.$metadata.currentPage }}</p>

    <pagination ng-change="pageChanged()" total-items="<?= lcfirst($modelClassPlural) ?>.$metadata.totalCount" ng-model="<?= lcfirst($modelClassPlural) ?>.$metadata.currentPage" items-per-page="<?= lcfirst($modelClassPlural) ?>.$metadata.perPage" num-pages="<?= lcfirst($modelClassPlural) ?>.$metadata.pageCount" class="pagination-sm" boundary-links="true" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>

    <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.add()" ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1" class="btn btn-primary btn-xs">Add new item</a>

    <!--contextMenu="['row_above', 'row_below', 'remove_row']"-->
    <hot-table
        settings="{}"
        currentRowClassName="'current-row'"
        currentColClassName="'current-column'"
        rowHeaders="false"
        colHeaders="true"
        contextMenu="false"
        persistentState="true"
        minSpareRows="0"
        manualRowMove="true"
        manualColumnMove="true"
        fixedColumnsLeft="0"
        manualColumnResize="true"
        manualRowResize="true"
        manualColumnResize="true"
        manualRowResize="true"
        afterSelectionEndByProp="<?= lcfirst($modelClassSingular) ?>Crud.handsontable.afterSelectionEndByPropCallback"
        datarows="<?= lcfirst($modelClassPlural) ?>"
        dataSchema="<?= lcfirst($modelClassSingular) ?>Resource.dataSchema"
        afterChange="<?= lcfirst($modelClassSingular) ?>Crud.handsontable.afterChange">

        <!--
        <hot-column data="_delete" title="'Delete'" type="'checkbox'" width="65" checkedTemplate="1"
                    uncheckedTemplate="null"></hot-column>
        -->

        <?php foreach ($model->flowSteps() as $step => $stepAttributes): ?>

        <!-- step: <?= $step ?> --><?php foreach ($stepAttributes as $attribute): ?>

        <!-- <?= $attribute ?> --><?php

            $prepend = $generator->prependActiveFieldForAttribute("hot-column." . $attribute, $model);
            $field = $generator->activeFieldForAttribute("hot-column." . $attribute, $model);
            $append = $generator->appendActiveFieldForAttribute("hot-column." . $attribute, $model);

            if ($prepend) {
                echo "\n" . $prepend . "";
            }
            if ($field) {
                echo "\n" . $field . "";
            }
            if ($append) {
                echo "\n" . $append . "";
            }

        endforeach;
        endforeach; ?>

    </hot-table>

</div>

<?php else: ?>

    [not a workflow-item]

<?php endif; ?>