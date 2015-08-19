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

<div class="alert alert-warning"
     ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved && <?= lcfirst($modelClassPlural) ?>.$promise.$$state.status !== 2 && <?= lcfirst($modelClassPlural) ?>.length == 0">
    You have no <?= lcfirst($modelClassPluralWords) ?>.
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

<div ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved && <?= lcfirst($modelClassPlural) ?>.$promise.$$state.status !== 2">

    <a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.add()" ng-show="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1" class="btn btn-primary btn-xs">Add new item</a>

    <!--contextMenu="['row_above', 'row_below', 'remove_row']"-->
    <hot-table
        settings="{}"
        currentRowClassName="'current-row'"
        currentColumnClassName="'current-column'"
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
        datarows="<?= lcfirst($modelClassPlural) ?>"
        dataSchema="<?= lcfirst($modelClassSingular) ?>Resource.dataSchema"
        afterChange="<?= lcfirst($modelClassSingular) ?>Crud.handsontable.afterChange">

        <!--
        <hot-column data="_delete" title="'Delete'" type="'checkbox'" width="65" checkedTemplate="1"
                    uncheckedTemplate="null"></hot-column>
        -->

        <?php foreach ($model->itemTypeAttributes() as $attribute => $attributeInfo): ?>

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

        endforeach; ?>

    </hot-table>

</div>

