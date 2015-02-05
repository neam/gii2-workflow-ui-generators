<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$translatableAttributes = $generator->getModel()->getTranslatableAttributes();

echo "<?php\n";
?>
/**
* @var <?= ltrim($generator->controllerClass, '\\') ?>|WorkflowUiControllerTrait $this
* @var <?= ltrim($generator->modelClass, '\\') ?>|ItemTrait $model
* @var TbActiveForm $form
*/
?>

<?="<?php";?> if ($this->actionUsesEditWorkflow()): ?>

<?php
var_dump($attributes);
?>

<?="<?php";?> endif; ?>

<?php foreach ($attributes as $attribute) {



//    $column = $generator->getTableSchema()->columns[$attribute];
/*
    $prepend = $generator->prependActiveField($column, $model);
    $field = $generator->activeField($column, $model);
    $append = $generator->appendActiveField($column, $model);

    if ($prepend) {
        echo "\n\t\t\t<?= " . $prepend . " ?>";
    }
    if ($field) {
        echo "\n\t\t\t<?= " . $field . " ?>";
    }
    if ($append) {
        echo "\n\t\t\t<?= " . $append . " ?>";
    }
    */
} ?>
