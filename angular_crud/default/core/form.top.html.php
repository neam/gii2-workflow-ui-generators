<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = get_class($model);
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);
// TODO: use item type choiceformat label for labels instead of inflector

?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>{{<?= lcfirst($modelClassSingular) ?>.item_label}}</h2>
        <div ng-include="'crud/<?= $modelClassSingularId ?>/elements/form-controls.html'"></div>
        <!-- Hint start -->
        <?= \ItemTypes::hint($modelClassSingular) . "\n" ?>
        <!-- Hint stop -->
    </div>
</div>