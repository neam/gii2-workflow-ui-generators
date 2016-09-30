<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = $generator->modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
$labelSingular = ItemTypes::label($modelClassSingular, 1);
$labelPlural = ItemTypes::label($modelClassSingular, 2);
$labelNone = ItemTypes::label($modelClassSingular, 2);
// TODO: fix choiceformat interpretation in yii2 and use item type choiceformat label for labels instead of inflector-created labels

if (in_array($modelClassSingular, array_keys(\ItemTypes::where('is_workflow_item')))): ?>
<div ng-controller="GeneralModalController">
    <div class="modal-header">
        <h3 class="modal-title">List of <?= strtolower($modelClassPluralWords) ?></h3>
    </div>
    <div class="modal-body">

        <div ng-controller="list<?= $modelClassPlural ?>Controller" ng-include="'crud/<?= lcfirst($modelClassSingularId) ?>/curate.html'"></div>

    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" ng-click="ok()">Close</button>
        <!--<button class="btn btn-warning" ng-click="cancel()">Cancel</button>-->
    </div>
</div>
<?php else: ?>

    [not a workflow-item]

<?php endif;
