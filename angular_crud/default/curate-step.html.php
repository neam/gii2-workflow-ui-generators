<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

$modelClassSingular = $generator->modelClass;
$modelClassSingularId = Inflector::camel2id($modelClassSingular);
$modelClassSingularWords = Inflector::camel2words($modelClassSingular);
$modelClassPluralWords = Inflector::pluralize($modelClassSingularWords);
$modelClassPlural = Inflector::camelize($modelClassPluralWords);

?>
<!--
Template for <?= ltrim($generator->modelClass, '\\') ?> model, curate-step "<?=$step?>"
-->

<h2><?= $modelClassPluralWords ?></h2>

<!-- Hint start -->
<?=\ItemTypes::hint($modelClassSingular) . "\n"?>
<!-- Hint stop -->

<div ng-controller="list<?= $modelClassPlural ?>Controller" ng-include="'crud/<?= $modelClassSingularId ?>/curate.html'"></div>
