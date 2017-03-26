<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

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
<div ng-if="<?= lcfirst($modelClassPlural) ?>.$promise.$$state.status === 1 && clerkTransactionRows.length > 0">
    <form id="current-classification-form" name="<?= lcfirst($modelClassSingular) ?>Form" ng-submit="saveOrRefresh(<?= lcfirst($modelClassSingular) ?>Form)"
          novalidate>

        <!-- Wrapper-->
        <div class="<?= $modelClassSingularId ?> current-classification">
            <div class="animated fadeIn">

                <crud-<?= $modelClassSingularId ?>-current-classification form="<?= lcfirst($modelClassSingular) ?>Form"></crud-<?= $modelClassSingularId ?>-current-classification>

            </div>
        </div>
        <!-- End wrapper-->

    </form>
</div>
