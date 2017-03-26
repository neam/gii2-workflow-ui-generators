<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClass = $generator->modelClass;
$modelClassSingular = $modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);


// TODO: handle prefixes through config
$unprefixedModelClassSingular = str_replace(["Clerk", "Neamtime"], "", $modelClass);
$unprefixedModelClassSingularId = Inflector::camel2id($unprefixedModelClassSingular);
$unprefixedModelClassSingularWords = Inflector::camel2words($unprefixedModelClassSingular);
$unprefixedModelClassPluralWords = Inflector::pluralize($unprefixedModelClassSingularWords);
$unprefixedModelClassPlural = Inflector::camelize($unprefixedModelClassPluralWords);
$unprefixedModelClassPluralId = Inflector::camel2id($unprefixedModelClassPlural);

// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);

if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')))): ?>
<div ng-controller="GeneralModalController">
    <div class="modal-header">
        <h3 class="modal-title">List of <?= strtolower($unprefixedModelClassPluralWords) ?></h3>
    </div>
    <div class="modal-body">

        <crud-<?= lcfirst($modelClassSingularId) ?>-curate></crud-<?= lcfirst($modelClassSingularId) ?>-curate>

    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" ng-click="ok()">Close</button>
        <!--<button class="btn btn-warning" ng-click="cancel()">Cancel</button>-->
    </div>
</div>
<?php else: ?>

    [not a workflow-item]

<?php endif;
