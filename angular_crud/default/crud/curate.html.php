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

<p>TODO INCLUDE ITEM TYPE HINT HERE</p>

<div class="alert alert-warning"
     ng-show="<?= lcfirst($modelClassPlural) ?>.$resolved && <?= lcfirst($modelClassPlural) ?>.length == 0">
    You have no <?= $modelClassPluralWords ?>.
</div>

<!--
<div ng-repeat="<?= lcfirst($modelClassSingular) ?> in <?= lcfirst($modelClassPlural) ?>">
    {{<?=lcfirst($modelClassSingular)?>.item_label}}
</div>
-->

<a href="javascript:void(0)" ng-click="<?= lcfirst($modelClassPlural) ?>.addPlaceholder()" class="btn btn-primary btn-xs">Add new item</a>

<!--contextMenu="['row_above', 'row_below', 'remove_row']"-->
<hot-table
    settings="{manualRowMove: true, manualColumnMove: true, fixedColumnsLeft: 0, manualColumnResize: true, manualRowResize: true}"
    currentRowClassName="'current-row'"
    currentColumnClassName="'current-column'"
    rowHeaders="false"
    colHeaders="true"
    contextMenu="false"
    persistentState="true"
    minSpareRows="0"
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

<?php else: ?>

    [not a workflow-item]

<?php endif; ?>