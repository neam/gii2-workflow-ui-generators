<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$model = $generator->getModel();

echo "<?php\n";
?>
/**
* @var <?= ltrim($generator->controllerClass, '\\') ?>|WorkflowUiControllerTrait $this
* @var <?= ltrim($generator->modelClass, '\\') ?>|ItemTrait $model
* @var TbActiveForm|WorkflowUiTbActiveFormTrait $form
*/
?>
<?php foreach ($translatableAttributes as $attribute => $sourceLanguageContentAttribute): ?>


<?= "<?php"; ?> // <?= $attribute ?> ?>
<?php

    $prepend = $generator->prependActiveFieldForAttribute($sourceLanguageContentAttribute, $model);
    $field = $generator->activeFieldForAttribute($sourceLanguageContentAttribute, $model);
    $append = $generator->appendActiveFieldForAttribute($sourceLanguageContentAttribute, $model);

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

