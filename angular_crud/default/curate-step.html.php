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

<h2>
    <?= $modelClassPluralWords ?>

    <span class="label label-primary"> <crud-<?=$modelClassSingularId?>-count></crud-<?=$modelClassSingularId?>-count> </span>
    &nbsp;
    <crud-clerk-ledger-regenerate-ledger-button></crud-clerk-ledger-regenerate-ledger-button>

    <crud-<?=$modelClassSingularId?>-filters-toggle-and-status></crud-<?=$modelClassSingularId?>-filters-toggle-and-status>

    <crud-<?=$modelClassSingularId?>-sortings-toggle-and-status></crud-<?=$modelClassSingularId?>-sortings-toggle-and-status>

    <crud-<?=$modelClassSingularId?>-groupings-toggle-and-status></crud-<?=$modelClassSingularId?>-groupings-toggle-and-status>

    <crud-<?=$modelClassSingularId?>-import-button></crud-<?=$modelClassSingularId?>-import-button>

</h2>

<!-- Hint start -->
<?=\ItemTypes::hint($modelClassSingular) . "\n"?>
<!-- Hint stop -->

<crud-<?= lcfirst($modelClassSingularId) ?>-curate></crud-<?= lcfirst($modelClassSingularId) ?>-curate>
